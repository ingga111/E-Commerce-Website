<?php

namespace App\Http\Controllers;

use App\Services\Order\OrderService;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function __construct(
        protected PaymentGatewayInterface $paymentGateway,
        protected OrderService $orderService
    ) {}

    /**
     * Handle incoming webhook notification from Payment Gateway (Tripay/Midtrans).
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info('Payment Webhook Received: '.$request->getContent());

        // 1. Signature Verification to prevent spoofing
        if (! $this->paymentGateway->validateCallback($request)) {
            Log::warning('Invalid Webhook Signature Attempt Detected', [
                'ip' => $request->ip(),
                'headers' => $request->headers->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature.',
            ], 403);
        }

        try {
            // 2. Parse callback payload
            $parsed = $this->paymentGateway->parseCallback($request);
            $merchantRef = $parsed['merchant_ref'];
            $status = $parsed['status'];

            if (empty($merchantRef)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing merchant reference.',
                ], 400);
            }

            // 3. Process according to status
            if ($status === 'paid') {
                $this->orderService->processPaymentSuccess($merchantRef, $parsed['raw']);
            } elseif (in_array($status, ['expired', 'failed'], true)) {
                $this->orderService->processPaymentFailed($merchantRef, $status, $parsed['raw']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook callback processed successfully.',
            ]);
        } catch (Exception $e) {
            Log::error('Error processing payment callback: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error while processing webhook.',
            ], 500);
        }
    }
}

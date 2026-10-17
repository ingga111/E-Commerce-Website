<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayPaymentService implements PaymentGatewayInterface
{
    protected string $apiKey;

    protected string $privateKey;

    protected string $merchantCode;

    protected string $baseUrl;

    protected int $expiryHours;

    public function __construct()
    {
        $this->apiKey = (string) config('tripay.api_key');
        $this->privateKey = (string) config('tripay.private_key');
        $this->merchantCode = (string) config('tripay.merchant_code');

        $mode = config('tripay.mode', 'sandbox');
        $this->baseUrl = config("tripay.urls.{$mode}", 'https://tripay.co.id/api-sandbox');
        $this->expiryHours = (int) config('tripay.expiry_hours', 24);
    }

    /**
     * Get list of payment channels from Tripay.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPaymentChannels(): array
    {
        // 1. Prioritize dynamic Payment Methods managed by Admin in Database
        $dbMethods = PaymentMethod::active()->get();
        if ($dbMethods->isNotEmpty()) {
            return $dbMethods->map(fn ($m) => [
                'group' => $m->group,
                'code' => $m->code,
                'name' => $m->name,
                'type' => 'direct',
                'fee_customer' => [
                    'flat' => (float) $m->fee_flat,
                    'percent' => (float) $m->fee_percent,
                ],
                'active' => true,
            ])->toArray();
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(10)
                ->get("{$this->baseUrl}/merchant/payment-channel");

            if ($response->successful() && isset($response->json()['data'])) {
                return $response->json()['data'];
            }
        } catch (Exception $e) {
            Log::warning('Tripay getPaymentChannels failed, using fallback list: '.$e->getMessage());
        }

        // Standard Indonesian digital channels fallback
        return [
            [
                'group' => 'E-Wallet & QRIS',
                'code' => 'QRIS',
                'name' => 'QRIS (Semua E-Wallet & Bank)',
                'type' => 'direct',
                'fee_merchant' => ['flat' => 750, 'percent' => 0.7],
                'fee_customer' => ['flat' => 750, 'percent' => 0],
                'active' => true,
            ],
            [
                'group' => 'Virtual Account',
                'code' => 'BCAVA',
                'name' => 'BCA Virtual Account',
                'type' => 'direct',
                'fee_merchant' => ['flat' => 4000, 'percent' => 0],
                'fee_customer' => ['flat' => 4000, 'percent' => 0],
                'active' => true,
            ],
            [
                'group' => 'Virtual Account',
                'code' => 'BRIVA',
                'name' => 'BRI Virtual Account',
                'type' => 'direct',
                'fee_merchant' => ['flat' => 3500, 'percent' => 0],
                'fee_customer' => ['flat' => 3500, 'percent' => 0],
                'active' => true,
            ],
            [
                'group' => 'Virtual Account',
                'code' => 'MANDIRIVA',
                'name' => 'Mandiri Virtual Account',
                'type' => 'direct',
                'fee_merchant' => ['flat' => 3500, 'percent' => 0],
                'fee_customer' => ['flat' => 3500, 'percent' => 0],
                'active' => true,
            ],
            [
                'group' => 'E-Wallet',
                'code' => 'OVO',
                'name' => 'OVO',
                'type' => 'direct',
                'fee_merchant' => ['flat' => 0, 'percent' => 1.5],
                'fee_customer' => ['flat' => 0, 'percent' => 1.5],
                'active' => true,
            ],
        ];
    }

    /**
     * Create closed transaction on Tripay.
     *
     * @return array<string, mixed>
     */
    public function createTransaction(Order $order, string $paymentMethod): array
    {
        $amount = (int) round((float) $order->total_amount);
        $signature = hash_hmac('sha256', $this->merchantCode.$order->order_number.$amount, $this->privateKey);
        $expiryTimestamp = now()->addHours($this->expiryHours)->timestamp;

        $orderItems = $order->items->map(function ($item) {
            return [
                'sku' => (string) ($item->variant->sku ?? 'ITEM-'.$item->id),
                'name' => "{$item->product_name} - {$item->variant_name}",
                'price' => (int) round((float) $item->price),
                'quantity' => (int) $item->quantity,
            ];
        })->toArray();

        $payload = [
            'method' => $paymentMethod,
            'merchant_ref' => $order->order_number,
            'amount' => $amount,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'order_items' => $orderItems,
            'callback_url' => route('payments.webhook'),
            'return_url' => route('orders.show', $order->order_number),
            'expired_time' => $expiryTimestamp,
            'signature' => $signature,
        ];

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(15)
                ->post("{$this->baseUrl}/transaction/create", $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['success']) && $result['success'] === true) {
                $data = $result['data'];

                return [
                    'success' => true,
                    'reference' => $data['reference'] ?? null,
                    'merchant_ref' => $order->order_number,
                    'payment_method' => $paymentMethod,
                    'payment_name' => $data['payment_name'] ?? $paymentMethod,
                    'pay_code' => $data['pay_code'] ?? null,
                    'qr_url' => $data['qr_url'] ?? null,
                    'checkout_url' => $data['checkout_url'] ?? null,
                    'fee' => $data['total_fee'] ?? 0,
                    'total_amount' => $data['amount'] ?? $amount,
                    'expires_at' => isset($data['expired_time']) ? date('Y-m-d H:i:s', $data['expired_time']) : now()->addHours($this->expiryHours),
                    'raw' => $result,
                ];
            }

            Log::error('Tripay Create Transaction Error: '.json_encode($result));
            throw new Exception($result['message'] ?? 'Gagal membuat tagihan pembayaran pada gateway');
        } catch (Exception $e) {
            Log::error('Tripay Connection Exception: '.$e->getMessage());

            // If in local/dev environment and API credentials are dummy, provide simulated sandbox response
            if (app()->environment('local', 'testing')) {
                return $this->mockSandboxTransaction($order, $paymentMethod, $amount, $payload);
            }

            throw $e;
        }
    }

    /**
     * Validate incoming webhook callback signature.
     */
    public function validateCallback(Request $request): bool
    {
        $callbackSignature = $request->header('X-Callback-Signature');
        $event = $request->header('X-Callback-Event');

        if (! $callbackSignature) {
            return false;
        }

        $rawContent = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $rawContent, $this->privateKey);

        return hash_equals($expectedSignature, $callbackSignature);
    }

    /**
     * Parse webhook payload and extract standardized order reference and status.
     *
     * @return array{merchant_ref: string, status: string, reference: ?string, raw: array<string, mixed>}
     */
    public function parseCallback(Request $request): array
    {
        $payload = $request->json()->all();

        $merchantRef = (string) ($payload['merchant_ref'] ?? '');
        $statusRaw = strtoupper((string) ($payload['status'] ?? ''));
        $reference = $payload['reference'] ?? null;

        $status = match ($statusRaw) {
            'PAID' => 'paid',
            'EXPIRED' => 'expired',
            'FAILED' => 'failed',
            default => 'pending',
        };

        return [
            'merchant_ref' => $merchantRef,
            'status' => $status,
            'reference' => $reference,
            'raw' => $payload,
        ];
    }

    /**
     * Provide a realistic sandbox mock for development when API keys are dummy.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function mockSandboxTransaction(Order $order, string $paymentMethod, int $amount, array $payload): array
    {
        $ref = 'DEV-'.strtoupper(uniqid());
        $isQris = str_contains(strtoupper($paymentMethod), 'QRIS');
        $isVa = str_contains(strtoupper($paymentMethod), 'VA');

        return [
            'success' => true,
            'reference' => $ref,
            'merchant_ref' => $order->order_number,
            'payment_method' => $paymentMethod,
            'payment_name' => $paymentMethod,
            'pay_code' => $isVa ? '8800'.rand(10000000, 99999999) : null,
            'qr_url' => $isQris ? 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data='.urlencode("QRIS-{$order->order_number}-{$amount}") : null,
            'checkout_url' => route('orders.show', $order->order_number),
            'fee' => 1000,
            'total_amount' => $amount,
            'expires_at' => now()->addHours($this->expiryHours),
            'raw' => [
                'mock' => true,
                'message' => 'Simulated Sandbox Response for Development',
                'payload' => $payload,
            ],
        ];
    }
}

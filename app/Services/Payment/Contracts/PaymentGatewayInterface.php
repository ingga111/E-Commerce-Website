<?php

namespace App\Services\Payment\Contracts;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Get list of payment channels (QRIS, VA, E-Wallet).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPaymentChannels(): array;

    /**
     * Create a closed transaction / payment request.
     *
     * @return array<string, mixed>
     */
    public function createTransaction(Order $order, string $paymentMethod): array;

    /**
     * Validate incoming webhook callback signature.
     */
    public function validateCallback(Request $request): bool;

    /**
     * Parse webhook payload and extract standardized order reference and status.
     *
     * @return array{merchant_ref: string, status: string, reference: ?string, raw: array<string, mixed>}
     */
    public function parseCallback(Request $request): array;
}

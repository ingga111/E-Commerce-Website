<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Request;

class ManualPaymentService implements PaymentGatewayInterface
{
    /**
     * Get list of manual payment methods (Transfer Bank, E-Wallet, QRIS) configured by Admin.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPaymentChannels(): array
    {
        $methods = PaymentMethod::active()->get();

        if ($methods->isEmpty()) {
            return [
                [
                    'group' => 'Transfer Bank Manual',
                    'code' => 'BCA',
                    'name' => 'Bank Central Asia (BCA)',
                    'account_number' => '1234567890',
                    'account_holder' => 'Pemilik Toko',
                    'fee_customer' => ['flat' => 0, 'percent' => 0],
                    'active' => true,
                ],
            ];
        }

        return $methods->map(fn ($m) => [
            'group' => $m->group,
            'code' => $m->code,
            'name' => $m->name,
            'account_number' => $m->account_number,
            'account_holder' => $m->account_holder,
            'qr_image' => $m->qr_image ? asset('storage/'.$m->qr_image) : null,
            'fee_customer' => [
                'flat' => (float) $m->fee_flat,
                'percent' => (float) $m->fee_percent,
            ],
            'instructions' => $m->instructions,
            'active' => true,
        ])->toArray();
    }

    /**
     * Create manual payment transaction record.
     *
     * @return array<string, mixed>
     */
    public function createTransaction(Order $order, string $paymentMethod): array
    {
        $dbMethod = PaymentMethod::where('code', $paymentMethod)->first();
        $amount = (int) round((float) $order->total_amount);

        return [
            'success' => true,
            'reference' => 'MANUAL-'.strtoupper(uniqid()),
            'merchant_ref' => $order->order_number,
            'payment_method' => $paymentMethod,
            'payment_name' => $dbMethod?->name ?? "Transfer {$paymentMethod}",
            'pay_code' => $dbMethod?->account_number,
            'account_number' => $dbMethod?->account_number,
            'account_holder' => $dbMethod?->account_holder,
            'qr_url' => $dbMethod?->qr_image ? asset('storage/'.$dbMethod->qr_image) : null,
            'checkout_url' => route('orders.show', $order->order_number),
            'fee' => $order->admin_fee,
            'total_amount' => $amount,
            'expires_at' => now()->addHours(24),
            'raw' => [
                'type' => 'manual_transfer',
                'account_number' => $dbMethod?->account_number,
                'account_holder' => $dbMethod?->account_holder,
            ],
        ];
    }

    /**
     * Validate incoming callback.
     */
    public function validateCallback(Request $request): bool
    {
        return true;
    }

    /**
     * Parse callback data.
     *
     * @return array{merchant_ref: string, status: string, reference: ?string, raw: array<string, mixed>}
     */
    public function parseCallback(Request $request): array
    {
        return [
            'merchant_ref' => (string) $request->input('merchant_ref', ''),
            'status' => (string) $request->input('status', 'pending'),
            'reference' => $request->input('reference'),
            'raw' => $request->all(),
        ];
    }
}

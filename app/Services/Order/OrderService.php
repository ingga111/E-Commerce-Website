<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Voucher;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected PaymentGatewayInterface $paymentGateway
    ) {}

    /**
     * Create an order and initialize payment gateway transaction.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws Exception
     */
    public function createOrder(array $data, ?User $user = null): Order
    {
        return DB::transaction(function () use ($data, $user) {
            /** @var ProductVariant $variant */
            $variant = ProductVariant::with('product')
                ->where('id', $data['variant_id'])
                ->where('is_active', true)
                ->firstOrFail();

            $product = $variant->product;
            $quantity = (int) ($data['quantity'] ?? 1);

            // Stock check
            if ($variant->stock_type !== 'unlimited' && ! $variant->hasStock($quantity)) {
                $availableStock = $variant->stock_count;
                throw new Exception("Stok untuk paket ini tidak mencukupi (Tersisa: {$availableStock}).");
            }

            // Subtotal calculation
            $subtotal = (float) $variant->price * $quantity;

            // Voucher calculation
            $voucherCode = ! empty($data['voucher_code']) ? strtoupper(trim((string) $data['voucher_code'])) : null;
            $discountAmount = 0.0;
            $voucher = null;

            if ($voucherCode) {
                $voucher = Voucher::where('code', $voucherCode)->lockForUpdate()->first();
                if ($voucher && $voucher->isValid()) {
                    if (! $voucher->canApplyTo($subtotal)) {
                        throw new Exception("Voucher {$voucherCode} membutuhkan minimal belanja Rp ".number_format($voucher->min_order_amount, 0, ',', '.').'.');
                    }
                    $discountAmount = $voucher->calculateDiscount($subtotal);
                    $voucher->increment('used_count');
                } else {
                    throw new Exception("Voucher {$voucherCode} tidak valid atau sudah kadaluarsa.");
                }
            }

            // Calculation with dynamic admin fee from configured PaymentMethod
            $payableSubtotal = max(0, $subtotal - $discountAmount);
            $paymentMethodCode = (string) $data['payment_method'];
            $dbPaymentMethod = PaymentMethod::where('code', $paymentMethodCode)->first();
            $adminFee = $dbPaymentMethod ? $dbPaymentMethod->calculateFee($payableSubtotal) : (float) ($data['admin_fee'] ?? 1000);
            $totalAmount = $payableSubtotal + $adminFee;

            // Create Order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user?->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'product_type' => $product->type,
                'target_user_id' => $data['target_user_id'] ?? null,
                'target_zone_id' => $data['target_zone_id'] ?? null,
                'target_nickname' => $data['target_nickname'] ?? null,
                'subtotal' => $subtotal,
                'voucher_code' => $voucher?->code,
                'discount_amount' => $discountAmount,
                'admin_fee' => $adminFee,
                'total_amount' => $totalAmount,
                'status' => OrderStatus::Pending,
                'notes' => $data['notes'] ?? null,
                'expires_at' => now()->addHours(24),
            ]);

            // Create Order Item
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'product_name' => $product->name,
                'variant_name' => $variant->name,
                'price' => $variant->price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ]);

            // Request Payment from Gateway
            $paymentMethod = (string) $data['payment_method'];
            $gatewayResponse = $this->paymentGateway->createTransaction($order, $paymentMethod);

            // Store Payment Record
            Payment::create([
                'order_id' => $order->id,
                'payment_gateway' => 'manual',
                'payment_method' => $paymentMethod,
                'payment_name' => $gatewayResponse['payment_name'] ?? $paymentMethod,
                'reference' => $gatewayResponse['reference'] ?? null,
                'merchant_ref' => $order->order_number,
                'pay_code' => $gatewayResponse['pay_code'] ?? null,
                'account_number' => $gatewayResponse['account_number'] ?? null,
                'account_holder' => $gatewayResponse['account_holder'] ?? null,
                'qr_url' => $gatewayResponse['qr_url'] ?? null,
                'checkout_url' => $gatewayResponse['checkout_url'] ?? null,
                'amount' => max(0, $order->subtotal - $order->discount_amount),
                'fee' => $gatewayResponse['fee'] ?? $adminFee,
                'total_amount' => $gatewayResponse['total_amount'] ?? $totalAmount,
                'status' => PaymentStatus::Pending,
                'expires_at' => $gatewayResponse['expires_at'] ?? now()->addHours(24),
                'raw_response' => $gatewayResponse['raw'] ?? null,
            ]);

            return $order->fresh(['items.product', 'items.variant', 'payment']);
        });
    }

    /**
     * Process successful payment webhook callback idempotently.
     *
     * @param  array<string, mixed>  $callbackData
     *
     * @throws Exception
     */
    public function processPaymentSuccess(string $merchantRef, array $callbackData = []): Order
    {
        return DB::transaction(function () use ($merchantRef, $callbackData) {
            /** @var Order $order */
            $order = Order::where('order_number', $merchantRef)
                ->lockForUpdate()
                ->firstOrFail();

            $payment = $order->payment;

            // Idempotency check: If already completed, return immediately
            if ($order->status === OrderStatus::Completed) {
                Log::info("Order {$merchantRef} already completed, skipping duplicate execution.");

                return $order;
            }

            // Update Payment Record
            if ($payment && $payment->status !== PaymentStatus::Paid) {
                $payment->update([
                    'status' => PaymentStatus::Paid,
                    'paid_at' => now(),
                    'raw_callback' => $callbackData,
                ]);
            }

            // Decrement variant stock if limited
            foreach ($order->items as $item) {
                $variant = $item->variant;
                if ($variant && $variant->stock_type !== 'unlimited') {
                    $variant->decrement('stock', $item->quantity);
                }
            }

            // Process based on digital product type
            if ($order->isAccount() || $order->isGameAccount()) {
                // Akun digital dikirimkan oleh Admin langsung via WhatsApp ke nomor customer
                $order->update([
                    'status' => OrderStatus::Completed,
                    'notes' => 'Pembayaran dikonfirmasi. Pengiriman akun dilakukan oleh Admin via WhatsApp.',
                ]);
            } else {
                // Game Top-up / Joki: Mark as Completed once payment is confirmed
                $order->update([
                    'status' => OrderStatus::Completed,
                    'notes' => 'Pembayaran telah dikonfirmasi dan pesanan telah berhasil diselesaikan.',
                ]);
            }

            Log::info("Order {$merchantRef} marked as paid successfully.");

            return $order->fresh(['items', 'payment']);
        });
    }

    /**
     * Process failed / expired payment callback.
     *
     * @param  string  $status  'failed' or 'expired'
     * @param  array<string, mixed>  $callbackData
     */
    public function processPaymentFailed(string $merchantRef, string $status, array $callbackData = []): void
    {
        DB::transaction(function () use ($merchantRef, $status, $callbackData) {
            $order = Order::where('order_number', $merchantRef)->lockForUpdate()->first();

            if (! $order || $order->isPaid()) {
                return;
            }

            $orderStatus = $status === 'expired' ? OrderStatus::Expired : OrderStatus::Failed;
            $paymentStatus = $status === 'expired' ? PaymentStatus::Expired : PaymentStatus::Failed;

            $order->update(['status' => $orderStatus]);

            if ($order->payment) {
                $order->payment->update([
                    'status' => $paymentStatus,
                    'raw_callback' => $callbackData,
                ]);
            }
        });
    }
}

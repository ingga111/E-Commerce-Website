<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\Order\OrderService;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected PaymentGatewayInterface $paymentGateway
    ) {}

    /**
     * Display checkout and product order form.
     */
    public function showCheckoutForm(Product $product): View
    {
        $product->load(['variants' => function ($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }]);

        $paymentChannels = $this->paymentGateway->getPaymentChannels();

        return view('orders.checkout', compact('product', 'paymentChannels'));
    }

    /**
     * Store new order and initiate payment.
     */
    public function store(StoreOrderRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $user = $request->user();
            $order = $this->orderService->createOrder($request->validated(), $user);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat.',
                    'order_number' => $order->order_number,
                    'redirect_url' => route('orders.show', $order->order_number),
                ]);
            }

            return redirect()->route('orders.show', $order->order_number)
                ->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show order invoice, payment details (QRIS / VA), and delivered credentials.
     */
    public function show(string $orderNumber): View
    {
        $order = Order::with(['items.product', 'items.variant', 'payment'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }

    /**
     * Real-time polling endpoint to check order payment status via AJAX.
     */
    public function checkStatus(string $orderNumber): JsonResponse
    {
        $order = Order::with(['payment'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json([
            'order_number' => $order->order_number,
            'status' => $order->status->value,
            'status_label' => $order->status->label(),
            'is_paid' => $order->isPaid(),
            'payment_status' => $order->payment?->status?->value,
        ]);
    }

    /**
     * Upload customer transfer payment proof.
     */
    public function uploadPaymentProof(Request $request, string $orderNumber): RedirectResponse
    {
        $request->validate([
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'payment_proof.required' => 'Pilih file foto atau dokumen bukti transfer Anda.',
            'payment_proof.mimes' => 'Format bukti transfer harus berupa gambar (JPG, PNG, WEBP) atau PDF.',
            'payment_proof.max' => 'Ukuran file bukti transfer maksimal 5 MB.',
        ]);

        $order = Order::with('payment')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        if ($order->payment) {
            $order->payment->update([
                'payment_proof' => $path,
                'payment_proof_uploaded_at' => now(),
                'admin_note' => $request->input('notes'),
            ]);
        }

        $order->update([
            'status' => OrderStatus::Processing,
            'notes' => 'Bukti pembayaran telah diunggah oleh pembeli. Menunggu verifikasi admin.',
        ]);

        return redirect()->route('orders.show', $orderNumber)
            ->with('success', 'Bukti transfer berhasil diunggah! Admin kami akan segera memverifikasi pembayaran Anda.');
    }
}

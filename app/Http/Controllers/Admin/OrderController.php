<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Order\OrderService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Display listing of orders with filters.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = Order::with(['items.product', 'payment'])->latest();

        if ($status === 'waiting_verification') {
            $query->where('status', OrderStatus::Processing)
                ->whereHas('payment', fn ($q) => $q->whereNotNull('payment_proof')->where('status', '!=', PaymentStatus::Paid));
        } elseif ($status && in_array($status, ['pending', 'processing', 'completed', 'failed', 'cancelled'], true)) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => Order::count(),
            'waiting_verification' => Order::where('status', OrderStatus::Processing)
                ->whereHas('payment', fn ($q) => $q->whereNotNull('payment_proof')->where('status', '!=', PaymentStatus::Paid))->count(),
            'pending' => Order::where('status', OrderStatus::Pending)->count(),
            'completed' => Order::where('status', OrderStatus::Completed)->count(),
            'failed' => Order::whereIn('status', [OrderStatus::Failed, OrderStatus::Cancelled])->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts', 'status'));
    }

    /**
     * Display order detail with payment proof verification modal/section.
     */
    public function show(Order $order): View
    {
        $order->load(['items.product', 'items.variant', 'payment']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Confirm customer's manual payment and fulfill order.
     */
    public function confirmPayment(Order $order): RedirectResponse
    {
        try {
            $this->orderService->processPaymentSuccess($order->order_number, [
                'confirmed_by' => auth()->user()?->name ?? 'Admin',
                'confirmed_at' => now()->toIso8601String(),
            ]);

            $order->refresh();
            if ($order->status !== OrderStatus::Completed && ! ($order->isAccount() || $order->isGameAccount())) {
                $order->update([
                    'status' => OrderStatus::Completed,
                    'notes' => 'Pembayaran dikonfirmasi oleh Admin.',
                ]);
            }

            return back()->with('success', "Pembayaran untuk pesanan {$order->order_number} berhasil dikonfirmasi dan pesanan telah selesai!");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses konfirmasi: '.$e->getMessage());
        }
    }

    /**
     * Reject customer's payment proof.
     */
    public function rejectPayment(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $reason = $request->input('reason', 'Bukti pembayaran tidak valid atau transfer belum diterima.');

        $order->update([
            'status' => OrderStatus::Failed,
            'notes' => 'Pembayaran ditolak: '.$reason,
        ]);

        if ($order->payment) {
            $order->payment->update([
                'status' => PaymentStatus::Failed,
                'admin_note' => $reason,
            ]);
        }

        return back()->with('success', "Pesanan {$order->order_number} telah ditandai ditolak.");
    }
}

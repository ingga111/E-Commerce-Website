<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderHistoryController extends Controller
{
    /**
     * Display logged-in customer's order history.
     */
    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Display single order detail for logged-in customer.
     */
    public function show(Request $request, string $orderNumber): View
    {
        $order = $request->user()
            ->orders()
            ->with(['items.product', 'items.variant', 'payment'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('customer.orders.show', compact('order'));
    }
}

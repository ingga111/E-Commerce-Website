<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display summary metrics and recent orders for admin.
     */
    public function index(): View
    {
        $todayRevenue = Order::whereIn('status', [OrderStatus::Completed, OrderStatus::Processing])
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $monthlyRevenue = Order::whereIn('status', [OrderStatus::Completed, OrderStatus::Processing])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', OrderStatus::Pending)->count();
        $processingOrders = Order::where('status', OrderStatus::Processing)->count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Total available stock for accounts / limited variants
        $availableAccountStocks = ProductVariant::where('stock_type', '!=', 'unlimited')->sum('stock');

        // Recent 10 orders
        $recentOrders = Order::with(['items.product', 'payment'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'todayRevenue',
            'monthlyRevenue',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'totalCustomers',
            'availableAccountStocks',
            'recentOrders'
        ));
    }
}

@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Admin Dashboard</h1>
            <p class="text-xs text-gray-400 mt-1">Ringkasan transaksi penjualan diamond game & akun digital real-time.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.orders.index') }}" class="px-3.5 py-2 rounded-xl bg-cyan-600/20 hover:bg-cyan-600/30 text-cyan-400 border border-cyan-500/30 text-xs font-bold transition-all flex items-center gap-1.5">
                <span>📋 Verifikasi Pesanan</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="px-3.5 py-2 rounded-xl bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 text-xs font-bold transition-all flex items-center gap-1.5">
                <span>👥 Kelola User</span>
            </a>
            <a href="{{ route('admin.products.index') }}" class="px-3.5 py-2 rounded-xl bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-400 border border-indigo-500/30 text-xs font-bold transition-all">
                📦 Kelola Produk
            </a>
            <a href="{{ route('admin.payment-methods.index') }}" class="px-3.5 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 text-xs font-bold transition-all">
                💳 Rekening Toko
            </a>
            <a href="{{ route('admin.banners.index') }}" class="px-3.5 py-2 rounded-xl bg-cyan-600/20 hover:bg-cyan-600/30 text-cyan-400 border border-cyan-500/30 text-xs font-bold transition-all">
                🖼️ Kelola Banner
            </a>
            <a href="{{ route('admin.flash-sales.index') }}" class="px-3.5 py-2 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 text-xs font-bold transition-all">
                🔥 Flash Sale
            </a>
            <a href="{{ route('admin.vouchers.index') }}" class="px-3.5 py-2 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 border border-emerald-500/30 text-xs font-bold transition-all">
                🎟️ Voucher Promo
            </a>
            <a href="{{ route('admin.products.create') }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition-all">
                + Tambah Produk
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Today Revenue -->
        <div class="glass-card rounded-2xl p-5 border border-gray-800">
            <span class="text-xs text-gray-400 font-medium">Pendapatan Hari Ini</span>
            <div class="text-2xl font-extrabold text-emerald-400 mt-2">
                Rp {{ number_format($todayRevenue, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-gray-500 mt-1 block">Pesanan lunas hari ini</span>
        </div>

        <!-- Monthly Revenue -->
        <div class="glass-card rounded-2xl p-5 border border-gray-800">
            <span class="text-xs text-gray-400 font-medium">Pendapatan Bulan Ini</span>
            <div class="text-2xl font-extrabold text-cyan-400 mt-2">
                Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-gray-500 mt-1 block">Bulan {{ date('F Y') }}</span>
        </div>

        <!-- Processing Orders -->
        <div class="glass-card rounded-2xl p-5 border border-gray-800">
            <span class="text-xs text-gray-400 font-medium">Pesanan Diproses (Game Top-Up)</span>
            <div class="text-2xl font-extrabold text-blue-400 mt-2">
                {{ $processingOrders }}
            </div>
            <span class="text-[10px] text-gray-500 mt-1 block">Menunggu pengisian diamond</span>
        </div>

        <!-- Available Account Stock -->
        <div class="glass-card rounded-2xl p-5 border border-gray-800">
            <span class="text-xs text-gray-400 font-medium">Stok Akun Siap Jual</span>
            <div class="text-2xl font-extrabold text-purple-400 mt-2">
                {{ $availableAccountStocks }}
            </div>
            <span class="text-[10px] text-gray-500 mt-1 block">Kredensial belum terpakai</span>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="glass-card rounded-3xl p-6 border border-gray-800">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-white">10 Transaksi Terbaru</h3>
            <span class="text-xs text-gray-400">Total Transaksi: {{ $totalOrders }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-300">
                <thead class="bg-gray-900/60 text-gray-400 uppercase tracking-wider text-[10px] border-b border-gray-800">
                    <tr>
                        <th class="py-3 px-4">Invoice</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Tipe Produk</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Metode</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-900/30 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-semibold text-white">
                                {{ $order->order_number }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-white block">{{ $order->customer_name }}</span>
                                <span class="text-[10px] text-gray-400">{{ $order->customer_email }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ $order->isGame() ? 'bg-indigo-500/10 text-indigo-400' : 'bg-cyan-500/10 text-cyan-400' }}">
                                    {{ $order->product_type->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-white">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-cyan-400 font-mono font-semibold">
                                {{ $order->payment_method }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                    @if($order->status->value === 'completed') bg-emerald-500/20 text-emerald-400
                                    @elseif($order->status->value === 'processing') bg-blue-500/20 text-blue-400
                                    @elseif($order->status->value === 'pending') bg-amber-500/20 text-amber-400
                                    @else bg-rose-500/20 text-rose-400 @endif">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1 rounded-lg bg-gray-800 hover:bg-gray-700 text-indigo-400 font-semibold transition-all">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">Belum ada transaksi pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400">Dashboard</a>
                <span>/</span>
                <span class="text-white">Daftar Pesanan & Verifikasi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Verifikasi Pembayaran & Pesanan</h1>
            <p class="text-xs text-gray-400 mt-1">Periksa bukti transfer manual pembeli dan konfirmasi pengiriman produk digital.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.payment-methods.index') }}" class="px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold border border-gray-700 transition-all flex items-center gap-1.5">
                <span>💳 Rekening Bank</span>
            </a>
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold border border-gray-700 transition-all flex items-center gap-1.5">
                <span>📦 Produk & Stok Akun</span>
            </a>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <a href="{{ route('admin.orders.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ empty($status) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-gray-900 border border-gray-800 text-gray-400 hover:text-white' }}">
            Semua Pesanan ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'waiting_verification']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all relative flex items-center gap-1.5 {{ $status === 'waiting_verification' ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-600/30' : 'bg-gray-900 border border-gray-800 text-cyan-400 hover:text-cyan-300' }}">
            <span>🔔 Menunggu Verifikasi</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold {{ $counts['waiting_verification'] > 0 ? 'bg-cyan-400 text-gray-950 animate-pulse' : 'bg-gray-800 text-gray-300' }}">
                {{ $counts['waiting_verification'] }}
            </span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'pending' ? 'bg-amber-600 text-white shadow-lg shadow-amber-600/30' : 'bg-gray-900 border border-gray-800 text-amber-400 hover:text-amber-300' }}">
            Menunggu Transfer ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'completed' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-gray-900 border border-gray-800 text-emerald-400 hover:text-emerald-300' }}">
            Selesai / Lunas ({{ $counts['completed'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'failed']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'failed' ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/30' : 'bg-gray-900 border border-gray-800 text-rose-400 hover:text-rose-300' }}">
            Ditolak / Batal ({{ $counts['failed'] }})
        </a>
    </div>

    <!-- Orders Table -->
    <div class="glass-card rounded-3xl p-6 border border-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-300">
                <thead class="bg-gray-900/60 text-gray-400 uppercase tracking-wider text-[10px] border-b border-gray-800">
                    <tr>
                        <th class="py-3 px-4">No. Pesanan & Waktu</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Produk Dibeli</th>
                        <th class="py-3 px-4">Rekening Tujuan</th>
                        <th class="py-3 px-4">Total Bayar</th>
                        <th class="py-3 px-4">Bukti Transfer</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-900/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="font-mono font-bold text-indigo-400 hover:text-indigo-300 block">
                                    #{{ $order->order_number }}
                                </a>
                                <span class="text-[10px] text-gray-500 block mt-0.5">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-white block">{{ $order->customer_name }}</span>
                                <span class="text-[11px] text-gray-400 block">{{ $order->customer_email }}</span>
                                <span class="text-[10px] text-gray-500 font-mono">{{ $order->customer_phone }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @foreach($order->items as $item)
                                    <div>
                                        <span class="font-semibold text-white">{{ $item->product_name }}</span>
                                        <span class="text-[10px] text-gray-400">({{ $item->variant_name }})</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded bg-gray-800 border border-gray-700 text-cyan-300 font-mono text-[11px] font-bold">
                                    {{ $order->payment_method }}
                                </span>
                                @if($order->payment?->account_number)
                                    <span class="text-[10px] text-gray-400 block mt-0.5 font-mono">
                                        {{ $order->payment->account_number }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-bold text-cyan-400 text-sm">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($order->payment?->payment_proof)
                                    <a href="{{ asset('storage/'.$order->payment->payment_proof) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-cyan-950/60 border border-cyan-500/40 text-cyan-400 hover:text-cyan-300 text-[11px] font-bold transition-all">
                                        <span>📎 Lihat Bukti</span>
                                    </a>
                                @else
                                    <span class="text-[11px] text-gray-500 italic">Belum ada bukti</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($order->status->value === 'processing' && $order->payment?->payment_proof && $order->payment?->status?->value !== 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 animate-pulse">
                                        ● Perlu Verifikasi
                                    </span>
                                @elseif($order->status->value === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                        ✓ Lunas & Selesai
                                    </span>
                                @elseif($order->status->value === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                        Menunggu Bayar
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                        {{ ucfirst($order->status->value) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 text-indigo-400 hover:text-white font-semibold transition-all">
                                        Detail
                                    </a>
                                    @if(in_array($order->status->value, ['pending', 'processing']) && $order->status->value !== 'completed' && $order->payment?->status?->value !== 'paid')
                                        <form action="{{ route('admin.orders.confirm', $order->id) }}" method="POST" onsubmit="return confirm('Konfirmasi pembayaran pesanan #{{ $order->order_number }} dan proses pesanan ke pelanggan?');" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition-all shadow-sm">
                                                Konfirmasi ✓
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-gray-500">
                                Tidak ada data pesanan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection

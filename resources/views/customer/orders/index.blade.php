@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Riwayat Transaksi Saya</h1>
            <p class="text-xs text-gray-400 mt-1">Daftar pesanan top-up game & akun digital yang pernah Anda beli.</p>
        </div>
        <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold text-xs shadow-md shadow-cyan-600/20 transition-all">
            + Pesan Baru
        </a>
    </div>

    <div class="glass-card rounded-3xl p-6 border border-gray-800">
        <div class="space-y-4">
            @forelse($orders as $order)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-gray-900/50 border border-gray-800 hover:border-cyan-500/40 transition-all">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono font-bold text-white text-sm">{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                {{ $order->isGame() ? 'bg-cyan-500/10 text-cyan-400' : 'bg-amber-500/10 text-amber-400' }}">
                                {{ $order->product_type->label() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400">
                            {{ $order->items->pluck('product_name')->implode(', ') }} • {{ $order->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-4">
                        <div class="text-right">
                            <span class="text-xs font-bold text-cyan-400 block">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            <span class="text-[10px] font-semibold
                                @if($order->status->value === 'completed') text-emerald-400
                                @elseif($order->status->value === 'processing') text-blue-400
                                @elseif($order->status->value === 'pending') text-amber-400
                                @else text-rose-400 @endif">
                                {{ $order->status->label() }}
                            </span>
                        </div>
                        <a href="{{ route('orders.show', $order->order_number) }}" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-cyan-400 hover:text-white font-bold text-xs border border-gray-700 transition-all">
                            Lihat Invoice ➜
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500 text-xs">
                    Anda belum memiliki riwayat transaksi pesanan.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

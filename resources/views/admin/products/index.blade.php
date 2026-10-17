@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400">Dashboard</a>
                <span>/</span>
                <span class="text-white">Manajemen Produk</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Daftar Produk Digital</h1>
            <p class="text-xs text-gray-400 mt-1">Kelola game top-up, akun premium, variasi harga, dan stok kredensial.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.payment-methods.index') }}" class="px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold border border-gray-700 transition-all">
                💳 Metode Pembayaran
            </a>
            <a href="{{ route('admin.products.create') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/20 transition-all flex items-center gap-1.5">
                <span>+ Tambah Produk Baru</span>
            </a>
        </div>
    </div>

    <!-- Products Table -->
    <div class="glass-card rounded-3xl p-6 border border-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-300">
                <thead class="bg-gray-900/60 text-gray-400 uppercase tracking-wider text-[10px] border-b border-gray-800">
                    <tr>
                        <th class="py-3 px-4">Produk</th>
                        <th class="py-3 px-4">Kategori / Tipe</th>
                        <th class="py-3 px-4">Publisher</th>
                        <th class="py-3 px-4">Form Target</th>
                        <th class="py-3 px-4">Variasi Nominal</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-900/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-lg flex-shrink-0">
                                        {{ $product->isGame() ? '🎮' : '🍿' }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-white block">{{ $product->name }}</span>
                                        <span class="text-[11px] text-gray-500">/product/{{ $product->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ $product->isGame() ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' }}">
                                    {{ $product->type->label() }}
                                </span>
                                <span class="text-[11px] text-gray-400 block mt-1">{{ $product->category->name ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-gray-300">
                                {{ $product->publisher ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="space-y-0.5 text-[11px]">
                                    @if($product->requires_user_id)
                                        <span class="text-gray-300 block">✓ {{ $product->user_id_label }}</span>
                                    @endif
                                    @if($product->requires_zone_id)
                                        <span class="text-amber-400 block">✓ {{ $product->zone_id_label }}</span>
                                    @endif
                                    @if(!$product->requires_user_id && !$product->requires_zone_id)
                                        <span class="text-gray-500">Email Only</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-cyan-400">{{ $product->variants->count() }} Paket</span>
                                @if($product->isAccount() || $product->isGameAccount())
                                    @php 
                                        $stockCount = $product->variants->sum('stock');
                                    @endphp
                                    <span class="text-[10px] block text-emerald-400 font-semibold">Stok: {{ $stockCount }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $product->is_active ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 text-indigo-400 hover:text-white font-semibold transition-all">
                                        Edit & Variasi
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini beserta seluruh variasinya?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-950/40 hover:bg-rose-900 text-rose-400 font-semibold transition-all">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">Belum ada produk digital. Silakan klik "Tambah Produk Baru".</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400">Dashboard</a>
                <span>/</span>
                <span class="text-white">Rekening & Metode Pembayaran</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Rekening & Pembayaran Manual</h1>
            <p class="text-xs text-gray-400 mt-1">Kelola nomor rekening bank, e-wallet, dan QRIS untuk transfer manual pembeli.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold border border-gray-700 transition-all flex items-center gap-1.5">
                <span>📋 Verifikasi Pesanan</span>
            </a>
            <a href="{{ route('admin.payment-methods.create') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold shadow-lg shadow-cyan-600/20 transition-all flex items-center gap-1.5">
                <span>+ Tambah Rekening Baru</span>
            </a>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="glass-card rounded-2xl p-5 border border-cyan-500/20 bg-cyan-950/20 mb-8 flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-cyan-600/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-xl flex-shrink-0">
            💳
        </div>
        <div class="text-xs">
            <h4 class="font-bold text-white text-sm">Alur Pembayaran Transfer Manual</h4>
            <p class="text-gray-300 mt-1 leading-relaxed">
                Nomor rekening dan nama pemilik rekening yang Anda aktifkan di bawah ini akan ditampilkan kepada pembeli saat checkout. Setelah pembeli mentransfer dan mengunggah foto bukti pembayaran, Anda dapat memverifikasi dan menyetujuinya di halaman 
                <a href="{{ route('admin.orders.index') }}" class="text-cyan-400 underline font-semibold hover:text-cyan-300">Verifikasi Pesanan</a> untuk otomatis mengirim akun digital ke pembeli.
            </p>
        </div>
    </div>

    <!-- Payment Methods Table -->
    <div class="glass-card rounded-3xl p-6 border border-gray-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Daftar Rekening & Channel Aktif</h3>
            <span class="text-xs text-gray-400">Total: {{ $paymentMethods->count() }} Metode</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-300">
                <thead class="bg-gray-900/60 text-gray-400 uppercase tracking-wider text-[10px] border-b border-gray-800">
                    <tr>
                        <th class="py-3 px-4">Bank / Channel</th>
                        <th class="py-3 px-4">Nomor Rekening / No. HP</th>
                        <th class="py-3 px-4">Atas Nama (A/N)</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Biaya Admin</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @forelse($paymentMethods as $method)
                        <tr class="hover:bg-gray-900/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-800 border border-gray-700 flex items-center justify-center font-bold text-cyan-400 text-xs">
                                        {{ substr($method->code, 0, 4) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-white block">{{ $method->name }}</span>
                                        <span class="text-[10px] text-gray-500 font-mono">{{ $method->code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-cyan-300 text-sm">
                                {{ $method->account_number ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-white">
                                {{ $method->account_holder ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-gray-800 border border-gray-700 text-gray-300">
                                    {{ $method->group }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-gray-300">
                                @if($method->fee_flat > 0)
                                    Rp {{ number_format($method->fee_flat, 0, ',', '.') }}
                                @else
                                    <span class="text-emerald-400">Gratis (Rp 0)</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <form action="{{ route('admin.payment-methods.toggle', $method->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase transition-all
                                        {{ $method->is_active ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30 hover:bg-rose-500/30' }}">
                                        {{ $method->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.payment-methods.edit', $method->id) }}" class="px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 text-indigo-400 hover:text-white font-semibold transition-all">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.payment-methods.destroy', $method->id) }}" method="POST" onsubmit="return confirm('Hapus rekening/metode pembayaran ini?');" class="inline">
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
                            <td colspan="7" class="text-center py-8 text-gray-500">Belum ada metode pembayaran/rekening. Silakan klik "Tambah Rekening Baru" di atas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

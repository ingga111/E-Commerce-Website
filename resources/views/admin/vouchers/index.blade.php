@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-400">Dashboard</a>
                <span>/</span>
                <span class="text-white">Kelola Voucher Promo</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-2.5">
                <span>🎟️</span> Kelola Voucher &amp; Kode Promo
            </h1>
            <p class="text-xs text-gray-400 mt-1">Buat kode diskon game, cashback, dan atur batasan kuota serta minimal order untuk pembeli.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold border border-gray-700 transition-all flex items-center gap-1.5">
                <span>⚡ Dashboard</span>
            </a>
            <button type="button" onclick="document.getElementById('modal-create-voucher').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold shadow-lg shadow-cyan-600/25 transition-all flex items-center gap-1.5">
                <span>+ Buat Voucher Baru</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-2">
            <span>✓</span> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 mb-6 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs space-y-1">
            @foreach($errors->all() as $error)
                <p>⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <div class="glass-card rounded-2xl p-5 border border-gray-800">
            <span class="text-xs text-gray-400 font-medium">Total Voucher</span>
            <div class="text-2xl font-extrabold text-white mt-1">
                {{ $totalVouchers }}
            </div>
            <span class="text-[10px] text-gray-500 mt-1 block">Semua kode promo tercatat</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-gray-800">
            <span class="text-xs text-gray-400 font-medium">Voucher Aktif</span>
            <div class="text-2xl font-extrabold text-emerald-400 mt-1">
                {{ $activeVouchers }}
            </div>
            <span class="text-[10px] text-gray-500 mt-1 block">Dapat digunakan pembeli</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-gray-800">
            <span class="text-xs text-gray-400 font-medium">Total Digunakan (Klaim)</span>
            <div class="text-2xl font-extrabold text-cyan-400 mt-1">
                {{ $totalClaims }} Kali
            </div>
            <span class="text-[10px] text-gray-500 mt-1 block">Penggunaan oleh pelanggan</span>
        </div>
    </div>

    <!-- Voucher Table -->
    <div class="glass-card rounded-3xl p-6 border border-gray-800">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-800">
            <div>
                <h3 class="text-base font-bold text-white">Daftar Kode Voucher</h3>
                <p class="text-xs text-gray-400 mt-0.5">Voucher dapat dimasukkan pembeli pada step ke-5 saat checkout order.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-300">
                <thead class="bg-gray-900/60 text-gray-400 uppercase tracking-wider text-[10px] border-b border-gray-800">
                    <tr>
                        <th class="py-3 px-4">Kode Voucher</th>
                        <th class="py-3 px-4">Nama & Deskripsi</th>
                        <th class="py-3 px-4">Potongan Diskon</th>
                        <th class="py-3 px-4">Min. Belanja</th>
                        <th class="py-3 px-4">Kuota Pemakaian</th>
                        <th class="py-3 px-4">Masa Berlaku</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @forelse($vouchers as $voucher)
                        <tr class="hover:bg-gray-900/40 transition-colors">
                            <td class="py-4 px-4 font-mono">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2.5 py-1 rounded-xl bg-cyan-500/10 text-cyan-400 font-extrabold border border-cyan-500/20 text-xs">
                                        {{ $voucher->code }}
                                    </span>
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $voucher->code }}'); alert('Kode disalin: {{ $voucher->code }}');" class="text-gray-500 hover:text-white transition-colors" title="Salin Kode">
                                        📋
                                    </button>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-white block">{{ $voucher->name }}</span>
                                @if($voucher->description)
                                    <span class="text-[11px] text-gray-400 block line-clamp-1 mt-0.5">{{ $voucher->description }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @if($voucher->type === 'percentage')
                                    <span class="font-extrabold text-amber-400 text-xs">{{ round($voucher->discount_amount) }}%</span>
                                    @if($voucher->max_discount)
                                        <span class="text-[10px] text-gray-400 block">Maks. Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}</span>
                                    @endif
                                @else
                                    <span class="font-extrabold text-emerald-400 text-xs">Rp {{ number_format($voucher->discount_amount, 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-gray-500 block">Potongan Tetap</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @if($voucher->min_order_amount > 0)
                                    <span class="font-semibold text-gray-200">Rp {{ number_format($voucher->min_order_amount, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-500">Tanpa Minimal</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-white">{{ $voucher->used_count }}</span>
                                    <span class="text-gray-500">/</span>
                                    <span class="text-gray-400">{{ $voucher->usage_limit ?? '∞' }}</span>
                                </div>
                                @if($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit)
                                    <span class="text-[10px] text-rose-400 font-bold block">Kuota Habis</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-[11px] text-gray-400">
                                @if($voucher->expires_at)
                                    <span class="{{ $voucher->expires_at->isPast() ? 'text-rose-400 font-semibold' : 'text-gray-300' }}">
                                        {{ $voucher->expires_at->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="block text-[10px] text-gray-500">
                                        {{ $voucher->expires_at->isPast() ? 'Kadaluarsa' : $voucher->expires_at->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-emerald-400">Selamanya</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @if(! $voucher->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-800 text-gray-400 border border-gray-700">Nonaktif</span>
                                @elseif($voucher->expires_at && $voucher->expires_at->isPast())
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">Kadaluarsa</span>
                                @elseif($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">Habis</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Aktif</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.vouchers.toggle', $voucher) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $voucher->is_active ? 'text-amber-400 hover:text-amber-300' : 'text-emerald-400 hover:text-emerald-300' }} transition-colors">
                                            {{ $voucher->is_active ? 'Matikan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <button type="button" onclick="openEditVoucherModal({{ json_encode($voucher) }})" class="p-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white transition-all" title="Edit Voucher">
                                        ✏️
                                    </button>

                                    <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 transition-all" title="Hapus Voucher">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 text-xs">
                                Belum ada voucher diskon. Klik tombol <strong>+ Buat Voucher Baru</strong> untuk menambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: BUAT VOUCHER BARU -->
<div id="modal-create-voucher" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-cyan-500/30 max-w-lg w-full shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between pb-4 border-b border-gray-800 mb-6">
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">🎟️</span>
                <h3 class="text-lg font-black text-white">Buat Voucher Diskon Baru</h3>
            </div>
            <button type="button" onclick="document.getElementById('modal-create-voucher').classList.add('hidden')" class="w-8 h-8 rounded-full bg-gray-800 text-gray-400 hover:text-white flex items-center justify-center">✕</button>
        </div>

        <form action="{{ route('admin.vouchers.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kode Voucher <span class="text-rose-400">*</span></label>
                    <input type="text" name="code" placeholder="DISKON10" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white uppercase font-mono focus:outline-none focus:border-cyan-500">
                    <span class="text-[10px] text-gray-500 mt-1 block">Huruf/angka tanpa spasi</span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Voucher <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" placeholder="Promo Gajian Diamond" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Deskripsi Singkat</label>
                <textarea name="description" rows="2" placeholder="Diskon khusus top up dan beli akun" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Tipe Diskon <span class="text-rose-400">*</span></label>
                    <select name="type" id="create-type" onchange="toggleMaxDiscount('create-type', 'create-max-discount-box')" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                        <option value="fixed">Potongan Tetap (Rp)</option>
                        <option value="percentage">Persentase (%)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nilai Diskon <span class="text-rose-400">*</span></label>
                    <input type="number" name="discount_amount" placeholder="Contoh: 10000 atau 10" step="any" min="0.01" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div id="create-max-discount-box" class="hidden">
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Maks. Potongan (Rp)</label>
                    <input type="number" name="max_discount" placeholder="Contoh: 50000" min="0" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                    <span class="text-[10px] text-gray-500 mt-1 block">Batas maksimal jika %</span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Min. Belanja (Rp)</label>
                    <input type="number" name="min_order_amount" value="0" min="0" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                    <span class="text-[10px] text-gray-500 mt-1 block">0 = tanpa syarat min</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Batas Kuota Pemakaian</label>
                <input type="number" name="usage_limit" placeholder="Kosongkan jika tanpa batas (Unlimited)" min="1" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Mulai Berlaku</label>
                    <input type="datetime-local" name="starts_at" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kadaluarsa (Selesai)</label>
                    <input type="datetime-local" name="expires_at" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-cyan-500 bg-gray-900 border-gray-700 focus:ring-0">
                    <span class="text-xs font-semibold text-gray-200">Aktifkan voucher sekarang</span>
                </label>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="document.getElementById('modal-create-voucher').classList.add('hidden')" class="flex-1 py-3 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold text-xs transition-all">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-xs shadow-lg shadow-cyan-600/20 transition-all">
                    Simpan Voucher 🎟️
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDIT VOUCHER -->
<div id="modal-edit-voucher" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-cyan-500/30 max-w-lg w-full shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between pb-4 border-b border-gray-800 mb-6">
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">✏️</span>
                <h3 class="text-lg font-black text-white">Edit Voucher</h3>
            </div>
            <button type="button" onclick="document.getElementById('modal-edit-voucher').classList.add('hidden')" class="w-8 h-8 rounded-full bg-gray-800 text-gray-400 hover:text-white flex items-center justify-center">✕</button>
        </div>

        <form id="edit-voucher-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kode Voucher <span class="text-rose-400">*</span></label>
                    <input type="text" name="code" id="edit-code" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white uppercase font-mono focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Voucher <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" id="edit-name" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Deskripsi Singkat</label>
                <textarea name="description" id="edit-description" rows="2" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Tipe Diskon <span class="text-rose-400">*</span></label>
                    <select name="type" id="edit-type" onchange="toggleMaxDiscount('edit-type', 'edit-max-discount-box')" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                        <option value="fixed">Potongan Tetap (Rp)</option>
                        <option value="percentage">Persentase (%)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nilai Diskon <span class="text-rose-400">*</span></label>
                    <input type="number" name="discount_amount" id="edit-discount-amount" step="any" min="0.01" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div id="edit-max-discount-box" class="hidden">
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Maks. Potongan (Rp)</label>
                    <input type="number" name="max_discount" id="edit-max-discount" min="0" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Min. Belanja (Rp)</label>
                    <input type="number" name="min_order_amount" id="edit-min-order-amount" min="0" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Batas Kuota Pemakaian</label>
                <input type="number" name="usage_limit" id="edit-usage-limit" placeholder="Kosongkan jika unlimited" min="1" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Mulai Berlaku</label>
                    <input type="datetime-local" name="starts_at" id="edit-starts-at" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kadaluarsa (Selesai)</label>
                    <input type="datetime-local" name="expires_at" id="edit-expires-at" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit-is-active" value="1" class="w-4 h-4 rounded text-cyan-500 bg-gray-900 border-gray-700 focus:ring-0">
                    <span class="text-xs font-semibold text-gray-200">Aktifkan voucher</span>
                </label>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="document.getElementById('modal-edit-voucher').classList.add('hidden')" class="flex-1 py-3 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold text-xs transition-all">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-xs shadow-lg shadow-cyan-600/20 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleMaxDiscount(selectId, boxId) {
        const sel = document.getElementById(selectId);
        const box = document.getElementById(boxId);
        if (sel.value === 'percentage') {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }

    function openEditVoucherModal(v) {
        document.getElementById('edit-voucher-form').action = `/admin/vouchers/${v.id}`;
        document.getElementById('edit-code').value = v.code || '';
        document.getElementById('edit-name').value = v.name || '';
        document.getElementById('edit-description').value = v.description || '';
        document.getElementById('edit-type').value = v.type || 'fixed';
        document.getElementById('edit-discount-amount').value = parseFloat(v.discount_amount) || '';
        document.getElementById('edit-min-order-amount').value = parseFloat(v.min_order_amount) || '0';
        document.getElementById('edit-max-discount').value = v.max_discount ? parseFloat(v.max_discount) : '';
        document.getElementById('edit-usage-limit').value = v.usage_limit || '';
        document.getElementById('edit-is-active').checked = !!v.is_active;

        toggleMaxDiscount('edit-type', 'edit-max-discount-box');

        if (v.starts_at) {
            const date = new Date(v.starts_at);
            const offset = date.getTimezoneOffset() * 60000;
            document.getElementById('edit-starts-at').value = (new Date(date.getTime() - offset)).toISOString().slice(0, 16);
        } else {
            document.getElementById('edit-starts-at').value = '';
        }

        if (v.expires_at) {
            const date = new Date(v.expires_at);
            const offset = date.getTimezoneOffset() * 60000;
            document.getElementById('edit-expires-at').value = (new Date(date.getTime() - offset)).toISOString().slice(0, 16);
        } else {
            document.getElementById('edit-expires-at').value = '';
        }

        document.getElementById('modal-edit-voucher').classList.remove('hidden');
    }
</script>
@endpush
@endsection

@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.payment-methods.index') }}" class="hover:text-indigo-400">Metode Pembayaran</a>
        <span>/</span>
        <span class="text-white">Edit {{ $paymentMethod->code }}</span>
    </div>

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Edit Rekening: {{ $paymentMethod->name }}</h1>
            <p class="text-xs text-gray-400 mt-1">Perbarui nomor rekening, nama pemilik rekening, atau petunjuk transfer.</p>
        </div>
    </div>

    <form action="{{ route('admin.payment-methods.update', $paymentMethod->id) }}" method="POST" enctype="multipart/form-data" class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Kode Channel (Permanen)</label>
                <input type="text" value="{{ $paymentMethod->code }}" disabled class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 text-sm text-cyan-400 font-mono font-bold cursor-not-allowed">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Bank / Channel <span class="text-rose-400">*</span></label>
                <input type="text" name="name" value="{{ old('name', $paymentMethod->name) }}" required class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white">
                @error('name')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nomor Rekening / No. HP E-Wallet <span class="text-rose-400">*</span></label>
                <input type="text" name="account_number" value="{{ old('account_number', $paymentMethod->account_number) }}" required class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white font-mono font-bold text-cyan-300">
                @error('account_number')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Pemilik Rekening (Atas Nama) <span class="text-rose-400">*</span></label>
                <input type="text" name="account_holder" value="{{ old('account_holder', $paymentMethod->account_holder) }}" required class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white font-semibold">
                @error('account_holder')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kategori Channel <span class="text-rose-400">*</span></label>
                <select name="group" required class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white">
                    <option value="Transfer Bank Manual" {{ $paymentMethod->group === 'Transfer Bank Manual' ? 'selected' : '' }}>Transfer Bank Manual</option>
                    <option value="E-Wallet" {{ $paymentMethod->group === 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                    <option value="QRIS" {{ $paymentMethod->group === 'QRIS' ? 'selected' : '' }}>QRIS Statis</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Urutan Tampilan</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $paymentMethod->sort_order) }}" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Biaya Admin (Rp)</label>
                <input type="number" name="fee_flat" value="{{ old('fee_flat', $paymentMethod->fee_flat) }}" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white font-semibold">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Gambar QRIS / Barcode (Opsional)</label>
                <input type="file" name="qr_image" accept="image/*" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-gray-300 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-600 file:text-white hover:file:bg-cyan-500">
                @if($paymentMethod->qr_image)
                    <div class="mt-2 flex items-center gap-2">
                        <img src="{{ asset('storage/'.$paymentMethod->qr_image) }}" alt="QR" class="w-12 h-12 object-contain bg-white rounded-lg p-1 border border-gray-700">
                        <span class="text-[10px] text-gray-400">QR code saat ini tersimpan</span>
                    </div>
                @endif
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Petunjuk Transfer Tambahan (Opsional)</label>
                <textarea name="instructions" rows="2" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white">{{ old('instructions', $paymentMethod->instructions) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="flex items-center gap-2 cursor-pointer mt-2">
                    <input type="checkbox" name="is_active" value="1" {{ $paymentMethod->is_active ? 'checked' : '' }} class="rounded bg-gray-800 border-gray-700 text-cyan-600 focus:ring-0">
                    <span class="text-xs font-bold text-white">Status Rekening Aktif (Tampil dan dapat dipilih pelanggan)</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
            <a href="{{ route('admin.payment-methods.index') }}" class="px-5 py-3 rounded-xl bg-gray-800 hover:bg-gray-700 text-xs font-bold text-gray-300 transition-all">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-sm shadow-xl shadow-cyan-600/30 transition-all">
                Simpan Perubahan ➜
            </button>
        </div>

    </form>
</div>
@endsection

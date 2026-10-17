@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.products.index') }}" class="hover:text-indigo-400">Produk</a>
        <span>/</span>
        <span class="text-white">Tambah Produk Baru</span>
    </div>

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Tambah Produk Digital Baru</h1>
            <p class="text-xs text-gray-400 mt-1">Daftarkan game top-up baru atau paket akun premium dengan variasi harga.</p>
        </div>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- 1. Informasi Dasar Produk -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800">
            <h3 class="text-base font-bold text-white mb-6 flex items-center gap-2">
                <span>📦</span> <span>Informasi Umum Produk</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Layanan / Produk <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Valorant, Netflix Premium" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                    @error('name')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kategori <span class="text-rose-400">*</span></label>
                    <select name="category_id" required class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ ucfirst($category->type ?? 'Umum') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Tipe Produk <span class="text-rose-400">*</span></label>
                    <select name="type" id="product_type" required class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                        @foreach($types as $type)
                            <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>
                                {{ $type->icon() }} {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Publisher / Pengembang</label>
                    <input type="text" name="publisher" value="{{ old('publisher') }}" placeholder="Contoh: Moonton, Garena, Riot Games, Netflix" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Foto Sampul / Screenshot Utama</label>
                    <input type="file" name="thumbnail" accept="image/*" class="w-full text-xs text-gray-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer bg-gray-900/80 border border-gray-700 rounded-xl py-1.5 px-3">
                    <span class="text-[10px] text-gray-500 mt-1 block">JPG, PNG, atau WebP (Maks 5MB)</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Galeri Screenshot Isi Akun (Bisa Pilih Banyak)</label>
                    <input type="file" name="gallery[]" multiple accept="image/*" class="w-full text-xs text-gray-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-600 file:text-white hover:file:bg-amber-500 cursor-pointer bg-gray-900/80 border border-gray-700 rounded-xl py-1.5 px-3">
                    <span class="text-[10px] text-gray-500 mt-1 block">Pilih beberapa screenshot (Vault, Skin, Winrate)</span>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Spesifikasi Akun Game (Khusus Akun Game)</label>
                    <textarea name="account_specs_raw" rows="3" placeholder="Rank Saat Ini: Mythic Glory 65★&#10;Total Skin: 285 Skins&#10;Total Hero: 122 Heroes&#10;Winrate: 68.4%&#10;Status Akun: Monsep All Unbind&#10;Kartu CN: Aktif" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 text-xs font-mono text-white focus:outline-none focus:border-indigo-500 transition-colors">{{ old('account_specs_raw') }}</textarea>
                    <span class="text-[10px] text-gray-400 mt-1 block">Tulis format per baris: <code class="text-amber-300">Nama Spesifikasi: Nilai Spesifikasi</code></span>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Deskripsi Produk</label>
                    <textarea name="description" rows="2" placeholder="Deskripsi singkat produk..." class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">{{ old('description') }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Panduan / Instruksi Pengisian</label>
                    <textarea name="instructions" rows="2" placeholder="Contoh: Masukkan User ID dan Zone ID. Contoh User ID: 1234567, Zone: 2024" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">{{ old('instructions') }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. Konfigurasi Input Tujuan (User ID & Zone ID) -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800" id="target-config-card">
            <h3 class="text-base font-bold text-white mb-2 flex items-center gap-2">
                <span>🎯</span> <span>Konfigurasi Form Input Pembeli (Khusus Game)</span>
            </h3>
            <p class="text-xs text-gray-400 mb-6">Atur kolom apa saja yang wajib diisi oleh pembeli saat checkout.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="p-4 rounded-2xl bg-gray-900/50 border border-gray-800">
                    <label class="flex items-center gap-2.5 cursor-pointer mb-3">
                        <input type="checkbox" name="requires_user_id" value="1" checked class="rounded bg-gray-800 border-gray-700 text-indigo-600 focus:ring-0">
                        <span class="text-xs font-bold text-white">Memerlukan User ID / Player ID</span>
                    </label>
                    <label class="block text-[11px] text-gray-400 mb-1">Label Kolom User ID</label>
                    <input type="text" name="user_id_label" value="User ID / ID Pemain" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white">
                </div>

                <div class="p-4 rounded-2xl bg-gray-900/50 border border-gray-800">
                    <label class="flex items-center gap-2.5 cursor-pointer mb-3">
                        <input type="checkbox" name="requires_zone_id" value="1" class="rounded bg-gray-800 border-gray-700 text-indigo-600 focus:ring-0">
                        <span class="text-xs font-bold text-white">Memerlukan Zone ID / Server (Contoh: MLBB)</span>
                    </label>
                    <label class="block text-[11px] text-gray-400 mb-1">Label Kolom Zone ID</label>
                    <input type="text" name="zone_id_label" value="Zone ID (4-5 Digit)" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white">
                </div>
            </div>
        </div>

        <!-- 3. Variasi Paket / Nominal Harga -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>💎</span> <span>Variasi Paket / Nominal Harga</span>
                    </h3>
                    <p class="text-xs text-gray-400">Tambahkan paket diamond atau durasi langganan akun (1 Hari, 3 Hari, 7 Hari, 1 Bulan, dll).</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" onclick="loadAccountDurationPresets()" class="px-3 py-1.5 rounded-xl bg-indigo-600/20 hover:bg-indigo-600/30 text-xs font-bold text-indigo-300 border border-indigo-500/30 transition-all flex items-center gap-1.5" title="Generate otomatis variasi 1 Hari, 3 Hari, 7 Hari, dan 1 Bulan">
                        <span>🍿</span>
                        <span>+ Template Durasi Akun</span>
                    </button>
                    <button type="button" id="add-variant-btn" class="px-3 py-1.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-xs font-bold text-cyan-400 border border-gray-700 transition-all">
                        + Tambah Baris
                    </button>
                </div>
            </div>

            <div id="variants-container" class="space-y-4">
                <!-- Initial Variant 1 -->
                <div class="variant-row p-5 rounded-2xl bg-gray-900/60 border border-gray-800 relative">
                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-gray-400 mb-1">Nama Variasi / Nominal <span class="text-rose-400">*</span></label>
                            <input type="text" name="variants[0][name]" required placeholder="Contoh: 1 Hari Sharing atau 86 Diamonds" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 mb-1">SKU / Kode <span class="text-rose-400">*</span></label>
                            <input type="text" name="variants[0][sku]" required placeholder="VAR-01" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 mb-1">Harga Jual (Rp) <span class="text-rose-400">*</span></label>
                            <input type="number" name="variants[0][price]" required placeholder="5000" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-semibold">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 mb-1">Tipe Stok <span class="text-rose-400">*</span></label>
                            <select name="variants[0][stock_type]" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white">
                                <option value="manual">Terbatas (Akun/Manual)</option>
                                <option value="unlimited">Unlimited (Game)</option>
                                <option value="auto_stock">Auto-Stock (Akun Otomatis)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 mb-1">Jumlah Stok Awal</label>
                            <input type="number" name="variants[0][stock]" min="0" value="10" placeholder="10" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-bold">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-3 rounded-xl bg-gray-800 hover:bg-gray-700 text-xs font-bold text-gray-300 transition-all">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/30 transition-all">
                Simpan Produk Baru ➜
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    let variantIndex = 1;

    function addVariantRow(name = '', sku = '', price = '', stockType = 'manual', stock = 10) {
        const container = document.getElementById('variants-container');
        const randSku = sku || 'VAR-' + (variantIndex + 1);
        const html = `
            <div class="variant-row p-5 rounded-2xl bg-gray-900/60 border border-gray-800 relative mt-4">
                <button type="button" onclick="this.closest('.variant-row').remove()" class="absolute top-3 right-3 text-xs text-rose-400 hover:text-rose-300 font-bold">
                    ✕ Hapus
                </button>
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-semibold text-gray-400 mb-1">Nama Variasi / Nominal <span class="text-rose-400">*</span></label>
                        <input type="text" name="variants[${variantIndex}][name]" value="${name}" required placeholder="Contoh: 3 Hari Sharing" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 mb-1">SKU / Kode <span class="text-rose-400">*</span></label>
                        <input type="text" name="variants[${variantIndex}][sku]" value="${randSku}" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 mb-1">Harga Jual (Rp) <span class="text-rose-400">*</span></label>
                        <input type="number" name="variants[${variantIndex}][price]" value="${price}" required placeholder="12000" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-semibold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 mb-1">Tipe Stok <span class="text-rose-400">*</span></label>
                        <select name="variants[${variantIndex}][stock_type]" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white">
                            <option value="manual" ${stockType === 'manual' ? 'selected' : ''}>Terbatas (Akun/Manual)</option>
                            <option value="unlimited" ${stockType === 'unlimited' ? 'selected' : ''}>Unlimited (Game)</option>
                            <option value="auto_stock" ${stockType === 'auto_stock' ? 'selected' : ''}>Auto-Stock</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 mb-1">Jumlah Stok Awal</label>
                        <input type="number" name="variants[${variantIndex}][stock]" min="0" value="${stock}" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-bold">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        variantIndex++;
    }

    function loadAccountDurationPresets() {
        const container = document.getElementById('variants-container');
        // Clear or set first row
        container.innerHTML = `
            <div class="variant-row p-5 rounded-2xl bg-gray-900/60 border border-gray-800 relative">
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-semibold text-gray-400 mb-1">Nama Variasi / Nominal <span class="text-rose-400">*</span></label>
                        <input type="text" name="variants[0][name]" value="1 Hari - 1 Profil (Sharing)" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 mb-1">SKU / Kode <span class="text-rose-400">*</span></label>
                        <input type="text" name="variants[0][sku]" value="NF-1D-${Date.now().toString().slice(-4)}" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 mb-1">Harga Jual (Rp) <span class="text-rose-400">*</span></label>
                        <input type="number" name="variants[0][price]" value="5000" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-semibold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 mb-1">Tipe Stok <span class="text-rose-400">*</span></label>
                        <select name="variants[0][stock_type]" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white">
                            <option value="manual" selected>Terbatas (Akun/Manual)</option>
                            <option value="unlimited">Unlimited (Game)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 mb-1">Jumlah Stok Awal</label>
                        <input type="number" name="variants[0][stock]" min="0" value="20" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-bold">
                    </div>
                </div>
            </div>
        `;
        variantIndex = 1;
        const now = Date.now().toString().slice(-4);
        addVariantRow('3 Hari - 1 Profil (Sharing)', `NF-3D-${now}`, 12000, 'manual', 15);
        addVariantRow('7 Hari - 1 Profil (Sharing)', `NF-7D-${now}`, 22000, 'manual', 15);
        addVariantRow('1 Bulan - 1 Profil (Sharing)', `NF-1M-${now}`, 35000, 'manual', 10);
        addVariantRow('1 Bulan - Akun Private (5 Profil)', `NF-1MPRV-${now}`, 135000, 'manual', 5);

        // Also change product type to account
        const typeSelect = document.getElementById('product_type');
        if (typeSelect) {
            typeSelect.value = 'account';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const addBtn = document.getElementById('add-variant-btn');
        if (addBtn) {
            addBtn.addEventListener('click', () => addVariantRow());
        }
    });
</script>
@endpush
@endsection

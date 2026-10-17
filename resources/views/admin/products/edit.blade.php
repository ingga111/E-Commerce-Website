@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.products.index') }}" class="hover:text-indigo-400">Produk</a>
        <span>/</span>
        <span class="text-white">Edit {{ $product->name }}</span>
    </div>

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Edit Produk: {{ $product->name }}</h1>
            <p class="text-xs text-gray-400 mt-1">Ubah informasi layanan, tambah variasi paket harga, dan tambah stok akun digital.</p>
        </div>
        <a href="{{ route('products.checkout', $product->slug) }}" target="_blank" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-xs font-bold text-cyan-400 border border-gray-700 transition-all">
            Lihat Halaman Checkout ↗
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Form Update Produk -->
        <div class="lg:col-span-2 space-y-8">
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800 space-y-5">
                @csrf
                @method('PUT')

                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span>⚙️</span> <span>Pengaturan Produk</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Produk</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kategori</label>
                        <select name="category_id" required class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Tipe</label>
                        <select name="type" required class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white">
                            @foreach($types as $type)
                                <option value="{{ $type->value }}" {{ $product->type->value == $type->value ? 'selected' : '' }}>
                                    {{ $type->icon() }} {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Publisher</label>
                        <input type="text" name="publisher" value="{{ old('publisher', $product->publisher) }}" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white">
                    </div>

                    <!-- Images & Gallery -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Ubah Foto Sampul / Screenshot Utama</label>
                        @if($product->getThumbnailUrl())
                            <div class="mb-2 w-32 aspect-video rounded-lg overflow-hidden border border-gray-700">
                                <img src="{{ $product->getThumbnailUrl() }}" alt="Current thumbnail" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <input type="file" name="thumbnail" accept="image/*" class="w-full text-xs text-gray-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white cursor-pointer bg-gray-900/80 border border-gray-700 rounded-xl py-1 px-2">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Tambah Foto Galeri Screenshot</label>
                        @if(!empty($product->getGalleryUrls()))
                            <div class="flex items-center gap-1.5 mb-2 overflow-x-auto pb-1">
                                @foreach($product->getGalleryUrls() as $gUrl)
                                    <div class="w-14 aspect-video rounded border border-gray-700 overflow-hidden flex-shrink-0">
                                        <img src="{{ $gUrl }}" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <input type="file" name="gallery[]" multiple accept="image/*" class="w-full text-xs text-gray-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-600 file:text-white cursor-pointer bg-gray-900/80 border border-gray-700 rounded-xl py-1 px-2">
                    </div>

                    @php
                        $specsText = '';
                        if (is_array($product->account_specs)) {
                            foreach ($product->account_specs as $k => $v) {
                                $specsText .= "$k: $v\n";
                            }
                        }
                    @endphp
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Spesifikasi Akun Game (Format: <code class="text-amber-300">Key: Value</code>)</label>
                        <textarea name="account_specs_raw" rows="3" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2 text-xs font-mono text-white">{{ old('account_specs_raw', trim($specsText)) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Deskripsi Produk</label>
                        <textarea name="description" rows="2" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2 text-xs text-white">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Panduan / Instruksi Pengisian</label>
                        <textarea name="instructions" rows="2" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2 text-xs text-white">{{ old('instructions', $product->instructions) }}</textarea>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer mt-2">
                            <input type="checkbox" name="requires_user_id" value="1" {{ $product->requires_user_id ? 'checked' : '' }} class="rounded bg-gray-800 border-gray-700 text-indigo-600 focus:ring-0">
                            <span class="text-xs font-bold text-white">Wajib User ID</span>
                        </label>
                        <input type="text" name="user_id_label" value="{{ $product->user_id_label }}" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-1.5 text-xs text-white mt-1">
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer mt-2">
                            <input type="checkbox" name="requires_zone_id" value="1" {{ $product->requires_zone_id ? 'checked' : '' }} class="rounded bg-gray-800 border-gray-700 text-indigo-600 focus:ring-0">
                            <span class="text-xs font-bold text-white">Wajib Zone ID (Server)</span>
                        </label>
                        <input type="text" name="zone_id_label" value="{{ $product->zone_id_label }}" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-1.5 text-xs text-white mt-1">
                    </div>
                </div>

                <div class="pt-4 flex justify-between items-center border-t border-gray-800">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded bg-gray-800 border-gray-700 text-indigo-600 focus:ring-0">
                        <span class="text-xs font-semibold text-white">Status Produk Aktif</span>
                    </label>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition-all">
                        Perbarui Informasi
                    </button>
                </div>
            </form>

            <!-- Daftar Variasi & Tambah Stok Kredensial -->
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>💎</span> <span>Daftar Variasi & Stok Kredensial</span>
                    </h3>
                    <span class="text-xs text-gray-400">Total: {{ $product->variants->count() }} Variasi</span>
                </div>

                <div class="space-y-4">
                    @forelse($product->variants as $variant)
                        <div class="p-4 rounded-2xl bg-gray-900/60 border border-gray-800">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-white text-sm">{{ $variant->name }}</span>
                                    <span class="text-[11px] text-gray-400 font-mono">SKU: {{ $variant->sku }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-cyan-400 text-sm">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                    <form action="{{ route('admin.variants.destroy', $variant->id) }}" method="POST" onsubmit="return confirm('Hapus variasi {{ $variant->name }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[11px] text-rose-400 hover:text-rose-300 font-semibold px-2 py-0.5 rounded bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition-all" title="Hapus variasi ini">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-t border-gray-800 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold {{ $variant->stock_type === 'unlimited' ? 'text-gray-400' : ($variant->stock > 0 ? 'text-emerald-400' : 'text-rose-400') }}">
                                        @if($variant->stock_type === 'unlimited')
                                            Stok: Unlimited
                                        @else
                                            Stok Tersedia: <strong class="text-sm font-bold {{ $variant->stock > 0 ? 'text-emerald-300' : 'text-rose-400' }}">{{ $variant->stock }}</strong> Akun
                                        @endif
                                    </span>
                                </div>

                                @if($variant->stock_type !== 'unlimited')
                                    <!-- Form Cepat Tambah Stok (Tanpa Ribet Input Akun) -->
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('admin.variants.add-stock', $variant->id) }}" method="POST" class="flex items-center gap-1.5">
                                            @csrf
                                            <input type="number" name="stock_to_add" value="5" min="1" class="w-16 bg-gray-950 border border-gray-700 rounded-lg px-2 py-1 text-xs text-emerald-400 text-center font-bold" title="Jumlah stok yang ingin ditambahkan">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600/30 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/40 text-xs font-bold transition-all">
                                                + Tambah Stok
                                            </button>
                                        </form>

                                        <details class="relative">
                                            <summary class="cursor-pointer text-[11px] text-gray-400 hover:text-indigo-300 px-2 py-1 rounded bg-gray-800">
                                                Atur Manual
                                            </summary>
                                            <form action="{{ route('admin.variants.add-stock', $variant->id) }}" method="POST" class="absolute right-0 top-8 z-10 p-3 rounded-xl bg-gray-950 border border-gray-700 shadow-xl w-48 space-y-2">
                                                @csrf
                                                <label class="block text-[10px] text-gray-400 font-semibold">Setel Total Stok Langsung:</label>
                                                <input type="number" name="stock" value="{{ $variant->stock }}" min="0" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-1.5 text-xs text-white text-center font-bold">
                                                <button type="submit" class="w-full py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs">
                                                    Simpan Perubahan
                                                </button>
                                            </form>
                                        </details>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500">Belum ada variasi pada produk ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Tambah Variasi Baru Langsung -->
        <div>
            <form action="{{ route('admin.products.variants.store', $product->id) }}" method="POST" class="glass-card rounded-3xl p-6 border border-gray-800 space-y-4 sticky top-24">
                @csrf
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>+</span> <span>Tambah Variasi Baru</span>
                    </h3>
                </div>

                @if($product->isAccount())
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block mb-1.5">Preset Durasi Cepat:</span>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="fillVariantPreset('1 Hari - 1 Profil (Sharing)', 'NF-1D', 5000)" class="text-[10px] px-2 py-1 rounded-lg bg-gray-800 hover:bg-indigo-600 text-gray-300 hover:text-white transition-all">+ 1 Hari</button>
                            <button type="button" onclick="fillVariantPreset('3 Hari - 1 Profil (Sharing)', 'NF-3D', 12000)" class="text-[10px] px-2 py-1 rounded-lg bg-gray-800 hover:bg-indigo-600 text-gray-300 hover:text-white transition-all">+ 3 Hari</button>
                            <button type="button" onclick="fillVariantPreset('7 Hari - 1 Profil (Sharing)', 'NF-7D', 22000)" class="text-[10px] px-2 py-1 rounded-lg bg-gray-800 hover:bg-indigo-600 text-gray-300 hover:text-white transition-all">+ 7 Hari</button>
                            <button type="button" onclick="fillVariantPreset('1 Bulan - 1 Profil (Sharing)', 'NF-1M', 35000)" class="text-[10px] px-2 py-1 rounded-lg bg-gray-800 hover:bg-indigo-600 text-gray-300 hover:text-white transition-all">+ 1 Bulan Sharing</button>
                            <button type="button" onclick="fillVariantPreset('1 Bulan - Akun Private (5 Profil)', 'NF-1MPRV', 135000)" class="text-[10px] px-2 py-1 rounded-lg bg-gray-800 hover:bg-indigo-600 text-gray-300 hover:text-white transition-all">+ 1 Bulan Private</button>
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 mb-1">Nama Variasi / Nominal</label>
                    <input type="text" name="name" id="new_variant_name" required placeholder="Contoh: 1 Hari Sharing atau 1 Bulan 4K" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 mb-1">SKU / Kode Variasi</label>
                    <input type="text" name="sku" id="new_variant_sku" required placeholder="Contoh: NF-1D-01" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 mb-1">Harga Jual (Rp)</label>
                    <input type="number" name="price" id="new_variant_price" required placeholder="5000" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 mb-1">Tipe Stok</label>
                    <select name="stock_type" id="new_variant_stock_type" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white">
                        <option value="manual" {{ $product->isAccount() ? 'selected' : '' }}>Terbatas / Stok (Netflix, Spotify, Akun)</option>
                        <option value="unlimited" {{ $product->isGame() ? 'selected' : '' }}>Unlimited (Game Top-Up)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 mb-1">Jumlah Stok Awal</label>
                    <input type="number" name="stock" id="new_variant_stock" value="10" min="0" placeholder="10" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-bold">
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-600/20 transition-all">
                    + Tambahkan Paket
                </button>
            </form>
        </div>

    </div>

</div>

@push('scripts')
<script>
    function fillVariantPreset(name, prefix, price) {
        const inputName = document.getElementById('new_variant_name');
        const inputSku = document.getElementById('new_variant_sku');
        const inputPrice = document.getElementById('new_variant_price');
        const inputStockType = document.getElementById('new_variant_stock_type');

        if (inputName) inputName.value = name;
        if (inputSku) inputSku.value = `${prefix}-${Date.now().toString().slice(-4)}`;
        if (inputPrice) inputPrice.value = price;
        if (inputStockType) inputStockType.value = 'manual';
    }
</script>
@endpush
@endsection

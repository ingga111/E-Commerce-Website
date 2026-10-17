@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400">Dashboard</a>
                <span>/</span>
                <span class="text-white">Kelola Banner Promo</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Kelola Banner Slide & Promo</h1>
            <p class="text-xs text-gray-400 mt-1">Upload banner promo game, event diskon, dan atur tampilan carousel beranda ala solustion411.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold border border-gray-700 transition-all flex items-center gap-1.5">
                <span>⚡ Dashboard Admin</span>
            </a>
            <button type="button" onclick="document.getElementById('modal-create-banner').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold shadow-lg shadow-cyan-600/20 transition-all flex items-center gap-1.5">
                <span>+ Upload / Tambah Banner</span>
            </button>
        </div>
    </div>

    <!-- Info Box -->
    <div class="glass-card rounded-2xl p-5 border border-cyan-500/20 bg-cyan-950/20 mb-8 flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-cyan-600/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-xl flex-shrink-0">
            🖼️
        </div>
        <div class="text-xs">
            <h4 class="font-bold text-white text-sm">Banner Carousel Otomatis di Beranda</h4>
            <p class="text-gray-300 mt-1 leading-relaxed">
                Admin memiliki kontrol penuh atas banner slide promo di beranda. Anda dapat mengunggah gambar banner custom (rekomendasi rasio 16:9 atau lebar 1200x500px), atau menggunakan styling gaming gradien dengan badge diskon. Banner yang berstatus <strong>Aktif</strong> akan otomatis tampil bergantian di slide beranda pembeli.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-2">
            <span>✓</span> {{ session('success') }}
        </div>
    @endif

    <!-- Banners Grid / List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($banners as $banner)
            <div class="glass-card rounded-3xl overflow-hidden border border-gray-800 flex flex-col justify-between group hover:border-cyan-500/40 transition-all">
                <!-- Banner Preview -->
                <div class="relative w-full aspect-[16/9] bg-gradient-to-br {{ $banner->gradient ?? 'from-slate-900 to-indigo-950' }} overflow-hidden flex items-center justify-center p-6 text-center">
                    @if($banner->getImageUrl())
                        <img src="{{ $banner->getImageUrl() }}" alt="{{ $banner->title }}" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                    @endif
                    <div class="relative z-10 max-w-full">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-400 border border-amber-500/30 mb-2">
                            {{ $banner->badge_text ?? 'PROMO' }}
                        </span>
                        <h4 class="text-sm font-extrabold text-white line-clamp-2 leading-tight drop-shadow-md">{{ $banner->title }}</h4>
                        @if($banner->subtitle)
                            <p class="text-[11px] text-gray-300 mt-1 line-clamp-1 drop-shadow">{{ $banner->subtitle }}</p>
                        @endif
                    </div>

                    <div class="absolute top-3 right-3 z-20 flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $banner->is_active ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-gray-800 text-gray-400 border border-gray-700' }}">
                            {{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>

                <!-- Info & Actions -->
                <div class="p-5 flex-1 flex flex-col justify-between bg-gray-900/40">
                    <div>
                        <div class="flex items-center justify-between text-[11px] text-gray-400 mb-2">
                            <span>Urutan: <strong class="text-white">#{{ $banner->sort_order }}</strong></span>
                            <span>Target: <span class="font-mono text-cyan-400 truncate max-w-[150px] inline-block">{{ $banner->target_url ?? '-' }}</span></span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-800 flex items-center justify-between gap-2">
                        <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-semibold {{ $banner->is_active ? 'text-amber-400 hover:text-amber-300' : 'text-emerald-400 hover:text-emerald-300' }} transition-colors">
                                {{ $banner->is_active ? 'Matikan' : 'Aktifkan' }}
                            </button>
                        </form>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openEditModal({{ json_encode($banner) }})" class="px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold transition-all">
                                ✏️ Edit
                            </button>
                            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-semibold transition-all">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full glass-card p-12 rounded-3xl text-center border border-gray-800">
                <span class="text-4xl block mb-3">🖼️</span>
                <h3 class="text-base font-bold text-white">Belum Ada Banner Promo</h3>
                <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Klik tombol di atas untuk menambah slide banner promo baru untuk ditampilkan di beranda.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Create Banner -->
<div id="modal-create-banner" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800 max-w-xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-gray-800 mb-6">
            <h3 class="text-lg font-bold text-white">Tambah Banner Promo Baru</h3>
            <button type="button" onclick="document.getElementById('modal-create-banner').classList.add('hidden')" class="text-gray-400 hover:text-white text-lg">✕</button>
        </div>

        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Judul Banner <span class="text-rose-400">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: PROMO SPESIAL TOP UP 24 JAM" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Subjudul / Deskripsi Singkat</label>
                <input type="text" name="subtitle" placeholder="Contoh: Diskon hingga 30% Diamond MLBB & Free Fire!" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Badge Teks</label>
                    <input type="text" name="badge_text" value="PROMO SPESIAL" placeholder="Contoh: ⚡ FLASH SALE" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Link Tujuan (Klik Banner)</label>
                    <input type="text" name="target_url" value="#games" placeholder="Contoh: /product/mobile-legends atau #games" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Upload Gambar Banner (Opsional)</label>
                <input type="file" name="image" accept="image/*" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-gray-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-600 file:text-white hover:file:bg-cyan-500">
                <span class="text-[10px] text-gray-400 mt-1 block">Format: JPG, PNG, WEBP (Maksimal 4MB). Jika kosong, background gradien gaming akan otomatis digunakan.</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Gradien Warna Background</label>
                    <select name="gradient" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                        <option value="from-blue-900 via-indigo-950 to-slate-950">Biru - Indigo (MLBB Style)</option>
                        <option value="from-amber-900 via-orange-950 to-slate-950">Oranye - Api (Free Fire Style)</option>
                        <option value="from-purple-900 via-slate-950 to-emerald-950">Ungu - Hijau (Sultan Style)</option>
                        <option value="from-rose-900 via-pink-950 to-slate-950">Merah Crimson - Hitam</option>
                        <option value="from-cyan-900 via-teal-950 to-slate-950">Cyan Neon - Gelap</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nomor Urutan Tampil</label>
                    <input type="number" name="sort_order" value="1" min="0" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="create-is-active" value="1" checked class="w-4 h-4 rounded text-cyan-500 bg-gray-900 border-gray-700 focus:ring-0">
                <label for="create-is-active" class="text-xs text-gray-300 select-none">Aktifkan langsung di slide beranda</label>
            </div>

            <div class="pt-4 border-t border-gray-800 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-create-banner').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-gray-800 text-gray-300 text-xs font-semibold hover:bg-gray-700">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold shadow-lg shadow-cyan-600/30">Simpan Banner</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Banner -->
<div id="modal-edit-banner" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800 max-w-xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-gray-800 mb-6">
            <h3 class="text-lg font-bold text-white">Edit Banner Promo</h3>
            <button type="button" onclick="document.getElementById('modal-edit-banner').classList.add('hidden')" class="text-gray-400 hover:text-white text-lg">✕</button>
        </div>

        <form id="edit-banner-form" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Judul Banner <span class="text-rose-400">*</span></label>
                <input type="text" name="title" id="edit-title" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Subjudul / Deskripsi Singkat</label>
                <input type="text" name="subtitle" id="edit-subtitle" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Badge Teks</label>
                    <input type="text" name="badge_text" id="edit-badge" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Link Tujuan</label>
                    <input type="text" name="target_url" id="edit-target-url" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Ganti Gambar Banner (Biarkan kosong jika tidak ingin ganti)</label>
                <input type="file" name="image" accept="image/*" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-gray-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-600 file:text-white hover:file:bg-cyan-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Gradien Warna</label>
                    <select name="gradient" id="edit-gradient" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                        <option value="from-blue-900 via-indigo-950 to-slate-950">Biru - Indigo (MLBB Style)</option>
                        <option value="from-amber-900 via-orange-950 to-slate-950">Oranye - Api (Free Fire Style)</option>
                        <option value="from-purple-900 via-slate-950 to-emerald-950">Ungu - Hijau (Sultan Style)</option>
                        <option value="from-rose-900 via-pink-950 to-slate-950">Merah Crimson - Hitam</option>
                        <option value="from-cyan-900 via-teal-950 to-slate-950">Cyan Neon - Gelap</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nomor Urutan</label>
                    <input type="number" name="sort_order" id="edit-sort-order" min="0" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="edit-is-active" value="1" class="w-4 h-4 rounded text-cyan-500 bg-gray-900 border-gray-700 focus:ring-0">
                <label for="edit-is-active" class="text-xs text-gray-300 select-none">Aktifkan di slide beranda</label>
            </div>

            <div class="pt-4 border-t border-gray-800 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-edit-banner').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-gray-800 text-gray-300 text-xs font-semibold hover:bg-gray-700">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold shadow-lg shadow-cyan-600/30">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(banner) {
        const form = document.getElementById('edit-banner-form');
        form.action = '/admin/banners/' + banner.id;
        document.getElementById('edit-title').value = banner.title || '';
        document.getElementById('edit-subtitle').value = banner.subtitle || '';
        document.getElementById('edit-badge').value = banner.badge_text || 'PROMO SPESIAL';
        document.getElementById('edit-target-url').value = banner.target_url || '#games';
        document.getElementById('edit-gradient').value = banner.gradient || 'from-blue-900 via-indigo-950 to-slate-950';
        document.getElementById('edit-sort-order').value = banner.sort_order || 0;
        document.getElementById('edit-is-active').checked = banner.is_active;

        document.getElementById('modal-edit-banner').classList.remove('hidden');
    }
</script>
@endsection

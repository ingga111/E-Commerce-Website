@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-400">Dashboard</a>
                <span>/</span>
                <span class="text-white">Kelola Flash Sale Event</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-2.5">
                <span>🔥</span> Kelola Flash Sale Event Hari Ini
            </h1>
            <p class="text-xs text-gray-400 mt-1">Atur banner ticker countdown promo Flash Sale yang tampil di halaman beranda toko secara dinamis.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold border border-gray-700 transition-all flex items-center gap-1.5">
                <span>👁️ Lihat Beranda</span>
            </a>
            <button type="button" onclick="document.getElementById('modal-create-flashsale').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white text-xs font-bold shadow-lg shadow-amber-500/20 transition-all flex items-center gap-1.5">
                <span>+ Buat Event Baru</span>
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

    <!-- 1. LIVE PREVIEW BANNER -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 flex items-center gap-2">
                <span>👀</span> Tampilan Langsung di Beranda (Live Preview)
            </h3>
            <span class="text-[11px] font-semibold {{ $currentFlashSale ? 'text-emerald-400' : 'text-gray-500' }}">
                ● {{ $currentFlashSale ? 'Sedang Tayang di Beranda' : 'Tidak Ada Event Aktif' }}
            </span>
        </div>

        @if($currentFlashSale)
            <div class="glass-card rounded-2xl p-4 sm:p-5 border border-amber-500/30 bg-gradient-to-r from-amber-950/20 via-slate-900/60 to-orange-950/20 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl glow-amber">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 text-2xl font-bold flex-shrink-0">
                        🔥
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm sm:text-base font-extrabold text-white">{{ $currentFlashSale->title }}</h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-400 text-[10px] font-bold border border-rose-500/30 animate-pulse">
                                {{ $currentFlashSale->badge_text }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $currentFlashSale->subtitle }}</p>
                    </div>
                </div>

                @if($currentFlashSale->end_time)
                    <div class="flex items-center gap-2 text-xs font-mono font-bold text-amber-400 bg-black/60 px-4 py-2 rounded-xl border border-amber-500/30 flex-shrink-0" id="preview-timer" data-endtime="{{ $currentFlashSale->end_time->toISOString() }}">
                        <span class="text-[11px] text-gray-400 font-sans">Berakhir dalam:</span>
                        <span class="px-2 py-1 bg-amber-500/20 rounded text-amber-300" id="p-hours">00</span>:
                        <span class="px-2 py-1 bg-amber-500/20 rounded text-amber-300" id="p-minutes">00</span>:
                        <span class="px-2 py-1 bg-amber-500/20 rounded text-amber-300" id="p-seconds">00</span>
                    </div>
                @endif
            </div>
        @else
            <div class="p-8 rounded-2xl border border-dashed border-gray-800 text-center">
                <p class="text-sm text-gray-400">Saat ini tidak ada Flash Sale yang aktif. Aktifkan event di bawah atau buat event baru agar tampil di beranda.</p>
            </div>
        @endif
    </div>

    <!-- 2. DAFTAR EVENT & PENGATURAN -->
    <div class="grid grid-cols-1 gap-6">
        <div class="glass-card rounded-3xl p-6 border border-gray-800">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-800">
                <div>
                    <h3 class="text-base font-bold text-white">Daftar Event Flash Sale</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Kelola judul, badge diskon, dan waktu hitung mundur countdown.</p>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($flashSales as $fs)
                    <div class="p-5 rounded-2xl bg-gray-900/60 border {{ $fs->isRunning() ? 'border-amber-500/40 glow-amber' : 'border-gray-800' }} flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl {{ $fs->is_active ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-gray-800 text-gray-500' }} flex items-center justify-center text-2xl flex-shrink-0">
                                🔥
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h4 class="font-extrabold text-sm text-white">{{ $fs->title }}</h4>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                        {{ $fs->badge_text }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $fs->isRunning() ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($fs->is_active ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-gray-800 text-gray-400 border border-gray-700') }}">
                                        {{ $fs->isRunning() ? '⚡ Sedang Berjalan' : ($fs->is_active ? '⏱️ Waktu Habis' : 'Nonaktif') }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400">{{ $fs->subtitle ?? 'Tidak ada deskripsi.' }}</p>
                                <div class="flex flex-wrap items-center gap-4 mt-2.5 text-[11px] text-gray-400">
                                    <span>Target: <code class="text-cyan-400 font-mono">{{ $fs->target_url ?? '#group-game' }}</code></span>
                                    @if($fs->end_time)
                                        <span>Berakhir: <strong class="text-white">{{ $fs->end_time->format('d M Y, H:i') }} WIB</strong> ({{ $fs->end_time->diffForHumans() }})</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2.5 self-end md:self-center">
                            <!-- Toggle Button -->
                            <form action="{{ route('admin.flash-sales.toggle', $fs) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $fs->is_active ? 'bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 border border-emerald-500/30' }}">
                                    {{ $fs->is_active ? 'Matikan' : 'Aktifkan' }}
                                </button>
                            </form>

                            <!-- Edit Button -->
                            <button type="button" onclick="openEditFlashSaleModal({{ json_encode($fs) }})" class="px-3 py-1.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-bold border border-gray-700 transition-all">
                                ✏️ Edit
                            </button>

                            <!-- Delete Button -->
                            <form action="{{ route('admin.flash-sales.destroy', $fs) }}" method="POST" onsubmit="return confirm('Hapus event flash sale ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-bold border border-rose-500/20 transition-all">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-500 text-center py-6">Belum ada event flash sale.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- MODAL: BUAT EVENT FLASH SALE BARU -->
<div id="modal-create-flashsale" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-amber-500/30 max-w-lg w-full shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between pb-4 border-b border-gray-800 mb-6">
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">🔥</span>
                <h3 class="text-lg font-black text-white">Buat Event Flash Sale Baru</h3>
            </div>
            <button type="button" onclick="document.getElementById('modal-create-flashsale').classList.add('hidden')" class="w-8 h-8 rounded-full bg-gray-800 text-gray-400 hover:text-white flex items-center justify-center">✕</button>
        </div>

        <form action="{{ route('admin.flash-sales.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Judul Event <span class="text-rose-400">*</span></label>
                <input type="text" name="title" value="FLASH SALE EVENT HARI INI" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Badge Teks <span class="text-rose-400">*</span></label>
                    <input type="text" name="badge_text" value="HEMAT 30%" required placeholder="Contoh: HEMAT 30%" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Diskon Persen (%)</label>
                    <input type="number" name="discount_percentage" value="30" min="1" max="100" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Deskripsi / Subtitle</label>
                <textarea name="subtitle" rows="2" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">Dapatkan harga diamond & pass termurah sebelum periode berakhir.</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Waktu Berakhir (Countdown Timer) <span class="text-rose-400">*</span></label>
                <input type="datetime-local" name="end_time" id="create-end-time" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-[10px] text-gray-400">Preset Cepat:</span>
                    <button type="button" onclick="setQuickTime('create-end-time', 6)" class="px-2 py-1 rounded bg-gray-800 hover:bg-gray-700 text-[10px] text-amber-300 font-semibold">+6 Jam</button>
                    <button type="button" onclick="setQuickTime('create-end-time', 12)" class="px-2 py-1 rounded bg-gray-800 hover:bg-gray-700 text-[10px] text-amber-300 font-semibold">+12 Jam</button>
                    <button type="button" onclick="setQuickTime('create-end-time', 24)" class="px-2 py-1 rounded bg-gray-800 hover:bg-gray-700 text-[10px] text-amber-300 font-semibold">+24 Jam</button>
                    <button type="button" onclick="setQuickTime('create-end-time', 72)" class="px-2 py-1 rounded bg-gray-800 hover:bg-gray-700 text-[10px] text-amber-300 font-semibold">+3 Hari</button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Link Tujuan Saat Diklik</label>
                <input type="text" name="target_url" value="#group-game" placeholder="#group-game atau /product/slug" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-amber-500 bg-gray-900 border-gray-700 focus:ring-0">
                    <span class="text-xs font-semibold text-gray-200">Aktifkan langsung di beranda</span>
                </label>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="document.getElementById('modal-create-flashsale').classList.add('hidden')" class="flex-1 py-3 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold text-xs transition-all">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white font-extrabold text-xs shadow-lg shadow-amber-500/20 transition-all">
                    Simpan Event 🔥
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDIT EVENT FLASH SALE -->
<div id="modal-edit-flashsale" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-amber-500/30 max-w-lg w-full shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between pb-4 border-b border-gray-800 mb-6">
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">✏️</span>
                <h3 class="text-lg font-black text-white">Edit Event Flash Sale</h3>
            </div>
            <button type="button" onclick="document.getElementById('modal-edit-flashsale').classList.add('hidden')" class="w-8 h-8 rounded-full bg-gray-800 text-gray-400 hover:text-white flex items-center justify-center">✕</button>
        </div>

        <form id="edit-flashsale-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Judul Event <span class="text-rose-400">*</span></label>
                <input type="text" name="title" id="edit-title" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Badge Teks <span class="text-rose-400">*</span></label>
                    <input type="text" name="badge_text" id="edit-badge-text" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Diskon Persen (%)</label>
                    <input type="number" name="discount_percentage" id="edit-discount-percentage" min="1" max="100" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Deskripsi / Subtitle</label>
                <textarea name="subtitle" id="edit-subtitle" rows="2" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Waktu Berakhir (Countdown Timer)</label>
                <input type="datetime-local" name="end_time" id="edit-end-time" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-[10px] text-gray-400">Preset Cepat:</span>
                    <button type="button" onclick="setQuickTime('edit-end-time', 6)" class="px-2 py-1 rounded bg-gray-800 hover:bg-gray-700 text-[10px] text-amber-300 font-semibold">+6 Jam</button>
                    <button type="button" onclick="setQuickTime('edit-end-time', 12)" class="px-2 py-1 rounded bg-gray-800 hover:bg-gray-700 text-[10px] text-amber-300 font-semibold">+12 Jam</button>
                    <button type="button" onclick="setQuickTime('edit-end-time', 24)" class="px-2 py-1 rounded bg-gray-800 hover:bg-gray-700 text-[10px] text-amber-300 font-semibold">+24 Jam</button>
                    <button type="button" onclick="setQuickTime('edit-end-time', 72)" class="px-2 py-1 rounded bg-gray-800 hover:bg-gray-700 text-[10px] text-amber-300 font-semibold">+3 Hari</button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Link Tujuan Saat Diklik</label>
                <input type="text" name="target_url" id="edit-target-url" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit-is-active" value="1" class="w-4 h-4 rounded text-amber-500 bg-gray-900 border-gray-700 focus:ring-0">
                    <span class="text-xs font-semibold text-gray-200">Aktifkan di beranda</span>
                </label>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="document.getElementById('modal-edit-flashsale').classList.add('hidden')" class="flex-1 py-3 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold text-xs transition-all">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white font-extrabold text-xs shadow-lg shadow-amber-500/20 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function setQuickTime(inputId, hoursToAdd) {
        const d = new Date();
        d.setHours(d.getHours() + hoursToAdd);
        // Format to YYYY-MM-DDTHH:MM
        const offset = d.getTimezoneOffset() * 60000;
        const localISOTime = (new Date(d.getTime() - offset)).toISOString().slice(0, 16);
        document.getElementById(inputId).value = localISOTime;
    }

    // Set default initial create time if empty
    const createEndTimeEl = document.getElementById('create-end-time');
    if (createEndTimeEl && !createEndTimeEl.value) {
        setQuickTime('create-end-time', 12);
    }

    function openEditFlashSaleModal(data) {
        document.getElementById('edit-flashsale-form').action = `/admin/flash-sales/${data.id}`;
        document.getElementById('edit-title').value = data.title || '';
        document.getElementById('edit-badge-text').value = data.badge_text || '';
        document.getElementById('edit-discount-percentage').value = data.discount_percentage || '';
        document.getElementById('edit-subtitle').value = data.subtitle || '';
        document.getElementById('edit-target-url').value = data.target_url || '#group-game';
        document.getElementById('edit-is-active').checked = !!data.is_active;

        if (data.end_time) {
            const date = new Date(data.end_time);
            const offset = date.getTimezoneOffset() * 60000;
            const localISOTime = (new Date(date.getTime() - offset)).toISOString().slice(0, 16);
            document.getElementById('edit-end-time').value = localISOTime;
        } else {
            document.getElementById('edit-end-time').value = '';
        }

        document.getElementById('modal-edit-flashsale').classList.remove('hidden');
    }

    // Live preview countdown
    const prevTimerEl = document.getElementById('preview-timer');
    if (prevTimerEl && prevTimerEl.dataset.endtime) {
        const targetTime = new Date(prevTimerEl.dataset.endtime).getTime();
        function updatePrevTimer() {
            const now = new Date().getTime();
            const diff = targetTime - now;
            if (diff <= 0) {
                document.getElementById('p-hours').textContent = '00';
                document.getElementById('p-minutes').textContent = '00';
                document.getElementById('p-seconds').textContent = '00';
                return;
            }
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById('p-hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('p-minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('p-seconds').textContent = String(seconds).padStart(2, '0');
        }
        updatePrevTimer();
        setInterval(updatePrevTimer, 1000);
    }
</script>
@endpush
@endsection

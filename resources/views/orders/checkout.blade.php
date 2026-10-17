@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 pb-32">
    
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-cyan-400 transition-colors">Beranda</a>
        <span>/</span>
        <a href="{{ route('home') }}#games" class="hover:text-cyan-400 transition-colors">Top-Up Game</a>
        <span>/</span>
        <span class="text-cyan-400 font-bold">{{ $product->name }}</span>
    </div>

    @php
        $galleryUrls = $product->getGalleryUrls();
        $primaryImg = $product->getThumbnailUrl();
        $accountSpecs = $product->account_specs ?? [];
    @endphp

    <!-- 1. PRODUCT HEADER & INSTRUCTIONS CARD (solustion411 Style) -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800 mb-8 relative overflow-hidden">
        <!-- Ambient Background Glow -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 pb-6 border-b border-gray-800/80">
            <div class="flex items-start sm:items-center gap-4">
                <!-- Game Icon / Thumbnail -->
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-cyan-600 via-blue-600 to-indigo-700 p-0.5 flex-shrink-0 shadow-lg shadow-cyan-600/20">
                    <div class="w-full h-full bg-gray-950 rounded-[14px] overflow-hidden flex items-center justify-center">
                        @if($primaryImg)
                            <img src="{{ $primaryImg }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl sm:text-4xl">{{ $product->type->icon() }}</span>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                            {{ $product->publisher ?? 'solustion411' }} • {{ $product->type->label() }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center gap-1">
                            ⚡ Proses Otomatis 1-3 Detik
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                            🛡️ 100% Legal & Aman
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ $product->name }}</h1>
                    <p class="text-xs text-gray-400 mt-1">
                        @if($product->isAccount())
                            Akun resmi premium &amp; bergaransi. Kredensial akun dikirim otomatis setelah pembayaran!
                        @elseif($product->isGameAccount())
                            Jual beli akun bergaransi anti hackback dengan kredensial langsung dan aman!
                        @elseif($product->isJoki())
                            Layanan joki rank profesional, cepat, rapi dan terpercaya 24 jam nonstop!
                        @else
                            Layanan top up resmi 24 jam nonstop. Cukup masukkan User ID tanpa perlu login akun game!
                        @endif
                    </p>
                </div>
            </div>

            <div class="text-left md:text-right flex-shrink-0">
                <span class="text-[11px] text-gray-400 block font-medium">Harga Mulai Dari</span>
                <span class="text-xl sm:text-2xl font-black text-cyan-400">
                    Rp {{ number_format($product->variants->min('price') ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        @if($product->isGameAccount() || !empty($galleryUrls))
            <!-- Interactive Screenshot Gallery & Specs Showcase -->
            <div class="mt-6 pt-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>📸</span> {{ $product->isGameAccount() ? 'Bukti Screenshot & Isi Akun Game' : 'Bukti Preview & Informasi Layanan' }}
                    </h2>
                    <span class="text-xs text-gray-400 hidden sm:inline">
                        Klik gambar untuk memperbesar layar penuh
                    </span>
                </div>

                <!-- Main Screenshot Preview -->
                <div class="relative w-full aspect-video sm:aspect-[16/9] max-h-[460px] rounded-2xl overflow-hidden bg-slate-950 border border-gray-800 group cursor-pointer shadow-2xl" id="gallery-main-container">
                    <img id="gallery-main-image" src="{{ $galleryUrls[0] ?? $primaryImg }}" alt="{{ $product->name }}" class="w-full h-full object-contain sm:object-cover transition-transform duration-300 group-hover:scale-[1.02]">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-950/80 via-transparent to-transparent pointer-events-none"></div>
                    <button type="button" id="btn-zoom-overlay" class="absolute bottom-4 right-4 px-3.5 py-2 rounded-xl bg-black/80 hover:bg-black text-white text-xs font-bold backdrop-blur-md border border-gray-700/80 flex items-center gap-2 shadow-lg transition-all">
                        <span>🔍</span>
                        <span>Perbesar Layar Penuh</span>
                    </button>
                    <div class="absolute top-4 left-4 pointer-events-none">
                        <span class="px-3 py-1 rounded-xl bg-black/70 backdrop-blur-md text-amber-300 text-xs font-semibold border border-amber-500/30">
                            {{ $product->isGameAccount() ? 'Screenshot Asli Terverifikasi' : 'Preview Akun Resmi Terverifikasi' }}
                        </span>
                    </div>
                </div>

                @if(count($galleryUrls) > 1)
                    <div class="flex items-center gap-3 mt-4 overflow-x-auto pb-2 scrollbar-none">
                        @foreach($galleryUrls as $idx => $url)
                            <button type="button" class="gallery-thumb-btn relative flex-shrink-0 w-28 sm:w-36 aspect-video rounded-xl overflow-hidden border-2 {{ $idx === 0 ? 'border-amber-400 ring-2 ring-amber-400/30' : 'border-gray-800 opacity-60 hover:opacity-100 hover:border-gray-600' }} transition-all" data-img-url="{{ $url }}" data-img-index="{{ $idx }}">
                                <img src="{{ $url }}" alt="Thumbnail {{ $idx + 1 }}" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 right-1 text-[9px] bg-black/80 px-1.5 py-0.5 rounded text-white font-medium">#{{ $idx + 1 }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif

                @if(!empty($accountSpecs))
                    <div class="mt-8 pt-6 border-t border-gray-800">
                        <h3 class="text-xs font-bold text-gray-200 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span>📋</span> Spesifikasi &amp; Statistik Akun Lengkap
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            @foreach($accountSpecs as $key => $val)
                                <div class="p-3.5 rounded-2xl bg-gray-900/60 border border-gray-800 hover:border-amber-500/30 transition-all flex flex-col justify-between">
                                    <span class="text-[11px] text-gray-400 font-medium block">{{ $key }}</span>
                                    <span class="text-xs sm:text-sm font-bold text-amber-300 mt-1 block leading-snug">{{ $val }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- 3 Quick Steps Instructions -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-6 pt-4 border-t border-gray-800/80 text-xs">
                <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-900/40 border border-gray-800/80">
                    <span class="w-7 h-7 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-black text-xs flex-shrink-0">1</span>
                    <span class="text-gray-300 font-medium">Masukkan Data Akun Game</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-900/40 border border-gray-800/80">
                    <span class="w-7 h-7 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-black text-xs flex-shrink-0">2</span>
                    <span class="text-gray-300 font-medium">Pilih Paket Nominal Diamond</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-900/40 border border-gray-800/80">
                    <span class="w-7 h-7 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-black text-xs flex-shrink-0">3</span>
                    <span class="text-gray-300 font-medium">Pilih Pembayaran & Selesai!</span>
                </div>
            </div>
        @endif
    </div>

    <!-- MAIN CHECKOUT FORM -->
    <form action="{{ route('orders.store') }}" method="POST" id="checkout-form" class="space-y-6 sm:space-y-8">
        @csrf

        @if($product->requires_user_id || $product->requires_zone_id || $product->isJoki())
            <!-- STEP 1: MASUKKAN DATA AKUN GAME / JOKI (solustion411 Step 1) -->
            <div class="glass-card rounded-3xl p-5 sm:p-8 border border-gray-800">
                <div class="flex items-center justify-between mb-5 sm:mb-6">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-600 text-white font-black flex items-center justify-center text-sm shadow-md shadow-cyan-600/30">1</span>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-white">
                                @if($product->isJoki())
                                    Masukkan Data Akun Joki
                                @elseif($product->isGameAccount())
                                    Informasi Kontak Pembeli Akun
                                @else
                                    Masukkan Data Akun Game
                                @endif
                            </h3>
                            <p class="text-xs text-gray-400">Pastikan data akun yang Anda masukkan sudah sesuai.</p>
                        </div>
                    </div>

                    @if($product->requires_user_id)
                        <button type="button" onclick="document.getElementById('modal-id-guide').classList.remove('hidden')" class="px-2.5 py-1.5 sm:px-3 rounded-xl bg-cyan-600/10 hover:bg-cyan-600/20 text-cyan-400 border border-cyan-500/30 text-xs font-bold transition-all flex items-center gap-1.5">
                            <span>ℹ️</span>
                            <span>Petunjuk ID</span>
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($product->isJoki())
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5">Login Via <span class="text-rose-400">*</span></label>
                            <select name="target_zone_id" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                                <option value="Moonton">Moonton (Email & Password)</option>
                                <option value="VK">VK</option>
                                <option value="Facebook">Facebook</option>
                                <option value="TikTok">TikTok</option>
                                <option value="Google Play">Google Play</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5">Email / No. HP Akun Game <span class="text-rose-400">*</span></label>
                            <input type="text" name="target_user_id" value="{{ old('target_user_id') }}" required placeholder="Contoh: akun@gmail.com" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5">Password Akun Game <span class="text-rose-400">*</span></label>
                            <input type="password" name="target_nickname" value="{{ old('target_nickname') }}" required placeholder="Password akun game" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                            <span class="text-[10px] text-gray-500 mt-1 block">Terenkripsi aman. Matikan verifikasi 2 langkah saat joki diproses.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5">Catatan Hero / Request (Opsional)</label>
                            <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Contoh: Request hero Ling / Fanny" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                        </div>
                    @else
                        @if($product->requires_user_id)
                            <div>
                                <label class="block text-xs font-semibold text-gray-300 mb-1.5">{{ $product->user_id_label }} <span class="text-rose-400">*</span></label>
                                <input type="text" name="target_user_id" id="input_user_id" value="{{ old('target_user_id') }}" required placeholder="Masukkan {{ $product->user_id_label }}" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white font-mono focus:outline-none focus:border-cyan-500 transition-colors">
                                @error('target_user_id')
                                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        @if($product->requires_zone_id)
                            <div>
                                <label class="block text-xs font-semibold text-gray-300 mb-1.5">{{ $product->zone_id_label }} <span class="text-rose-400">*</span></label>
                                <input type="text" name="target_zone_id" id="input_zone_id" value="{{ old('target_zone_id') }}" required placeholder="Contoh: 2024" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white font-mono focus:outline-none focus:border-cyan-500 transition-colors">
                                @error('target_zone_id')
                                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @else
            <!-- STEP 1: INFORMASI AKUN DIGITAL & GARANSI (Khusus Produk Akun / Streaming / Netflix) -->
            <div class="glass-card rounded-3xl p-5 sm:p-8 border border-gray-800">
                <div class="flex items-center gap-3 mb-4 sm:mb-6">
                    <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-600 text-white font-black flex items-center justify-center text-sm shadow-md shadow-cyan-600/30">1</span>
                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-white">Detail Pengiriman &amp; Garansi Akun</h3>
                        <p class="text-xs text-gray-400">Kredensial akun dikirim otomatis ke nomor WhatsApp &amp; Email Anda setelah pembayaran.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-4 rounded-2xl bg-cyan-950/30 border border-cyan-500/20 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-base flex-shrink-0">
                            ⚡
                        </div>
                        <div>
                            <span class="text-xs font-bold text-white block">Pengiriman Instan</span>
                            <span class="text-[11px] text-gray-400 mt-0.5 block leading-relaxed">Email, Password &amp; PIN Profil dikirim otomatis via WhatsApp &amp; invoice web.</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-emerald-950/30 border border-emerald-500/20 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-base flex-shrink-0">
                            🛡️
                        </div>
                        <div>
                            <span class="text-xs font-bold text-white block">Garansi Full Masa Aktif</span>
                            <span class="text-[11px] text-gray-400 mt-0.5 block leading-relaxed">Garansi 100% aktif sesuai pilihan durasi (1 Hari, 3 Hari, 7 Hari, 1 Bulan).</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-indigo-950/30 border border-indigo-500/20 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-base flex-shrink-0">
                            🍿
                        </div>
                        <div>
                            <span class="text-xs font-bold text-white block">Ultra HD 4K &amp; Siap Pakai</span>
                            <span class="text-[11px] text-gray-400 mt-0.5 block leading-relaxed">Bisa login di HP, Laptop, Tablet, &amp; Smart TV tanpa kendala screen limit.</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- STEP 2: PILIH NOMINAL / VARIASI DURASI (solustion411 Step 2) -->
        <div class="glass-card rounded-3xl p-5 sm:p-8 border border-gray-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 sm:mb-6">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-600 text-white font-black flex items-center justify-center text-sm shadow-md shadow-cyan-600/30">2</span>
                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-white">
                            {{ $product->isGame() ? 'Pilih Nominal Top Up' : 'Pilih Durasi & Paket Variasi' }}
                        </h3>
                        <p class="text-xs text-gray-400">
                            {{ $product->isGame() ? 'Pilih nominal diamond atau paket yang Anda inginkan.' : 'Pilih durasi paket langganan (1 Hari, 3 Hari, 7 Hari, 1 Bulan) sesuai kebutuhan.' }}
                        </p>
                    </div>
                </div>
            </div>

            @php
                $hasDurationKeywords = $product->variants->contains(function ($v) {
                    $n = strtolower($v->name);
                    return str_contains($n, 'hari') || str_contains($n, 'bulan') || str_contains($n, 'bln') || str_contains($n, 'minggu');
                });
            @endphp

            @if($hasDurationKeywords)
                <!-- Quick Duration Filter Tabs -->
                <div class="flex items-center gap-2 mb-5 overflow-x-auto pb-1.5 scrollbar-none -mx-1 px-1" id="duration-filter-tabs">
                    <button type="button" onclick="filterVariantsByDuration('all', this)" class="duration-tab active-dur-tab px-3.5 py-1.5 rounded-xl text-xs font-bold bg-cyan-600 text-white shadow-sm border border-cyan-400/40 whitespace-nowrap transition-all">
                        Semua Pilihan
                    </button>
                    <button type="button" onclick="filterVariantsByDuration('1hari', this)" class="duration-tab px-3.5 py-1.5 rounded-xl text-xs font-bold bg-gray-900 text-gray-300 hover:text-white border border-gray-800 hover:bg-gray-800 transition-all whitespace-nowrap">
                        ⏱️ 1 Hari
                    </button>
                    <button type="button" onclick="filterVariantsByDuration('3hari', this)" class="duration-tab px-3.5 py-1.5 rounded-xl text-xs font-bold bg-gray-900 text-gray-300 hover:text-white border border-gray-800 hover:bg-gray-800 transition-all whitespace-nowrap">
                        ⚡ 3 Hari
                    </button>
                    <button type="button" onclick="filterVariantsByDuration('7hari', this)" class="duration-tab px-3.5 py-1.5 rounded-xl text-xs font-bold bg-gray-900 text-gray-300 hover:text-white border border-gray-800 hover:bg-gray-800 transition-all whitespace-nowrap">
                        🔥 7 Hari
                    </button>
                    <button type="button" onclick="filterVariantsByDuration('bulan', this)" class="duration-tab px-3.5 py-1.5 rounded-xl text-xs font-bold bg-gray-900 text-gray-300 hover:text-white border border-gray-800 hover:bg-gray-800 transition-all whitespace-nowrap">
                        📅 1 Bulan
                    </button>
                </div>
            @endif

            <!-- Nominal / Duration Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-3.5" id="variant-container">
                @foreach($product->variants as $variant)
                    @php
                        // Estimate strikethrough price (+15% higher)
                        $strikethrough = round($variant->price * 1.15, -2);
                        $lowerName = strtolower($variant->name);

                        // Determine duration tag and icon badge
                        $badgeIcon = '💎';
                        $badgeStyle = 'bg-cyan-500/20 text-cyan-400';
                        $durationChip = null;
                        $filterTag = 'other';

                        if (str_contains($lowerName, '1 hari') || str_contains($lowerName, '1d')) {
                            $badgeIcon = '⏱️';
                            $badgeStyle = 'bg-blue-500/20 text-blue-300';
                            $durationChip = '1 Hari';
                            $filterTag = '1hari';
                        } elseif (str_contains($lowerName, '3 hari') || str_contains($lowerName, '3d')) {
                            $badgeIcon = '⚡';
                            $badgeStyle = 'bg-amber-500/20 text-amber-300';
                            $durationChip = '3 Hari';
                            $filterTag = '3hari';
                        } elseif (str_contains($lowerName, '7 hari') || str_contains($lowerName, '7d') || str_contains($lowerName, 'minggu')) {
                            $badgeIcon = '🔥';
                            $badgeStyle = 'bg-orange-500/20 text-orange-300';
                            $durationChip = '7 Hari';
                            $filterTag = '7hari';
                        } elseif (str_contains($lowerName, 'bulan') || str_contains($lowerName, 'bln') || str_contains($lowerName, '1m')) {
                            $badgeIcon = '📅';
                            $badgeStyle = 'bg-indigo-500/20 text-indigo-300';
                            $durationChip = '1 Bulan';
                            $filterTag = 'bulan';
                        }
                    @endphp
                    <label class="cursor-pointer relative block p-3.5 sm:p-4 rounded-2xl border border-gray-800 hover:border-cyan-500/60 bg-gray-900/40 transition-all variant-card has-[:checked]:border-cyan-400 has-[:checked]:bg-cyan-500/10 has-[:checked]:shadow-lg has-[:checked]:shadow-cyan-500/20 group" data-filter-tag="{{ $filterTag }}">
                        <input type="radio" name="variant_id" value="{{ $variant->id }}" data-price="{{ $variant->price }}" data-name="{{ $variant->name }}" class="hidden variant-input" {{ $loop->first ? 'checked' : '' }} required>
                        
                        <div class="flex flex-col h-full justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-1 mb-2">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg {{ $badgeStyle }} flex items-center justify-center text-xs flex-shrink-0">{{ $badgeIcon }}</span>
                                        @if($durationChip)
                                            <span class="text-[9px] sm:text-[10px] font-black px-1.5 py-0.5 rounded-md {{ $badgeStyle }} border border-white/10 uppercase">
                                                {{ $durationChip }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($variant->stock_type !== 'unlimited')
                                        @if($variant->stock > 0)
                                            <span class="text-[9px] font-bold text-emerald-400 bg-emerald-500/10 px-1.5 sm:px-2 py-0.5 rounded-full border border-emerald-500/20 flex-shrink-0">
                                                Stok: {{ $variant->stock }}
                                            </span>
                                        @else
                                            <span class="text-[9px] font-bold text-rose-400 bg-rose-500/10 px-1.5 sm:px-2 py-0.5 rounded-full border border-rose-500/20 flex-shrink-0">
                                                Habis
                                            </span>
                                        @endif
                                    @endif
                                </div>
                                <span class="text-xs sm:text-sm font-extrabold text-white block group-hover:text-cyan-300 transition-colors line-clamp-2 leading-snug">
                                    {{ $variant->name }}
                                </span>
                            </div>

                            <div class="mt-3 pt-2 border-t border-gray-800/80">
                                <span class="text-[10px] text-gray-500 line-through block">Rp {{ number_format($strikethrough, 0, ',', '.') }}</span>
                                <span class="text-xs sm:text-sm font-black text-cyan-400 block mt-0.5">
                                    Rp {{ number_format($variant->price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Checkmark Indicator -->
                        <div class="hidden group-has-[:checked]:flex absolute top-2.5 right-2.5 w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-cyan-400 text-black items-center justify-center text-[10px] font-black shadow-md">
                            ✓
                        </div>
                    </label>
                @endforeach
            </div>
            @error('variant_id')
                <p class="text-xs text-rose-400 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- STEP 3: PILIH METODE PEMBAYARAN (solustion411 Categorized Accordion) -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-600 text-white font-black flex items-center justify-center text-sm shadow-md shadow-cyan-600/30">3</span>
                <div>
                    <h3 class="text-lg font-extrabold text-white">Pilih Metode Pembayaran</h3>
                    <p class="text-xs text-gray-400">Pilih saluran pembayaran otomatis QRIS, E-Wallet, atau Transfer Bank.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                @foreach($paymentChannels as $channel)
                    @php 
                        $fee = $channel['fee_customer']['flat'] ?? 0;
                    @endphp
                    <label class="cursor-pointer relative p-4 rounded-2xl border border-gray-800 hover:border-cyan-500/60 bg-gray-900/40 transition-all payment-card has-[:checked]:border-cyan-400 has-[:checked]:bg-cyan-500/10 has-[:checked]:shadow-lg has-[:checked]:shadow-cyan-500/20 group">
                        <input type="radio" name="payment_method" value="{{ $channel['code'] }}" data-name="{{ $channel['name'] }}" data-fee="{{ $fee }}" class="hidden payment-input" {{ $loop->first ? 'checked' : '' }} required>
                        
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-gray-900 border border-gray-700 flex items-center justify-center font-black text-xs text-cyan-400 flex-shrink-0 shadow-sm">
                                    {{ substr($channel['code'], 0, 4) }}
                                </div>
                                <div>
                                    <span class="text-xs font-extrabold text-white block group-hover:text-cyan-300 transition-colors">
                                        {{ $channel['name'] }}
                                    </span>
                                    <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 mt-0.5">
                                        ⚡ Otomatis 1-3 Detik
                                    </span>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="text-[11px] font-bold text-gray-400 block channel-calculated-price">
                                    {{ $fee > 0 ? '+ Rp '.number_format($fee, 0, ',', '.') : 'Bebas Biaya' }}
                                </span>
                            </div>
                        </div>

                        <!-- Checkmark Indicator -->
                        <div class="hidden group-has-[:checked]:flex absolute top-2 right-2 w-4 h-4 rounded-full bg-cyan-400 text-black items-center justify-center text-[9px] font-black">
                            ✓
                        </div>
                    </label>
                @endforeach
            </div>
            @error('payment_method')
                <p class="text-xs text-rose-400 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- STEP 4: MASUKKAN KONTAK WHATSAPP & EMAIL (solustion411 Step 4) -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-600 text-white font-black flex items-center justify-center text-sm shadow-md shadow-cyan-600/30">4</span>
                <div>
                    <h3 class="text-lg font-extrabold text-white">Masukkan Informasi Kontak</h3>
                    <p class="text-xs text-gray-400">Bukti pembayaran dan status invoice akan dikirim ke WhatsApp & Email Anda.</p>
                </div>
            </div>

            <!-- Guest Checkout Notice Banner -->
            <div class="mb-5 p-3.5 rounded-2xl bg-gradient-to-r from-cyan-950/40 via-slate-900/60 to-blue-950/40 border border-cyan-500/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">⚡</span>
                    <div>
                        <span class="font-extrabold text-white block">Belanja Cepat Tanpa Perlu Login</span>
                        <span class="text-gray-300 text-[11px]">Pembeli umum bisa langsung checkout. Cukup masukkan kontak di bawah untuk bukti invoice otomatis.</span>
                    </div>
                </div>
                @guest
                    <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-cyan-400 hover:text-cyan-300 text-[11px] font-bold border border-gray-700 flex-shrink-0 transition-all flex items-center gap-1">
                        <span>Punya Akun Member? Masuk</span> ↗
                    </a>
                @else
                    <span class="px-3 py-1 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-bold text-[11px] flex-shrink-0">
                        ✓ Login: {{ auth()->user()->name }}
                    </span>
                @endguest
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">No. WhatsApp <span class="text-rose-400">*</span></label>
                    <input type="tel" name="customer_phone" id="input_phone" value="{{ old('customer_phone', auth()->user()?->phone) }}" required placeholder="08xxxxxxxxxx" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                    <span class="text-[10px] text-gray-500 mt-1 block">Pastikan nomor aktif untuk konfirmasi pesanan via WhatsApp.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Alamat Email Aktif <span class="text-rose-400">*</span></label>
                    <input type="email" name="customer_email" id="input_email" value="{{ old('customer_email', auth()->user()?->email) }}" required placeholder="nama@email.com" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Lengkap <span class="text-rose-400">*</span></label>
                    <input type="text" name="customer_name" id="input_name" value="{{ old('customer_name', auth()->user()?->name) }}" required placeholder="Nama Anda" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                </div>
            </div>
        </div>

        <!-- STEP 5: KODE PROMO & RINGKASAN PEMBELIAN -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-cyan-500/30 glow-cyan">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-600 text-white font-black flex items-center justify-center text-sm shadow-md shadow-cyan-600/30">5</span>
                <div>
                    <h3 class="text-lg font-extrabold text-white">Kode Promo & Ringkasan Pembayaran</h3>
                    <p class="text-xs text-gray-400">Gunakan voucher diskon khusus member untuk mendapatkan potongan harga hemat.</p>
                </div>
            </div>

            <!-- Applied Voucher Code Hidden Input -->
            <input type="hidden" name="voucher_code" id="applied_voucher_code" value="{{ old('voucher_code') }}">

            @auth
                <!-- Logged in Member Badge -->
                <div class="mb-4 p-3 rounded-2xl bg-emerald-950/40 border border-emerald-500/30 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-base">👑</span>
                        <span class="text-emerald-300 font-bold">Login: {{ auth()->user()->name }} ({{ auth()->user()->isAdmin() ? 'Admin' : 'Member' }})</span>
                    </div>
                    <span class="text-[11px] text-emerald-400 font-semibold">Member Aktif</span>
                </div>
            @else
                <!-- Guest Voucher Note -->
                <div class="mb-4 p-3 rounded-2xl bg-cyan-950/30 border border-cyan-500/20 flex items-center justify-between text-xs text-gray-300">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🎟️</span>
                        <span class="text-gray-300 text-[11px]">Punya kode voucher promo? Masukkan kode di bawah untuk mendapatkan potongan harga langsung!</span>
                    </div>
                </div>
            @endauth

            <!-- Promo Code Input (Open to All Customers) -->
            <div class="flex gap-2 mb-3">
                <input type="text" id="promo-code-input" placeholder="Masukkan Kode Voucher (Contoh: HEMAT10)" class="flex-1 bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white uppercase font-mono focus:outline-none focus:border-cyan-500">
                <button type="button" id="btn-apply-promo" onclick="applyPromoCode()" class="px-5 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-cyan-400 hover:text-cyan-300 text-xs font-bold transition-all border border-gray-700 hover:border-cyan-500/40 cursor-pointer">
                    Terapkan
                </button>
            </div>
            
            <!-- Promo Feedback Message -->
            <div id="promo-message" class="hidden text-xs mb-4 p-3 rounded-xl border"></div>

            <!-- Summary Table -->
            <div class="space-y-2.5 text-xs text-gray-300 pb-4 border-b border-gray-800">
                <div class="flex justify-between">
                    <span>Layanan</span>
                    <span class="font-bold text-white">{{ $product->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Item Nominal</span>
                    <span class="font-extrabold text-cyan-400" id="summary-variant">-</span>
                </div>
                <div class="flex justify-between">
                    <span>Metode Pembayaran</span>
                    <span class="font-semibold text-white" id="summary-payment">-</span>
                </div>
                <div class="flex justify-between">
                    <span>Harga Item</span>
                    <span class="font-semibold text-white" id="summary-price">Rp 0</span>
                </div>
                <div class="hidden justify-between text-emerald-400 font-bold" id="summary-discount-row">
                    <span class="flex items-center gap-1.5">
                        <span>🎟️ Diskon Voucher</span>
                        <span class="text-[10px] text-emerald-300 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20" id="summary-discount-code">-</span>
                    </span>
                    <span id="summary-discount">- Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span>Biaya Layanan</span>
                    <span class="font-semibold text-gray-400" id="summary-fee">Rp 0</span>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-between mb-6">
                <span class="text-sm font-bold text-gray-300">Total Pembayaran</span>
                <span class="text-2xl sm:text-3xl font-black text-cyan-400" id="summary-total">Rp 0</span>
            </div>

            <button type="button" onclick="openOrderConfirmModal()" class="w-full py-4 rounded-2xl bg-gradient-to-r from-cyan-500 via-blue-600 to-indigo-600 hover:from-cyan-400 hover:to-blue-500 text-white font-black text-base shadow-xl shadow-cyan-600/30 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                <span>Beli Sekarang ⚡</span>
            </button>
        </div>

    </form>
</div>

<!-- STICKY FLOATING BOTTOM SUMMARY BAR (solustion411 Signature) -->
<div class="fixed bottom-0 left-0 right-0 z-40 bg-gray-950/95 backdrop-blur-md border-t border-gray-800 p-3 sm:p-4 shadow-2xl" style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
    <div class="max-w-4xl mx-auto flex items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 font-medium hidden sm:inline">Item:</span>
                <span class="text-xs sm:text-sm font-bold text-white truncate max-w-[150px] sm:max-w-xs block" id="sticky-variant">-</span>
            </div>
            <div class="flex items-center gap-2 mt-0.5">
                <span class="text-[10px] text-gray-400 uppercase font-medium">Total:</span>
                <span class="text-base sm:text-xl font-black text-cyan-400" id="sticky-total">Rp 0</span>
            </div>
        </div>

        <button type="button" onclick="openOrderConfirmModal()" class="px-6 sm:px-8 py-2.5 sm:py-3 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-black text-xs sm:text-sm shadow-lg shadow-cyan-600/30 hover:scale-105 active:scale-95 transition-all flex items-center gap-2 flex-shrink-0">
            <span>Beli Sekarang</span>
            <span>⚡</span>
        </button>
    </div>
</div>

<!-- MODAL: ORDER CONFIRMATION MODAL (solustion411 Double-Check Dialog) -->
<div id="modal-order-confirm" class="hidden fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-cyan-500/30 max-w-md w-full shadow-2xl">
        <div class="text-center pb-4 border-b border-gray-800 mb-5">
            <div class="w-14 h-14 rounded-2xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-3xl mx-auto mb-3 border border-cyan-500/30">
                ⚡
            </div>
            <h3 class="text-lg font-black text-white">Konfirmasi Data Pesanan</h3>
            <p class="text-xs text-gray-400 mt-1">
                @if($product->requires_user_id)
                    Harap pastikan User ID dan Server ID Anda sudah benar sebelum membayar.
                @else
                    Harap periksa pilihan variasi dan kontak Anda sebelum melanjutkan ke pembayaran.
                @endif
            </p>
        </div>

        <div class="space-y-3 text-xs bg-gray-900/60 p-4 rounded-2xl border border-gray-800 mb-6">
            <div class="flex justify-between">
                <span class="text-gray-400">{{ $product->isGame() ? 'Game:' : 'Layanan:' }}</span>
                <span class="font-bold text-white">{{ $product->name }}</span>
            </div>
            @if($product->requires_user_id)
                <div class="flex justify-between">
                    <span class="text-gray-400">{{ $product->user_id_label }}:</span>
                    <span class="font-mono font-extrabold text-cyan-400" id="confirm-user-id">-</span>
                </div>
            @endif
            @if($product->requires_zone_id)
                <div class="flex justify-between">
                    <span class="text-gray-400">{{ $product->zone_id_label }}:</span>
                    <span class="font-mono font-extrabold text-cyan-400" id="confirm-zone-id">-</span>
                </div>
            @endif
            <div class="flex justify-between">
                <span class="text-gray-400">Item Nominal:</span>
                <span class="font-bold text-white" id="confirm-variant">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Metode Bayar:</span>
                <span class="font-bold text-white" id="confirm-payment">-</span>
            </div>
            <div class="hidden justify-between text-emerald-400 font-bold" id="confirm-discount-row">
                <span>Diskon Voucher:</span>
                <span id="confirm-discount">- Rp 0</span>
            </div>
            <div class="flex justify-between pt-2 border-t border-gray-800">
                <span class="text-gray-400 font-bold">Total Tagihan:</span>
                <span class="font-black text-base text-cyan-400" id="confirm-total">Rp 0</span>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="document.getElementById('modal-order-confirm').classList.add('hidden')" class="flex-1 py-3 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold text-xs transition-all">
                Ubah Data
            </button>
            <button type="button" onclick="submitFinalOrder()" id="btn-final-submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-xs shadow-lg shadow-cyan-600/30 transition-all flex items-center justify-center gap-1.5">
                <span>Bayar Sekarang</span>
                <span>➜</span>
            </button>
        </div>
    </div>
</div>

<!-- MODAL: PETUNJUK USER ID GAME (solustion411 Graphic Guide) -->
<div id="modal-id-guide" class="hidden fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800 max-w-lg w-full">
        <div class="flex items-center justify-between pb-4 border-b border-gray-800 mb-5">
            <div class="flex items-center gap-2">
                <span class="text-xl">ℹ️</span>
                <h3 class="text-base font-extrabold text-white">Petunjuk Menemukan User ID Game</h3>
            </div>
            <button type="button" onclick="document.getElementById('modal-id-guide').classList.add('hidden')" class="text-gray-400 hover:text-white text-lg">✕</button>
        </div>

        <div class="space-y-4 text-xs text-gray-300 leading-relaxed">
            <div class="p-4 rounded-2xl bg-cyan-950/20 border border-cyan-500/20">
                <h4 class="font-bold text-cyan-400 mb-1">🎮 Mobile Legends: Bang Bang</h4>
                <ol class="list-decimal pl-4 space-y-1 text-gray-300">
                    <li>Buka game Mobile Legends di ponsel Anda.</li>
                    <li>Klik foto profil avatar Anda di pojok kiri atas layar utama.</li>
                    <li>Pilih tab <strong>Info Dasar</strong> (Basic Info).</li>
                    <li>Di bawah nickname Anda, terdapat User ID dan Zone ID. Contoh: <code class="text-cyan-300 font-mono">12345678 (2024)</code>. User ID: <strong>12345678</strong>, Zone ID: <strong>2024</strong>.</li>
                </ol>
            </div>

            <div class="p-4 rounded-2xl bg-amber-950/20 border border-amber-500/20">
                <h4 class="font-bold text-amber-400 mb-1">🔥 Free Fire</h4>
                <ol class="list-decimal pl-4 space-y-1 text-gray-300">
                    <li>Buka game Free Fire lalu klik profil pemain di pojok kiri atas.</li>
                    <li>Nomor Player ID / UID Anda tertera di sebelah nama akun.</li>
                    <li>Salin UID tersebut dan masukkan pada kolom User ID.</li>
                </ol>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="button" onclick="document.getElementById('modal-id-guide').classList.add('hidden')" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold">
                Mengerti
            </button>
        </div>
    </div>
</div>

<!-- Fullscreen Lightbox Modal for Game Account Screenshots -->
<div id="screenshot-lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/90 backdrop-blur-md p-4 transition-all duration-300">
    <div class="relative max-w-5xl w-full max-h-[92vh] flex flex-col items-center">
        <div class="w-full flex items-center justify-between pb-3 text-white text-xs sm:text-sm font-semibold">
            <span id="lightbox-counter" class="px-3.5 py-1 rounded-full bg-gray-900/80 border border-gray-700 text-amber-300">Foto 1 dari {{ count($galleryUrls) }}</span>
            <button type="button" id="lightbox-close" class="p-2 rounded-full bg-gray-900/80 hover:bg-gray-800 text-gray-300 hover:text-white transition-all text-base leading-none border border-gray-700">
                ✕ Tutup
            </button>
        </div>
        <div class="relative w-full max-h-[80vh] flex items-center justify-center overflow-hidden rounded-2xl bg-black border border-gray-800">
            <img id="lightbox-img" src="" alt="Screenshot Akun Zoom" class="max-w-full max-h-[78vh] object-contain rounded-xl">
            @if(count($galleryUrls) > 1)
                <button type="button" id="lightbox-prev" class="absolute left-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-black/70 hover:bg-black text-white flex items-center justify-center border border-gray-700 text-xl font-bold transition-all shadow-lg hover:scale-105">‹</button>
                <button type="button" id="lightbox-next" class="absolute right-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-black/70 hover:bg-black text-white flex items-center justify-center border border-gray-700 text-xl font-bold transition-all shadow-lg hover:scale-105">›</button>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    let appliedVoucher = {
        code: '',
        name: '',
        discountAmount: 0
    };

    function formatRupiah(amount) {
        return 'Rp ' + Math.round(amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function updateCalculation() {
        let price = 0;
        let variantName = '-';
        let fee = 0;
        let paymentName = '-';

        const summaryVariant = document.getElementById('summary-variant');
        const summaryPayment = document.getElementById('summary-payment');
        const summaryPrice = document.getElementById('summary-price');
        const summaryFee = document.getElementById('summary-fee');
        const summaryTotal = document.getElementById('summary-total');

        const summaryDiscountRow = document.getElementById('summary-discount-row');
        const summaryDiscountCode = document.getElementById('summary-discount-code');
        const summaryDiscount = document.getElementById('summary-discount');
        const confirmDiscountRow = document.getElementById('confirm-discount-row');
        const confirmDiscount = document.getElementById('confirm-discount');

        const stickyVariant = document.getElementById('sticky-variant');
        const stickyTotal = document.getElementById('sticky-total');

        const selectedVariant = document.querySelector('.variant-input:checked');
        if (selectedVariant) {
            price = parseFloat(selectedVariant.dataset.price) || 0;
            variantName = selectedVariant.dataset.name || '-';
        }

        const selectedPayment = document.querySelector('.payment-input:checked');
        if (selectedPayment) {
            fee = parseFloat(selectedPayment.dataset.fee) || 0;
            paymentName = selectedPayment.dataset.name || selectedPayment.value;
        }

        const discount = appliedVoucher.discountAmount || 0;
        const payableSubtotal = Math.max(0, price - discount);
        const total = payableSubtotal + fee;

        if (summaryVariant) summaryVariant.textContent = variantName;
        if (summaryPayment) summaryPayment.textContent = paymentName;
        if (summaryPrice) summaryPrice.textContent = formatRupiah(price);
        if (summaryFee) summaryFee.textContent = formatRupiah(fee);

        if (discount > 0) {
            if (summaryDiscountRow) {
                summaryDiscountRow.classList.remove('hidden');
                summaryDiscountRow.classList.add('flex');
            }
            if (summaryDiscountCode) summaryDiscountCode.textContent = appliedVoucher.code;
            if (summaryDiscount) summaryDiscount.textContent = '- ' + formatRupiah(discount);

            if (confirmDiscountRow) {
                confirmDiscountRow.classList.remove('hidden');
                confirmDiscountRow.classList.add('flex');
            }
            if (confirmDiscount) confirmDiscount.textContent = '- ' + formatRupiah(discount);
        } else {
            if (summaryDiscountRow) {
                summaryDiscountRow.classList.add('hidden');
                summaryDiscountRow.classList.remove('flex');
            }
            if (confirmDiscountRow) {
                confirmDiscountRow.classList.add('hidden');
                confirmDiscountRow.classList.remove('flex');
            }
        }

        if (summaryTotal) summaryTotal.textContent = formatRupiah(total);
        if (stickyVariant) stickyVariant.textContent = variantName;
        if (stickyTotal) stickyTotal.textContent = formatRupiah(total);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const variantInputs = document.querySelectorAll('.variant-input');
        const paymentInputs = document.querySelectorAll('.payment-input');

        variantInputs.forEach(input => {
            input.addEventListener('change', () => {
                if (appliedVoucher.code) {
                    applyPromoCode();
                } else {
                    updateCalculation();
                }
            });
        });
        paymentInputs.forEach(input => input.addEventListener('change', updateCalculation));

        updateCalculation();

        // Gallery & Lightbox Logic
        const galleryUrls = @json($galleryUrls ?? []);
        let activeGalleryIndex = 0;
        const mainImage = document.getElementById('gallery-main-image');
        const mainContainer = document.getElementById('gallery-main-container');
        const btnZoomOverlay = document.getElementById('btn-zoom-overlay');
        const thumbButtons = document.querySelectorAll('.gallery-thumb-btn');

        const lightbox = document.getElementById('screenshot-lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxClose = document.getElementById('lightbox-close');
        const lightboxCounter = document.getElementById('lightbox-counter');
        const lightboxPrev = document.getElementById('lightbox-prev');
        const lightboxNext = document.getElementById('lightbox-next');

        function setActiveImage(index) {
            if (!galleryUrls.length) return;
            activeGalleryIndex = (index + galleryUrls.length) % galleryUrls.length;
            const newUrl = galleryUrls[activeGalleryIndex];

            if (mainImage) mainImage.src = newUrl;

            thumbButtons.forEach((btn, idx) => {
                if (idx === activeGalleryIndex) {
                    btn.classList.add('border-amber-400', 'ring-2', 'ring-amber-400/30');
                    btn.classList.remove('border-gray-800', 'opacity-60');
                } else {
                    btn.classList.remove('border-amber-400', 'ring-2', 'ring-amber-400/30');
                    btn.classList.add('border-gray-800', 'opacity-60');
                }
            });

            if (lightbox && !lightbox.classList.contains('hidden')) {
                lightboxImg.src = newUrl;
                if (lightboxCounter) {
                    lightboxCounter.textContent = `Foto ${activeGalleryIndex + 1} dari ${galleryUrls.length}`;
                }
            }
        }

        thumbButtons.forEach((btn, idx) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                setActiveImage(idx);
            });
        });

        function openLightbox() {
            if (!galleryUrls.length || !lightbox) return;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            lightboxImg.src = galleryUrls[activeGalleryIndex];
            if (lightboxCounter) lightboxCounter.textContent = `Foto ${activeGalleryIndex + 1} dari ${galleryUrls.length}`;
            document.body.classList.add('overflow-hidden');
        }

        function closeLightbox() {
            if (!lightbox) return;
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        if (mainContainer) mainContainer.addEventListener('click', openLightbox);
        if (btnZoomOverlay) btnZoomOverlay.addEventListener('click', (e) => { e.stopPropagation(); openLightbox(); });
        if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
        if (lightboxPrev) lightboxPrev.addEventListener('click', (e) => { e.stopPropagation(); setActiveImage(activeGalleryIndex - 1); });
        if (lightboxNext) lightboxNext.addEventListener('click', (e) => { e.stopPropagation(); setActiveImage(activeGalleryIndex + 1); });
        if (lightbox) lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });

        document.addEventListener('keydown', (e) => {
            if (lightbox && !lightbox.classList.contains('hidden')) {
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft') setActiveImage(activeGalleryIndex - 1);
                if (e.key === 'ArrowRight') setActiveImage(activeGalleryIndex + 1);
            }
        });
    });

    // Duration Filter Tabs (1 Hari, 3 Hari, 7 Hari, 1 Bulan)
    function filterVariantsByDuration(tag, element) {
        const tabs = document.querySelectorAll('.duration-tab');
        tabs.forEach(tab => {
            tab.className = 'duration-tab px-3.5 py-1.5 rounded-xl text-xs font-bold bg-gray-900 text-gray-300 hover:text-white border border-gray-800 hover:bg-gray-800 transition-all whitespace-nowrap cursor-pointer';
        });
        if (element) {
            element.className = 'duration-tab active-dur-tab px-3.5 py-1.5 rounded-xl text-xs font-bold bg-cyan-600 text-white shadow-sm border border-cyan-400/40 whitespace-nowrap transition-all cursor-pointer';
        }

        const cards = document.querySelectorAll('.variant-card');
        let firstVisibleInput = null;

        cards.forEach(card => {
            const cardTag = card.dataset.filterTag;
            if (tag === 'all' || cardTag === tag) {
                card.classList.remove('hidden');
                if (!firstVisibleInput) {
                    firstVisibleInput = card.querySelector('.variant-input');
                }
            } else {
                card.classList.add('hidden');
            }
        });

        // Auto-select first visible variant if active one is hidden
        const currentChecked = document.querySelector('.variant-input:checked');
        if (currentChecked && currentChecked.closest('.variant-card').classList.contains('hidden')) {
            if (firstVisibleInput) {
                firstVisibleInput.checked = true;
                firstVisibleInput.dispatchEvent(new Event('change'));
            }
        }
    }

    // Confirmation Modal Trigger
    function openOrderConfirmModal() {
        const form = document.getElementById('checkout-form');
        if (!form.reportValidity()) {
            return;
        }

        const inputUserId = document.getElementById('input_user_id');
        const inputZoneId = document.getElementById('input_zone_id');
        const confirmUserId = document.getElementById('confirm-user-id');
        const confirmZoneId = document.getElementById('confirm-zone-id');

        if (confirmUserId && inputUserId) confirmUserId.textContent = inputUserId.value;
        if (confirmZoneId && inputZoneId) confirmZoneId.textContent = inputZoneId.value;

        const selVar = document.querySelector('.variant-input:checked');
        const selPay = document.querySelector('.payment-input:checked');
        
        if (selVar) document.getElementById('confirm-variant').textContent = selVar.dataset.name;
        if (selPay) document.getElementById('confirm-payment').textContent = selPay.dataset.name || selPay.value;
        document.getElementById('confirm-total').textContent = document.getElementById('summary-total').textContent;

        document.getElementById('modal-order-confirm').classList.remove('hidden');
    }

    function submitFinalOrder() {
        const btn = document.getElementById('btn-final-submit');
        btn.disabled = true;
        btn.innerHTML = '<span>Memproses...</span> ⏳';
        document.getElementById('checkout-form').submit();
    }

    async function applyPromoCode() {
        const input = document.getElementById('promo-code-input');
        const code = input ? input.value.trim().toUpperCase() : '';
        const msg = document.getElementById('promo-message');
        const btn = document.getElementById('btn-apply-promo');
        const appliedVoucherInput = document.getElementById('applied_voucher_code');

        if (!code) {
            alert('Silakan masukkan kode voucher terlebih dahulu!');
            return;
        }

        const selectedVariant = document.querySelector('.variant-input:checked');
        if (!selectedVariant) {
            alert('Silakan pilih nominal produk terlebih dahulu.');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Memeriksa...';

        try {
            const response = await fetch("{{ route('vouchers.check') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    code: code,
                    variant_id: selectedVariant.value,
                    quantity: 1
                })
            });

            const data = await response.json();

            if (response.status === 401 || data.requires_login) {
                removePromoCode(false);
                msg.className = 'text-xs mb-4 p-3 rounded-xl border bg-amber-500/10 border-amber-500/30 text-amber-300';
                msg.innerHTML = `⚠️ ${data.message} <a href="{{ route('login') }}" class="underline font-bold text-cyan-400 ml-1">Klik untuk Login ↗</a>`;
                msg.classList.remove('hidden');
                return;
            }

            if (response.ok && data.valid) {
                appliedVoucher = {
                    code: data.code,
                    name: data.name,
                    discountAmount: parseFloat(data.discount_amount) || 0
                };
                if (appliedVoucherInput) appliedVoucherInput.value = data.code;

                msg.className = 'text-xs mb-4 p-3 rounded-xl border bg-emerald-500/10 border-emerald-500/30 text-emerald-400 flex items-center justify-between';
                msg.innerHTML = `
                    <span>✓ ${data.message}</span>
                    <button type="button" onclick="removePromoCode()" class="text-rose-400 hover:text-rose-300 font-bold ml-2 underline text-[11px] cursor-pointer">Hapus</button>
                `;
                msg.classList.remove('hidden');

                updateCalculation();
            } else {
                removePromoCode(false);
                msg.className = 'text-xs mb-4 p-3 rounded-xl border bg-rose-500/10 border-rose-500/30 text-rose-400';
                msg.textContent = '⚠️ ' + (data.message || 'Kode voucher tidak valid atau tidak memenuhi syarat.');
                msg.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Voucher check error:', err);
            removePromoCode(false);
            msg.className = 'text-xs mb-4 p-3 rounded-xl border bg-rose-500/10 border-rose-500/30 text-rose-400';
            msg.textContent = '⚠️ Gagal memeriksa kode voucher. Silakan periksa jaringan Anda.';
            msg.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Terapkan';
        }
    }

    function removePromoCode(clearInput = true) {
        appliedVoucher = { code: '', name: '', discountAmount: 0 };
        const appliedVoucherInput = document.getElementById('applied_voucher_code');
        if (appliedVoucherInput) appliedVoucherInput.value = '';
        if (clearInput) {
            const input = document.getElementById('promo-code-input');
            if (input) input.value = '';
            const msg = document.getElementById('promo-message');
            if (msg) msg.classList.add('hidden');
        }
        updateCalculation();
    }
</script>
@endpush
@endsection

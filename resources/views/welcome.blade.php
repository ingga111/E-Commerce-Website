@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-20">
    
    <!-- 1. HERO BANNER CAROUSEL (solustion411 Slider - Admin Configurable) -->
    <div class="relative rounded-3xl overflow-hidden glass-card border border-cyan-500/20 mb-8 sm:mb-10 shadow-2xl group" id="hero-slider-container">
        <div class="relative w-full aspect-[16/10] sm:aspect-[21/9] min-h-[290px] sm:min-h-[340px] md:min-h-[400px] overflow-hidden" id="slider-track">
            @forelse($banners as $index => $banner)
                <div class="banner-slide absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}" data-index="{{ $index }}">
                    <!-- Background: Uploaded Image or Dynamic Gradient -->
                    @if($banner->getImageUrl())
                        <img src="{{ $banner->getImageUrl() }}" alt="{{ $banner->title }}" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/50 to-transparent"></div>
                    @else
                        <div class="absolute inset-0 bg-gradient-to-r {{ $banner->gradient ?? 'from-blue-900 via-indigo-950 to-slate-950' }}"></div>
                        <!-- Ambient Gamer Glow & Decorative Graphics -->
                        <div class="absolute -right-10 -bottom-10 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute right-12 top-1/2 -translate-y-1/2 hidden md:flex items-center justify-center opacity-30 pointer-events-none text-9xl">
                            🎮
                        </div>
                    @endif

                    <!-- Slide Content Overlay -->
                    <div class="relative z-20 h-full flex flex-col justify-center px-5 sm:px-12 md:px-16 py-6 sm:py-8 max-w-2xl">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold uppercase bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 mb-2 sm:mb-3 w-fit backdrop-blur-md">
                            <span class="animate-pulse">⚡</span> {{ $banner->badge_text ?? 'PROMO SPESIAL' }}
                        </div>
                        <h2 class="text-xl sm:text-3xl md:text-5xl font-black text-white tracking-tight leading-tight sm:leading-tight drop-shadow-lg">
                            {{ $banner->title }}
                        </h2>
                        @if($banner->subtitle)
                            <p class="mt-2 sm:mt-3 text-[11px] sm:text-sm md:text-base text-gray-200 line-clamp-2 max-w-xl leading-relaxed drop-shadow">
                                {{ $banner->subtitle }}
                            </p>
                        @endif

                        <div class="mt-4 sm:mt-6 flex items-center gap-3">
                            <a href="{{ $banner->target_url ?? '#catalog-section' }}" class="px-5 sm:px-7 py-2.5 sm:py-3 rounded-2xl bg-gradient-to-r from-cyan-500 via-blue-600 to-indigo-600 hover:from-cyan-400 hover:to-blue-500 text-white font-black text-xs sm:text-sm shadow-lg shadow-cyan-500/30 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                                <span>Top Up Sekarang</span>
                                <span>→</span>
                            </a>
                            <span class="hidden sm:inline-block text-xs font-semibold text-gray-300 backdrop-blur-md bg-black/40 px-3 py-1.5 rounded-xl border border-white/10">
                                ⚡ Proses 1-3 Detik
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Fallback slide if no banner seeded -->
                <div class="banner-slide absolute inset-0 w-full h-full bg-gradient-to-r from-blue-900 via-indigo-950 to-slate-950 flex flex-col justify-center px-6 sm:px-16 py-6 sm:py-8">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 w-fit mb-2 sm:mb-3">⚡ SOLUSTION411 ID</span>
                    <h2 class="text-2xl sm:text-5xl font-black text-white">Top Up Game Termurah & Tercepat</h2>
                    <p class="text-xs sm:text-sm text-gray-300 mt-2">Mobile Legends, Free Fire, Valorant, & Akun Sultan Bergaransi.</p>
                    <div class="mt-4 sm:mt-6">
                        <a href="#catalog-section" class="px-5 py-2.5 rounded-2xl bg-cyan-500 hover:bg-cyan-400 text-black font-extrabold text-xs transition-all inline-flex items-center gap-2">
                            <span>Mulai Belanja</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Slider Navigation Controls -->
        <button type="button" onclick="prevSlide()" class="absolute left-3 top-1/2 -translate-y-1/2 z-30 w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-black/50 hover:bg-black/80 text-white border border-white/10 flex items-center justify-center transition-all opacity-0 group-hover:opacity-100" aria-label="Slide Sebelumnya">
            ❮
        </button>
        <button type="button" onclick="nextSlide()" class="absolute right-3 top-1/2 -translate-y-1/2 z-30 w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-black/50 hover:bg-black/80 text-white border border-white/10 flex items-center justify-center transition-all opacity-0 group-hover:opacity-100" aria-label="Slide Selanjutnya">
            ❯
        </button>

        <!-- Slider Indicators Dots -->
        <div class="absolute bottom-2.5 sm:bottom-4 left-1/2 -translate-x-1/2 z-30 flex items-center gap-2">
            @foreach($banners as $index => $banner)
                <button type="button" onclick="goToSlide({{ $index }})" class="slider-dot w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full transition-all {{ $index === 0 ? 'bg-cyan-400 w-6 sm:w-8' : 'bg-gray-600 hover:bg-gray-400' }}" aria-label="Go to slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
    </div>

    <!-- 2. FLASH SALE / PROMO HARI INI TICKER (solustion411 Feature - Admin Managed) -->
    @if($flashSale)
        <a href="{{ $flashSale->target_url ?? '#group-game' }}" class="block group">
            <div class="glass-card rounded-2xl p-4 sm:p-5 border border-amber-500/30 bg-gradient-to-r from-amber-950/20 via-slate-900/60 to-orange-950/20 mb-8 sm:mb-10 flex flex-col sm:flex-row items-center justify-between gap-4 hover:border-amber-500/50 transition-all shadow-lg">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xl font-bold flex-shrink-0 group-hover:scale-110 transition-transform">
                        🔥
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm sm:text-base font-extrabold text-white group-hover:text-amber-300 transition-colors">{{ $flashSale->title }}</h3>
                            <span class="px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-400 text-[10px] font-bold border border-rose-500/30 animate-pulse">{{ $flashSale->badge_text }}</span>
                        </div>
                        @if($flashSale->subtitle)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $flashSale->subtitle }}</p>
                        @endif
                    </div>
                </div>

                @if($flashSale->end_time)
                    <div class="flex items-center gap-2 text-xs font-mono font-bold text-amber-400 bg-black/60 px-4 py-2 rounded-xl border border-amber-500/30 flex-shrink-0 w-full sm:w-auto justify-center" id="flash-sale-timer" data-endtime="{{ $flashSale->end_time->toISOString() }}">
                        <span class="text-[11px] text-gray-400">Berakhir dalam:</span>
                        <span class="px-2 py-1 bg-amber-500/20 rounded text-amber-300" id="fs-hours">00</span>:
                        <span class="px-2 py-1 bg-amber-500/20 rounded text-amber-300" id="fs-minutes">00</span>:
                        <span class="px-2 py-1 bg-amber-500/20 rounded text-amber-300" id="fs-seconds">00</span>
                    </div>
                @endif
            </div>
        </a>
    @endif

    <!-- 3. CATEGORY TABS FILTER (solustion411 Signature Pill Navigation) -->
    <div class="-mx-4 px-4 sm:mx-0 sm:px-0 mb-8 overflow-x-auto pb-2 scrollbar-none">
        <div class="flex items-center gap-2 sm:gap-2.5 min-w-max">
            <button type="button" onclick="filterCategory('all', this)" class="category-pill active-pill px-3.5 py-2 sm:px-5 sm:py-2.5 rounded-xl sm:rounded-2xl text-xs font-extrabold transition-all flex items-center gap-2 bg-gradient-to-r from-cyan-600 to-blue-600 text-white shadow-lg shadow-cyan-600/25 border border-cyan-400/30">
                <span>🔥</span>
                <span>Semua Layanan</span>
            </button>
            <button type="button" onclick="filterCategory('game', this)" class="category-pill px-3.5 py-2 sm:px-5 sm:py-2.5 rounded-xl sm:rounded-2xl text-xs font-bold text-gray-300 hover:text-white bg-gray-900/80 hover:bg-gray-800 border border-gray-800 transition-all flex items-center gap-2">
                <span>📱</span>
                <span>Top-Up Game</span>
            </button>
            <button type="button" onclick="filterCategory('account', this)" class="category-pill px-3.5 py-2 sm:px-5 sm:py-2.5 rounded-xl sm:rounded-2xl text-xs font-bold text-gray-300 hover:text-white bg-gray-900/80 hover:bg-gray-800 border border-gray-800 transition-all flex items-center gap-2">
                <span>🍿</span>
                <span>Akun Streaming (Netflix)</span>
            </button>
            <button type="button" onclick="filterCategory('game_account', this)" class="category-pill px-3.5 py-2 sm:px-5 sm:py-2.5 rounded-xl sm:rounded-2xl text-xs font-bold text-gray-300 hover:text-white bg-gray-900/80 hover:bg-gray-800 border border-gray-800 transition-all flex items-center gap-2">
                <span>👑</span>
                <span>Jual Beli Akun Game</span>
            </button>
            <button type="button" onclick="filterCategory('joki', this)" class="category-pill px-3.5 py-2 sm:px-5 sm:py-2.5 rounded-xl sm:rounded-2xl text-xs font-bold text-gray-300 hover:text-white bg-gray-900/80 hover:bg-gray-800 border border-gray-800 transition-all flex items-center gap-2">
                <span>⚔️</span>
                <span>Jasa Joki Rank</span>
            </button>
        </div>
    </div>

    <!-- 4. PRODUCT CATALOG GRID (solustion411 Game Cards) -->
    <div id="catalog-section" class="scroll-mt-24 mb-16">
        
        <!-- SECTION A: TOP-UP GAME (solustion411 Style Grid) -->
        <div class="product-group-section mb-12" id="group-game">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-sm border border-cyan-500/30">🎮</span>
                        <span>Top-Up Game Favorit</span>
                    </h3>
                    <p class="text-xs text-gray-400 mt-1">Isi diamond & koin game instan cukup masukkan User ID tanpa antre!</p>
                </div>
                <span class="text-xs font-semibold text-cyan-400 bg-cyan-950/40 px-3 py-1 rounded-xl border border-cyan-500/20 hidden sm:inline-block">
                    ⚡ 1-3 Detik Masuk
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-5">
                @forelse($gameProducts as $product)
                    <a href="{{ route('products.checkout', $product->slug) }}" class="product-game-card group glass-card rounded-2xl p-3.5 border border-gray-800 hover:border-cyan-500/50 hover:scale-[1.03] transition-all flex flex-col justify-between" data-category="game" data-title="{{ $product->name }}" data-publisher="{{ $product->publisher }}">
                        <div>
                            <!-- Game Poster Thumbnail -->
                            <div class="w-full aspect-[4/3] rounded-xl bg-gradient-to-tr from-slate-900 to-indigo-950 flex flex-col items-center justify-center border border-gray-800/80 mb-3 group-hover:border-cyan-500/40 transition-all overflow-hidden relative shadow-md">
                                @if($product->getThumbnailUrl())
                                    <img src="{{ $product->getThumbnailUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="text-4xl group-hover:scale-110 transition-transform">🎮</div>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">{{ $product->publisher }}</span>
                                @endif
                                
                                <!-- Top Badges -->
                                <span class="absolute top-2 right-2 text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-cyan-600/90 text-white backdrop-blur-md shadow-sm">
                                    ⚡ INSTAN
                                </span>
                            </div>

                            <h4 class="font-extrabold text-xs sm:text-sm text-white group-hover:text-cyan-400 transition-colors line-clamp-1">
                                {{ $product->name }}
                            </h4>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $product->publisher ?? 'Top-Up Otomatis' }}</p>
                        </div>

                        <!-- Price start from & Action button -->
                        <div class="mt-4 pt-2.5 border-t border-gray-800/80 flex items-center justify-between">
                            <div>
                                <span class="text-[9px] text-gray-400 block uppercase font-medium">Mulai</span>
                                <span class="text-xs font-black text-cyan-400">
                                    Rp {{ number_format($product->variants->min('price') ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-gray-800 group-hover:bg-cyan-600 text-gray-300 group-hover:text-white flex items-center justify-center text-xs font-bold transition-colors">
                                →
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full p-8 rounded-3xl glass-card border border-gray-800/80 text-center flex flex-col items-center justify-center">
                        <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-2xl mb-2 text-cyan-400">🎮</div>
                        <p class="text-xs text-gray-300 font-bold">Belum ada produk game yang aktif.</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Admin dapat menambahkan produk baru melalui panel dashboard.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- SECTION B: JUAL BELI AKUN GAME (Sultan & Smurf) -->
        <div class="product-group-section mb-12" id="group-game_account">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm border border-amber-500/30">👑</span>
                        <span>Jual Beli Akun Game Bergaransi</span>
                    </h3>
                    <p class="text-xs text-gray-400 mt-1">Akun MLBB Sultan, Smurf & FF Old. Monsep 100% Anti Hackback & Kredensial Langsung.</p>
                </div>
                <span class="text-xs font-semibold text-amber-400 bg-amber-950/40 px-3 py-1 rounded-xl border border-amber-500/20 hidden sm:inline-block">
                    🛡️ Monsep Anti Hackback
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($gameAccountProducts as $product)
                    @php
                        $thumb = $product->getThumbnailUrl();
                        $specs = $product->account_specs ?? [];
                    @endphp
                    <a href="{{ route('products.checkout', $product->slug) }}" class="product-game-card group glass-card rounded-3xl p-5 border border-gray-800 hover:border-amber-500/50 hover:scale-[1.02] transition-all flex flex-col justify-between" data-category="game_account" data-title="{{ $product->name }}" data-publisher="{{ $product->publisher }}">
                        <div>
                            <!-- Account Cover Art -->
                            <div class="w-full aspect-[16/9] rounded-2xl bg-slate-950 border border-gray-800 overflow-hidden relative mb-4">
                                @if($thumb)
                                    <img src="{{ $thumb }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-5xl">👑</div>
                                @endif
                                <span class="absolute top-2.5 left-2.5 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-black/80 text-amber-400 border border-amber-500/30 backdrop-blur-md">
                                    🛡️ Full Garansi
                                </span>
                            </div>

                            <h4 class="font-extrabold text-sm sm:text-base text-white group-hover:text-amber-400 transition-colors line-clamp-1">
                                {{ $product->name }}
                            </h4>

                            <!-- Quick Specs Badges -->
                            @if(!empty($specs))
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach(array_slice($specs, 0, 3) as $k => $v)
                                        <span class="text-[10px] px-2 py-0.5 rounded-lg bg-gray-900 border border-gray-800 text-gray-300">
                                            <strong>{{ $k }}:</strong> {{ $v }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 pt-3 border-t border-gray-800 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-gray-400 block font-medium uppercase">Harga Akun</span>
                                <span class="text-base font-black text-amber-400">
                                    Rp {{ number_format($product->variants->min('price') ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                            <span class="px-3.5 py-1.5 rounded-xl bg-amber-500/20 group-hover:bg-amber-500 text-amber-400 group-hover:text-black font-extrabold text-xs transition-colors flex items-center gap-1">
                                Beli Akun →
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full p-8 rounded-3xl glass-card border border-gray-800/80 text-center flex flex-col items-center justify-center">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-2xl mb-2 text-amber-400">👑</div>
                        <p class="text-xs text-gray-300 font-bold">Belum ada akun game yang dijual.</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Nantikan update katalog akun game sultan dan smurf terverifikasi.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- SECTION C: JASA JOKI RANK (Fast & Aman) -->
        <div class="product-group-section mb-12" id="group-joki">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/30">⚔️</span>
                        <span>Jasa Joki Rank & Push Winrate</span>
                    </h3>
                    <p class="text-xs text-gray-400 mt-1">Dikerjakan booster pro player, winrate 85%+, aman anti banned.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @forelse($jokiProducts as $product)
                    <a href="{{ route('products.checkout', $product->slug) }}" class="product-game-card group glass-card rounded-3xl p-5 border border-gray-800 hover:border-emerald-500/50 hover:scale-[1.02] transition-all flex flex-col justify-between" data-category="joki" data-title="{{ $product->name }}" data-publisher="{{ $product->publisher }}">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-3xl flex-shrink-0">
                                ⚔️
                            </div>
                            <div>
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    PRO PLAYER BOOSTER
                                </span>
                                <h4 class="font-bold text-base text-white group-hover:text-emerald-400 transition-colors mt-1">
                                    {{ $product->name }}
                                </h4>
                                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $product->description }}</p>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-t border-gray-800 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-gray-400 block uppercase">Mulai Dari</span>
                                <span class="text-sm font-black text-emerald-400">
                                    Rp {{ number_format($product->variants->min('price') ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                            <span class="px-3.5 py-1.5 rounded-xl bg-emerald-600/20 group-hover:bg-emerald-600 text-emerald-400 group-hover:text-white font-extrabold text-xs transition-colors flex items-center gap-1">
                                Pesan Joki →
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full p-8 rounded-3xl glass-card border border-gray-800/80 text-center flex flex-col items-center justify-center">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-2xl mb-2 text-emerald-400">⚔️</div>
                        <p class="text-xs text-gray-300 font-bold">Belum ada layanan joki yang aktif.</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Layanan jasa joki rank profesional akan segera hadir.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- SECTION D: AKUN PREMIUM & STREAMING -->
        <div class="product-group-section mb-12" id="group-account">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-sm border border-indigo-500/30">🍿</span>
                        <span>Akun Premium & Streaming</span>
                    </h3>
                    <p class="text-xs text-gray-400 mt-1">Netflix 4K UHD, Spotify, YouTube Premium murah dengan pilihan durasi fleksibel (1 Hari, 3 Hari, 7 Hari, 1 Bulan).</p>
                </div>
                <span class="text-xs font-semibold text-indigo-400 bg-indigo-950/40 px-3 py-1 rounded-xl border border-indigo-500/20 hidden sm:inline-block">
                    ⚡ Kredensial Otomatis
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-5">
                @forelse($accountProducts as $product)
                    <a href="{{ route('products.checkout', $product->slug) }}" class="product-game-card group glass-card rounded-3xl p-5 border border-gray-800 hover:border-indigo-500/50 hover:scale-[1.02] active:scale-[0.99] transition-all flex flex-col justify-between" data-category="account" data-title="{{ $product->name }}" data-publisher="{{ $product->publisher }}">
                        <div>
                            <div class="flex items-center justify-between gap-3 mb-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-2xl flex-shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                                        🍿
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-md bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                            {{ $product->publisher ?? 'Akun Digital' }}
                                        </span>
                                        <h4 class="font-bold text-sm sm:text-base text-white group-hover:text-indigo-400 transition-colors line-clamp-1 mt-1">
                                            {{ $product->name }}
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            @if($product->variants->isNotEmpty())
                                <!-- Duration Variant Chips Preview -->
                                <div class="mt-3 pt-3 border-t border-gray-800/80">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-1.5">Pilihan Durasi / Variasi:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($product->variants as $v)
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-lg bg-gray-900 border border-gray-800 text-indigo-300 group-hover:border-indigo-500/30 transition-colors">
                                                ⏱️ {{ Str::limit(str_replace(['- 1 Profil (Sharing)', '- Akun Private (5 Profil)', ' - Sharing', ' - Private'], '', $v->name), 14) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-800 flex items-center justify-between">
                            <div>
                                <span class="text-[9px] text-gray-400 block uppercase font-medium">Mulai Dari</span>
                                <span class="text-sm font-black text-indigo-400">
                                    Rp {{ number_format($product->variants->min('price') ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                            <span class="px-3.5 py-1.5 rounded-xl bg-indigo-600/20 group-hover:bg-indigo-600 text-indigo-400 group-hover:text-white font-extrabold text-xs transition-colors flex items-center gap-1">
                                Pilih Paket →
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full p-8 rounded-3xl glass-card border border-gray-800/80 text-center flex flex-col items-center justify-center">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-2xl mb-2 text-indigo-400">🍿</div>
                        <p class="text-xs text-gray-300 font-bold">Belum ada akun premium yang aktif saat ini.</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Admin dapat menambahkan akun Netflix, Spotify, atau YouTube Premium.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- 5. KEUNGGULAN LAYANAN (solustion411 Feature Highlights) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mt-16 pt-12 border-t border-gray-800">
        <div class="glass-card p-5 rounded-3xl border border-gray-800 hover:border-cyan-500/30 transition-all flex flex-col items-center sm:items-start text-center sm:text-left">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center text-2xl mb-3 shadow-sm">
                ⚡
            </div>
            <h4 class="text-sm font-extrabold text-white">Proses Kilat 1-3 Detik</h4>
            <p class="text-[11px] text-gray-400 mt-1 leading-relaxed">Pengiriman diamond game instan & otomatis 24 jam nonstop.</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-gray-800 hover:border-amber-500/30 transition-all flex flex-col items-center sm:items-start text-center sm:text-left">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-2xl mb-3 shadow-sm">
                🛡️
            </div>
            <h4 class="text-sm font-extrabold text-white">100% Legal & Bergaransi</h4>
            <p class="text-[11px] text-gray-400 mt-1 leading-relaxed">Diamond resmi aman dari ban dan akun game monsep anti hackback.</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-gray-800 hover:border-emerald-500/30 transition-all flex flex-col items-center sm:items-start text-center sm:text-left">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl mb-3 shadow-sm">
                💳
            </div>
            <h4 class="text-sm font-extrabold text-white">Metode Bayar Lengkap</h4>
            <p class="text-[11px] text-gray-400 mt-1 leading-relaxed">QRIS, BCA, Mandiri, BRI, DANA, OVO, ShopeePay hingga Indomaret.</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-gray-800 hover:border-purple-500/30 transition-all flex flex-col items-center sm:items-start text-center sm:text-left">
            <div class="w-12 h-12 rounded-2xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center text-2xl mb-3 shadow-sm">
                💬
            </div>
            <h4 class="text-sm font-extrabold text-white">Customer Support 24/7</h4>
            <p class="text-[11px] text-gray-400 mt-1 leading-relaxed">Tim bantuan ramah & responsif siap membantu Anda melalui WhatsApp.</p>
        </div>
    </div>

</div>

<!-- Auto Slider & Category Filter Logic -->
<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.banner-slide');
    const dots = document.querySelectorAll('.slider-dot');
    let sliderTimer = null;

    function showSlide(index) {
        if (!slides.length) return;
        if (index >= slides.length) currentSlide = 0;
        else if (index < 0) currentSlide = slides.length - 1;
        else currentSlide = index;

        slides.forEach((slide, i) => {
            if (i === currentSlide) {
                slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                slide.classList.add('opacity-100', 'z-10');
            } else {
                slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
                slide.classList.remove('opacity-100', 'z-10');
            }
        });

        dots.forEach((dot, i) => {
            if (i === currentSlide) {
                dot.classList.add('bg-cyan-400', 'w-8');
                dot.classList.remove('bg-gray-600');
            } else {
                dot.classList.remove('bg-cyan-400', 'w-8');
                dot.classList.add('bg-gray-600');
            }
        });
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    function goToSlide(index) {
        showSlide(index);
        resetSliderTimer();
    }

    function resetSliderTimer() {
        if (sliderTimer) clearInterval(sliderTimer);
        sliderTimer = setInterval(nextSlide, 5000);
    }

    // Initialize Auto Slider
    if (slides.length > 1) {
        resetSliderTimer();
        const sliderContainer = document.getElementById('hero-slider-container');
        if (sliderContainer) {
            sliderContainer.addEventListener('mouseenter', () => clearInterval(sliderTimer));
            sliderContainer.addEventListener('mouseleave', resetSliderTimer);
        }
    }

    // Category Filter Logic
    function filterCategory(category, buttonEl) {
        // Highlight active pill
        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.classList.remove('active-pill', 'bg-gradient-to-r', 'from-cyan-600', 'to-blue-600', 'text-white', 'shadow-lg', 'shadow-cyan-600/25', 'border-cyan-400/30');
            pill.classList.add('text-gray-300', 'bg-gray-900/80', 'border-gray-800');
        });

        buttonEl.classList.add('active-pill', 'bg-gradient-to-r', 'from-cyan-600', 'to-blue-600', 'text-white', 'shadow-lg', 'shadow-cyan-600/25', 'border-cyan-400/30');
        buttonEl.classList.remove('text-gray-300', 'bg-gray-900/80', 'border-gray-800');

        // Show/hide sections
        const groups = document.querySelectorAll('.product-group-section');
        groups.forEach(group => {
            if (category === 'all') {
                group.style.display = '';
            } else if (group.id === 'group-' + category) {
                group.style.display = '';
            } else {
                group.style.display = 'none';
            }
        });
    }

    // Flash Sale Live Countdown Timer
    const fsTimerEl = document.getElementById('flash-sale-timer');
    if (fsTimerEl && fsTimerEl.dataset.endtime) {
        const targetTime = new Date(fsTimerEl.dataset.endtime).getTime();
        function updateFSTimer() {
            const now = new Date().getTime();
            const diff = targetTime - now;
            if (diff <= 0) {
                const elH = document.getElementById('fs-hours');
                const elM = document.getElementById('fs-minutes');
                const elS = document.getElementById('fs-seconds');
                if (elH) elH.textContent = '00';
                if (elM) elM.textContent = '00';
                if (elS) elS.textContent = '00';
                return;
            }
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            const elH = document.getElementById('fs-hours');
            const elM = document.getElementById('fs-minutes');
            const elS = document.getElementById('fs-seconds');
            if (elH) elH.textContent = String(hours).padStart(2, '0');
            if (elM) elM.textContent = String(minutes).padStart(2, '0');
            if (elS) elS.textContent = String(seconds).padStart(2, '0');
        }
        updateFSTimer();
        setInterval(updateFSTimer, 1000);
    }
</script>
@endsection

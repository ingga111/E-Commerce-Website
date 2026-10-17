<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'solustion411 - Top Up Game & Akun Digital Murah, Cepat & Terpercaya' }}</title>
    <meta name="description" content="Pusat Top Up Diamond Mobile Legends, Free Fire, Valorant, Jual Beli Akun Sultan & Smurf bergaransi, dan Akun Premium murah 24 jam nonstop proses 1-3 detik.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Instant Theme Detection Script (Anti-FOUC) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark) || savedTheme === null) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            400: '#22d3ee',
                            500: '#06b6d4',
                            600: '#0891b2',
                            700: '#0e7490',
                        },
                        darkbg: '#0B0E14',
                        darkcard: '#121824',
                        darkborder: '#1E293B',
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --bg-page: #F8FAFC;
            --text-primary: #0F172A;
            --text-secondary: #475569;
            --card-bg: rgba(255, 255, 255, 0.95);
            --card-border: #E2E8F0;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            --header-bg: rgba(255, 255, 255, 0.9);
            --footer-bg: #F1F5F9;
        }

        html.dark {
            --bg-page: #0B0E14;
            --text-primary: #F8FAFC;
            --text-secondary: #94A3B8;
            --card-bg: rgba(18, 24, 36, 0.85);
            --card-border: rgba(255, 255, 255, 0.08);
            --card-shadow: 0 0 25px -5px rgba(6, 182, 212, 0.15);
            --header-bg: rgba(11, 14, 20, 0.88);
            --footer-bg: #07090E;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(14px);
            border: 1px solid var(--card-border);
            box-shadow: var(--card-shadow);
        }

        .glow-cyan {
            box-shadow: 0 0 20px -3px rgba(6, 182, 212, 0.35);
        }

        .glow-amber {
            box-shadow: 0 0 20px -3px rgba(245, 158, 11, 0.35);
        }

        /* Running Ticker Animation */
        @keyframes tickerMove {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .ticker-inner {
            display: inline-flex;
            white-space: nowrap;
            animation: tickerMove 28s linear infinite;
        }
        .ticker-inner:hover {
            animation-play-state: paused;
        }

        /* Light Mode Styling Overrides */
        html:not(.dark) .text-white {
            color: #0F172A !important;
        }
        html:not(.dark) .text-gray-200 {
            color: #1E293B !important;
        }
        html:not(.dark) .text-gray-300 {
            color: #334155 !important;
        }
        html:not(.dark) .text-gray-400 {
            color: #64748B !important;
        }
        html:not(.dark) .border-gray-800,
        html:not(.dark) .border-gray-800\/80,
        html:not(.dark) .border-gray-800\/60 {
            border-color: #E2E8F0 !important;
        }
        html:not(.dark) .border-gray-700 {
            border-color: #CBD5E1 !important;
        }
        html:not(.dark) .bg-gray-950,
        html:not(.dark) .bg-gray-900,
        html:not(.dark) .bg-gray-900\/80,
        html:not(.dark) .bg-gray-900\/60,
        html:not(.dark) .bg-gray-900\/40 {
            background-color: #F1F5F9 !important;
        }
        html:not(.dark) .bg-gray-800 {
            background-color: #E2E8F0 !important;
            color: #1E293B !important;
        }
        html:not(.dark) input,
        html:not(.dark) select,
        html:not(.dark) textarea {
            background-color: #FFFFFF !important;
            color: #0F172A !important;
            border-color: #CBD5E1 !important;
        }
        html:not(.dark) footer.bg-\[\#07090E\] {
            background-color: #F1F5F9 !important;
            border-color: #E2E8F0 !important;
        }

        @supports (padding-bottom: env(safe-area-inset-bottom)) {
            .mobile-bottom-nav {
                padding-bottom: max(0.6rem, env(safe-area-inset-bottom));
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased selection:bg-cyan-500 selection:text-white {{ request()->routeIs('products.checkout') || request()->routeIs('orders.show') ? 'pb-24 sm:pb-28' : 'pb-20 md:pb-0' }}">

    <!-- RUNNING TICKER ANNOUNCEMENT BAR (solustion411 Signature) -->
    <div class="bg-gradient-to-r from-cyan-950 via-slate-900 to-indigo-950 border-b border-cyan-500/20 py-1.5 px-3 sm:px-4 text-xs overflow-hidden relative z-50">
        <div class="max-w-7xl mx-auto flex items-center gap-2.5 sm:gap-3">
            <span class="flex-shrink-0 flex items-center gap-1.5 px-2 py-0.5 sm:px-2.5 rounded-full text-[9px] sm:text-[10px] font-extrabold uppercase bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                <span class="animate-pulse">⚡</span> INFO RESMI
            </span>
            <div class="overflow-hidden whitespace-nowrap flex-1">
                <div class="ticker-inner text-[10px] sm:text-[11px] font-medium text-gray-300">
                    <span class="mx-5 sm:mx-6">⚡ Proses Otomatis 1-3 Detik 24 Jam Nonstop!</span>
                    <span class="mx-5 sm:mx-6">🎮 Top Up Diamond Mobile Legends, Free Fire, Valorant, & Genshin Impact Termurah!</span>
                    <span class="mx-5 sm:mx-6">👑 Jual Beli Akun Sultan & Smurf Bergaransi Anti-Hackback 100%!</span>
                    <span class="mx-5 sm:mx-6">🍿 Akun Netflix 4K UHD Pilihan 1 Hari, 3 Hari, 7 Hari & 1 Bulan Siap Pakai!</span>
                    <span class="mx-5 sm:mx-6">💬 CS Aktif Siap Membantu via WhatsApp Resmi.</span>
                    <!-- Marquee loop duplicate -->
                    <span class="mx-5 sm:mx-6">⚡ Proses Otomatis 1-3 Detik 24 Jam Nonstop!</span>
                    <span class="mx-5 sm:mx-6">🎮 Top Up Diamond Mobile Legends, Free Fire, Valorant, & Genshin Impact Termurah!</span>
                    <span class="mx-5 sm:mx-6">👑 Jual Beli Akun Sultan & Smurf Bergaransi Anti-Hackback 100%!</span>
                    <span class="mx-5 sm:mx-6">🍿 Akun Netflix 4K UHD Pilihan 1 Hari, 3 Hari, 7 Hari & 1 Bulan Siap Pakai!</span>
                    <span class="mx-5 sm:mx-6">💬 CS Aktif Siap Membantu via WhatsApp Resmi.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN NAVBAR (solustion411 Header) -->
    <header class="sticky top-0 z-40 glass-card border-b border-gray-800/80 transition-all">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14 sm:h-20 gap-2 sm:gap-4">
                
                <!-- Brand Logo -->
                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 group">
                        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-cyan-500 via-blue-600 to-indigo-600 flex items-center justify-center font-black text-white text-base sm:text-xl shadow-lg shadow-cyan-500/25 group-hover:scale-105 transition-transform flex-shrink-0">
                            ⚡
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1 sm:gap-1.5">
                                <span class="font-black text-base sm:text-2xl text-white tracking-tight leading-none group-hover:text-cyan-400 transition-colors">SOLUSTION<span class="text-cyan-400">411</span></span>
                                <span class="px-1 py-0.2 sm:px-1.5 sm:py-0.5 rounded text-[7px] sm:text-[8px] font-extrabold uppercase bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">ID</span>
                            </div>
                            <span class="text-[8px] sm:text-[10px] text-gray-400 tracking-wider font-medium uppercase mt-0.5 truncate max-w-[140px] sm:max-w-none">Top-Up Game &amp; Digital Store</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Search Bar -->
                <div class="hidden lg:flex flex-1 max-w-md mx-6">
                    <div class="relative w-full">
                        <input type="text" id="global-game-search" oninput="filterGamesSearch(this.value)" placeholder="Cari game, voucher, atau produk..." class="w-full bg-gray-900/90 border border-gray-700/80 rounded-2xl pl-10 pr-4 py-2.5 text-xs text-gray-200 placeholder-gray-400 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                        <div class="absolute left-3.5 top-3 text-gray-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="{{ route('home') }}" class="px-3 py-2 rounded-xl text-xs font-bold text-gray-200 hover:text-cyan-400 hover:bg-gray-800/50 transition-all flex items-center gap-1.5">
                        <span>🏠</span>
                        <span>Beranda</span>
                    </a>
                    
                    <a href="{{ route('home') }}#games" class="px-3 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-cyan-400 hover:bg-gray-800/50 transition-all">
                        Top-Up Game
                    </a>

                    <!-- Track Order Button (Modal Trigger) -->
                    <button type="button" onclick="document.getElementById('modal-track-order').classList.remove('hidden')" class="px-3 py-2 rounded-xl text-xs font-semibold text-cyan-400 hover:text-cyan-300 hover:bg-cyan-950/30 transition-all flex items-center gap-1.5 border border-cyan-500/20">
                        <span>🔍</span>
                        <span>Lacak Pesanan</span>
                    </button>

                    <!-- MLBB Calculator Button (Modal Trigger) -->
                    <button type="button" onclick="document.getElementById('modal-calculator-mlbb').classList.remove('hidden')" class="px-3 py-2 rounded-xl text-xs font-semibold text-amber-400 hover:text-amber-300 hover:bg-amber-950/30 transition-all flex items-center gap-1.5 border border-amber-500/20">
                        <span>🧮</span>
                        <span>Kalkulator ML</span>
                    </button>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-xl text-xs font-bold text-amber-300 hover:text-amber-200 bg-amber-500/10 border border-amber-500/20 transition-all">
                                ⚡ Admin
                            </a>
                        @endif
                    @endauth
                </nav>

                <!-- Search (Mobile Trigger), Theme Toggle, & Auth -->
                <div class="flex items-center gap-2.5">
                    <!-- Quick Track Order Icon (Mobile) -->
                    <button type="button" onclick="document.getElementById('modal-track-order').classList.remove('hidden')" class="md:hidden w-9 h-9 rounded-xl border border-gray-700 bg-gray-900/60 flex items-center justify-center text-cyan-400" title="Lacak Pesanan">
                        🔍
                    </button>

                    <!-- THEME TOGGLE BUTTON -->
                    <button id="theme-toggle" type="button" aria-label="Ganti Mode Terang atau Gelap" class="w-9 h-9 rounded-xl border border-gray-700 bg-gray-900/60 dark:bg-gray-800/80 flex items-center justify-center text-gray-300 hover:text-cyan-400 dark:hover:text-amber-300 hover:scale-105 active:scale-95 transition-all shadow-sm">
                        <span id="theme-toggle-dark-icon" class="text-base select-none">🌙</span>
                        <span id="theme-toggle-light-icon" class="hidden text-base select-none">☀️</span>
                    </button>

                    <!-- Profile / Auth Button -->
                    @auth
                        <div class="relative" id="profile-dropdown-container">
                            <button id="profile-btn" type="button" aria-expanded="false" aria-label="Menu Profil" class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyan-600 to-indigo-600 p-[1.5px] hover:scale-105 active:scale-95 transition-all shadow-md shadow-cyan-600/20 flex items-center justify-center focus:outline-none">
                                <div class="w-full h-full bg-gray-900 rounded-[10px] flex items-center justify-center text-white">
                                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="profile-menu" class="hidden absolute right-0 mt-2 w-64 glass-card rounded-2xl p-2 border border-gray-800 shadow-2xl shadow-black/50 z-50">
                                <div class="px-3 py-2.5 border-b border-gray-800/80 mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs text-white truncate block">{{ auth()->user()->name }}</span>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase {{ auth()->user()->isAdmin() ? 'bg-amber-500/20 text-amber-400' : 'bg-cyan-500/20 text-cyan-400' }}">
                                            {{ auth()->user()->isAdmin() ? 'Admin' : 'Member' }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-gray-400 truncate block mt-0.5">{{ auth()->user()->email }}</span>
                                </div>

                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-white hover:bg-gray-800/80 transition-all">
                                    <span>👤</span>
                                    <span>Profil Saya</span>
                                </a>

                                <a href="{{ route('customer.orders.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-white hover:bg-gray-800/80 transition-all">
                                    <span>📦</span>
                                    <span>Pesanan Saya</span>
                                </a>

                                @if(auth()->user()->isAdmin())
                                    <div class="my-1 border-t border-gray-800/80"></div>
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-amber-300 hover:text-amber-200 hover:bg-amber-950/30 transition-all">
                                        <span>⚡</span>
                                        <span>Dashboard Admin</span>
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-purple-300 hover:text-purple-200 hover:bg-purple-950/30 transition-all">
                                        <span>👥</span>
                                        <span>Kelola User & Admin</span>
                                    </a>
                                    <a href="{{ route('admin.banners.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-cyan-300 hover:text-cyan-200 hover:bg-cyan-950/30 transition-all">
                                        <span>🖼️</span>
                                        <span>Kelola Banner Slide</span>
                                    </a>
                                    <a href="{{ route('admin.flash-sales.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-amber-300 hover:text-amber-200 hover:bg-amber-950/30 transition-all">
                                        <span>🔥</span>
                                        <span>Kelola Flash Sale</span>
                                    </a>
                                    <a href="{{ route('admin.vouchers.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-emerald-300 hover:text-emerald-200 hover:bg-emerald-950/30 transition-all">
                                        <span>🎟️</span>
                                        <span>Kelola Voucher</span>
                                    </a>
                                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-white hover:bg-gray-800/80 transition-all">
                                        <span>📋</span>
                                        <span>Verifikasi Pesanan</span>
                                    </a>
                                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-white hover:bg-gray-800/80 transition-all">
                                        <span>📦</span>
                                        <span>Kelola Produk</span>
                                    </a>
                                    <a href="{{ route('admin.payment-methods.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-white hover:bg-gray-800/80 transition-all">
                                        <span>💳</span>
                                        <span>Rekening Toko</span>
                                    </a>
                                @endif

                                <div class="my-1 border-t border-gray-800/80"></div>

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-rose-400 hover:bg-rose-950/40 transition-all">
                                        <span>🚪</span>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest Login Button -->
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold shadow-md shadow-cyan-600/20 transition-all flex items-center gap-1.5">
                            <span>Masuk</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs shadow-sm">
                <span class="text-base">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="bg-rose-950/60 border border-rose-500/40 text-rose-300 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs shadow-sm">
                <span class="text-base">✕</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content Body -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- FOOTER (solustion411 Style) -->
    <footer class="border-t border-gray-800 bg-[#07090E] mt-20 pt-14 pb-20 md:pb-12 text-xs transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-16 pb-12 border-b border-gray-800/80">
                <!-- Col 1: Store Bio -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center font-black text-white text-base shadow-md shadow-cyan-500/30">
                            ⚡
                        </div>
                        <span class="font-black text-xl text-white tracking-tight">SOLUSTION<span class="text-cyan-400">411</span></span>
                    </div>
                    <p class="text-gray-400 leading-relaxed text-[11px] max-w-lg">
                        Platform top up diamond game instan, voucher digital, jasa joki rank, dan jual beli akun game terpercaya dengan sistem otomatis 24 jam nonstop se-Indonesia.
                    </p>
                    <div class="flex items-center gap-3 text-gray-400">
                        <span class="p-2 rounded-xl bg-gray-900 border border-gray-800 text-cyan-400 text-sm">⚡ 1-3 Detik</span>
                        <span class="p-2 rounded-xl bg-gray-900 border border-gray-800 text-emerald-400 text-sm">🛡️ 100% Legal</span>
                    </div>
                </div>

                <!-- Col 2: Layanan Pelanggan -->
                <div class="space-y-3">
                    <h4 class="font-bold text-white text-sm uppercase tracking-wider mb-2 flex items-center gap-2">
                        <span>💬</span> Bantuan & Kontak
                    </h4>
                    <p class="text-gray-400 text-[11px]">Butuh bantuan transaksi atau konfirmasi pesanan? Customer Service kami siap melayani Anda.</p>
                    <div class="flex flex-col sm:flex-row gap-3 pt-1">
                        <a href="https://wa.me/6281234567890?text=Halo%20Admin%20solustion411,%20saya%20butuh%20bantuan%20terkait%20pesanan" target="_blank" class="px-5 py-2.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 text-xs font-bold transition-all flex items-center justify-center gap-2">
                            <span>📱 WhatsApp CS 24 Jam</span>
                        </a>
                        <button type="button" onclick="document.getElementById('modal-track-order').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl bg-cyan-600/20 hover:bg-cyan-600/30 text-cyan-400 border border-cyan-500/30 text-xs font-bold transition-all flex items-center justify-center gap-2">
                            <span>🔍 Cek Status Pesanan</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-gray-500 text-[11px]">
                <p>&copy; {{ date('Y') }} solustion411. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex items-center gap-4 text-gray-400">
                    <span class="hover:text-cyan-400 cursor-pointer">Syarat & Ketentuan</span>
                    <span>•</span>
                    <span class="hover:text-cyan-400 cursor-pointer">Kebijakan Privasi</span>
                    <span>•</span>
                    <span class="hover:text-cyan-400 cursor-pointer">Garansi Anti-Hackback</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- MOBILE BOTTOM NAVIGATION (solustion411 Mobile Navigation) -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-gray-950/95 backdrop-blur-md border-t border-gray-800 px-4 py-2">
        <div class="flex items-center justify-around text-center">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 text-cyan-400">
                <span class="text-lg">🏠</span>
                <span class="text-[10px] font-bold">Beranda</span>
            </a>
            <button type="button" onclick="document.getElementById('modal-track-order').classList.remove('hidden')" class="flex flex-col items-center gap-1 text-gray-400 hover:text-white">
                <span class="text-lg">🔍</span>
                <span class="text-[10px] font-medium">Lacak</span>
            </button>
            <button type="button" onclick="document.getElementById('modal-calculator-mlbb').classList.remove('hidden')" class="flex flex-col items-center gap-1 text-gray-400 hover:text-white">
                <span class="text-lg">🧮</span>
                <span class="text-[10px] font-medium">Hitung WR</span>
            </button>
            @auth
                <a href="{{ route('customer.orders.index') }}" class="flex flex-col items-center gap-1 text-gray-400 hover:text-white">
                    <span class="text-lg">📦</span>
                    <span class="text-[10px] font-medium">Pesanan</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 text-gray-400 hover:text-white">
                    <span class="text-lg">👤</span>
                    <span class="text-[10px] font-medium">Masuk</span>
                </a>
            @endauth
        </div>
    </div>

    <!-- MODAL 1: LACAK PESANAN / CEK TRANSAKSI (solustion411 Feature) -->
    <div id="modal-track-order" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4">
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800 max-w-md w-full">
            <div class="flex items-center justify-between pb-4 border-b border-gray-800 mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🔍</span>
                    <h3 class="text-base font-extrabold text-white">Lacak Status Pesanan</h3>
                </div>
                <button type="button" onclick="document.getElementById('modal-track-order').classList.add('hidden')" class="text-gray-400 hover:text-white text-lg">✕</button>
            </div>

            <p class="text-xs text-gray-400 mb-4 leading-relaxed">
                Masukkan Nomor Invoice yang Anda dapatkan saat melakukan pemesanan (Contoh: <code class="text-cyan-400 font-mono">INV-20261003-XXXX</code>).
            </p>

            <form onsubmit="event.preventDefault(); const inv = document.getElementById('input-track-inv').value.trim(); if(inv) window.location.href='/invoice/' + inv;" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nomor Invoice <span class="text-rose-400">*</span></label>
                    <input type="text" id="input-track-inv" required placeholder="INV-XXXXXXXX-XXXX" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white font-mono focus:outline-none focus:border-cyan-500 uppercase">
                </div>

                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-track-order').classList.add('hidden')" class="px-4 py-2.5 rounded-xl bg-gray-800 text-gray-300 text-xs font-semibold hover:bg-gray-700">Tutup</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold shadow-lg shadow-cyan-600/30">Cek Status Sekarang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: KALKULATOR WIN RATE MLBB (solustion411 Gamer Feature) -->
    <div id="modal-calculator-mlbb" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4">
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800 max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-gray-800 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🧮</span>
                    <h3 class="text-base font-extrabold text-white">Kalkulator Win Rate Mobile Legends</h3>
                </div>
                <button type="button" onclick="document.getElementById('modal-calculator-mlbb').classList.add('hidden')" class="text-gray-400 hover:text-white text-lg">✕</button>
            </div>

            <p class="text-xs text-gray-400 mb-5">
                Hitung berapa kali kemenangan beruntun (win streak) yang Anda butuhkan untuk mencapai target win rate impian Anda!
            </p>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Total Pertandingan Saat Ini</label>
                    <input type="number" id="calc-total-match" placeholder="Contoh: 350" min="1" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Win Rate Saat Ini (%)</label>
                        <input type="number" step="0.1" id="calc-current-wr" placeholder="Contoh: 51.5" min="0" max="100" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Target Win Rate (%)</label>
                        <input type="number" step="0.1" id="calc-target-wr" placeholder="Contoh: 65" min="1" max="100" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                    </div>
                </div>

                <button type="button" onclick="calculateWinRate()" class="w-full py-3 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-black font-extrabold text-xs shadow-lg shadow-amber-500/20 transition-all">
                    Hitung Kemenangan
                </button>

                <!-- Result Box -->
                <div id="calc-result" class="hidden p-4 rounded-2xl bg-cyan-950/30 border border-cyan-500/30 text-center">
                    <span class="text-xs text-gray-300 block">Anda membutuhkan sekitar:</span>
                    <span id="calc-wins-needed" class="text-2xl font-black text-cyan-400 my-1 block">0 Win Streak</span>
                    <span class="text-[11px] text-gray-400 block">kemenangan tanpa kalah untuk mencapai target win rate.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Theme Toggle Logic
            const themeToggleBtn = document.getElementById('theme-toggle');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');

            function syncThemeIcon() {
                const isDark = document.documentElement.classList.contains('dark');
                if (isDark) {
                    darkIcon.classList.add('hidden');
                    lightIcon.classList.remove('hidden');
                } else {
                    lightIcon.classList.add('hidden');
                    darkIcon.classList.remove('hidden');
                }
            }

            syncThemeIcon();

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function () {
                    const isDark = document.documentElement.classList.contains('dark');
                    if (isDark) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                    syncThemeIcon();
                });
            }

            // Profile Dropdown
            const profileBtn = document.getElementById('profile-btn');
            const profileMenu = document.getElementById('profile-menu');

            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isHidden = profileMenu.classList.contains('hidden');
                    if (isHidden) {
                        profileMenu.classList.remove('hidden');
                        profileBtn.setAttribute('aria-expanded', 'true');
                    } else {
                        profileMenu.classList.add('hidden');
                        profileBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                document.addEventListener('click', function (e) {
                    if (!profileMenu.contains(e.target) && !profileBtn.contains(e.target)) {
                        profileMenu.classList.add('hidden');
                        profileBtn.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });

        // MLBB Calculator Logic
        function calculateWinRate() {
            const totalMatch = parseFloat(document.getElementById('calc-total-match').value);
            const currentWr = parseFloat(document.getElementById('calc-current-wr').value);
            const targetWr = parseFloat(document.getElementById('calc-target-wr').value);
            const resBox = document.getElementById('calc-result');
            const resText = document.getElementById('calc-wins-needed');

            if (isNaN(totalMatch) || isNaN(currentWr) || isNaN(targetWr)) {
                alert('Silakan lengkapi semua kolom pertandingan & win rate.');
                return;
            }

            if (targetWr <= currentWr) {
                alert('Target win rate harus lebih tinggi daripada win rate Anda saat ini!');
                return;
            }

            if (targetWr >= 100) {
                alert('Target win rate tidak bisa mencapai 100% jika sudah pernah kalah.');
                return;
            }

            // Formula: wins = (targetWR * total - 100 * currentWins) / (100 - targetWR)
            const currentWins = (currentWr / 100) * totalMatch;
            const winsNeeded = Math.ceil(((targetWr * totalMatch) - (100 * currentWins)) / (100 - targetWr));

            resText.innerText = winsNeeded + ' Kemenangan Beruntun';
            resBox.classList.remove('hidden');
        }

        // Live Search Filter for Games
        function filterGamesSearch(query) {
            const term = query.toLowerCase().trim();
            const cards = document.querySelectorAll('.product-game-card');
            cards.forEach(card => {
                const title = card.getAttribute('data-title') || '';
                const pub = card.getAttribute('data-publisher') || '';
                if (title.toLowerCase().includes(term) || pub.toLowerCase().includes(term)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>

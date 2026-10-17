@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-indigo-400">Beranda</a>
        <span>/</span>
        <span class="text-white">Profil Pengguna</span>
    </div>

    <!-- User Header Profile Card -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800 mb-8 glow-effect">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <!-- Profile Avatar with Gradient Ring -->
            <div class="relative">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-cyan-400 p-1 shadow-xl shadow-indigo-600/30 flex-shrink-0">
                    <div class="w-full h-full bg-gray-950 rounded-xl flex items-center justify-center text-3xl font-extrabold text-white">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                <span class="absolute bottom-0 right-0 w-5 h-5 rounded-full bg-emerald-500 border-2 border-gray-950" title="Akun Aktif"></span>
            </div>

            <!-- Profile Info -->
            <div class="flex-1 text-center sm:text-left">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-1.5">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white">{{ $user->name }}</h1>
                    <span class="inline-flex self-center sm:self-auto px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider
                        {{ $user->isAdmin() ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30' }}">
                        {{ $user->isAdmin() ? '⚡ Administrator' : '👤 Customer' }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 flex items-center justify-center sm:justify-start gap-3">
                    <span>📧 {{ $user->email }}</span>
                    <span>•</span>
                    <span>Bergabung sejak {{ $user->created_at->format('M Y') }}</span>
                </p>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-5">
                    <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-800 text-center sm:text-left">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Total Transaksi</span>
                        <span class="text-lg font-extrabold text-indigo-400">{{ $totalOrders }} Pesanan</span>
                    </div>
                    <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-800 text-center sm:text-left">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Pesanan Selesai</span>
                        <span class="text-lg font-extrabold text-emerald-400">{{ $completedOrders }} Berhasil</span>
                    </div>
                    <div class="col-span-2 sm:col-span-1 bg-gray-900/60 p-3 rounded-xl border border-gray-800 text-center sm:text-left">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider block">WhatsApp Terhubung</span>
                        <span class="text-xs font-mono font-bold text-cyan-300 block mt-1">
                            {{ $user->phone ?? 'Belum Diatur' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Edit Forms -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Edit Personal Information -->
        <div class="lg:col-span-7">
            <form action="{{ route('profile.update') }}" method="POST" class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>👤</span> Informasi Data Diri
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Perbarui nama lengkap, kontak WhatsApp, dan alamat email Anda.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Lengkap <span class="text-rose-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="Nama Anda" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                        @error('name')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Alamat Email <span class="text-rose-400">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="email@gmail.com" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                        <span class="text-[10px] text-gray-500 mt-1 block">Bukti transaksi & kredensial akun digital dikirim ke email ini.</span>
                        @error('email')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nomor WhatsApp Aktif</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08123456789" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white font-mono focus:outline-none focus:border-indigo-500 transition-colors">
                        <span class="text-[10px] text-gray-500 mt-1 block">Digunakan oleh admin / tim joki untuk konfirmasi pesanan akun atau joki.</span>
                        @error('phone')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-800">
                    <h4 class="text-sm font-bold text-white mb-1 flex items-center gap-2">
                        <span>🔒</span> Ganti Password (Opsional)
                    </h4>
                    <p class="text-xs text-gray-400 mb-4">Kosongkan kolom di bawah jika Anda tidak ingin mengubah password.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5">Password Saat Ini</label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password" placeholder="Masukkan password lama Anda" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 pr-11 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                                <button type="button" onclick="togglePasswordVisibility('current_password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-indigo-400 focus:outline-none transition-colors" title="Lihat/Sembunyikan Password" aria-label="Lihat Password">
                                    <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Password Baru</label>
                                <div class="relative">
                                    <input type="password" name="password" id="profile_password" placeholder="Minimal 8 karakter" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 pr-11 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                                    <button type="button" onclick="togglePasswordVisibility('profile_password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-indigo-400 focus:outline-none transition-colors" title="Lihat/Sembunyikan Password" aria-label="Lihat Password">
                                        <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Konfirmasi Password Baru</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="profile_password_confirmation" placeholder="Ulangi password baru" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 pr-11 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                                    <button type="button" onclick="togglePasswordVisibility('profile_password_confirmation', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-indigo-400 focus:outline-none transition-colors" title="Lihat/Sembunyikan Konfirmasi Password" aria-label="Lihat Konfirmasi Password">
                                        <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/30 transition-all flex items-center gap-2">
                        <span>Simpan Perubahan Profil</span> ➜
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Recent Orders & Navigation Shortcuts -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Quick Actions Card -->
            <div class="glass-card rounded-3xl p-6 border border-gray-800 space-y-3">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-2">Menu Cepat</h3>

                @if($user->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 text-amber-300 text-xs font-bold transition-all">
                        <span class="flex items-center gap-2.5">
                            <span class="text-base">⚡</span> Dashboard Admin
                        </span>
                        <span>➜</span>
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-gray-900 hover:bg-gray-800 border border-gray-800 text-gray-200 text-xs font-semibold transition-all">
                        <span class="flex items-center gap-2.5">
                            <span class="text-base">📋</span> Verifikasi Pesanan Pembeli
                        </span>
                        <span>➜</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-gray-900 hover:bg-gray-800 border border-gray-800 text-purple-300 text-xs font-semibold transition-all">
                        <span class="flex items-center gap-2.5">
                            <span class="text-base">👥</span> Kelola User & Admin
                        </span>
                        <span>➜</span>
                    </a>
                @endif

                <a href="{{ route('customer.orders.index') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-gray-900 hover:bg-gray-800 border border-gray-800 text-gray-200 text-xs font-semibold transition-all">
                    <span class="flex items-center gap-2.5">
                        <span class="text-base">📦</span> Riwayat Pesanan Saya
                    </span>
                    <span>➜</span>
                </a>

                <a href="{{ route('home') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-gray-900 hover:bg-gray-800 border border-gray-800 text-gray-200 text-xs font-semibold transition-all">
                    <span class="flex items-center gap-2.5">
                        <span class="text-base">🎮</span> Katalog Top-Up & Akun
                    </span>
                    <span>➜</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="pt-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-rose-950/40 hover:bg-rose-900/60 border border-rose-500/30 text-rose-400 text-xs font-bold transition-all">
                        <span>🚪 Keluar dari Akun</span>
                    </button>
                </form>
            </div>

            <!-- Recent Orders Preview -->
            <div class="glass-card rounded-3xl p-6 border border-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Pesanan Terakhir</h3>
                    <a href="{{ route('customer.orders.index') }}" class="text-xs text-indigo-400 hover:underline">Lihat Semua</a>
                </div>

                <div class="space-y-3">
                    @forelse($recentOrders as $order)
                        <a href="{{ route('orders.show', $order->order_number) }}" class="block p-3 rounded-xl bg-gray-900/60 border border-gray-800/80 hover:border-gray-700 transition-all">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-mono text-xs font-bold text-indigo-400">#{{ $order->order_number }}</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                                    {{ $order->status->value === 'completed' ? 'bg-emerald-500/20 text-emerald-400' : ($order->status->value === 'processing' ? 'bg-cyan-500/20 text-cyan-400' : 'bg-amber-500/20 text-amber-400') }}">
                                    {{ $order->status->label() }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-gray-400">
                                <span>{{ $order->items->first()?->product_name ?? 'Pesanan' }}</span>
                                <span class="font-bold text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </a>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">Belum ada riwayat pesanan.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        
        const eyeIcon = btn.querySelector('.eye-icon');
        const eyeOffIcon = btn.querySelector('.eye-off-icon');
        if (eyeIcon && eyeOffIcon) {
            if (isPassword) {
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    }
</script>
@endpush

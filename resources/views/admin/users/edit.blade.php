@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.users.index') }}" class="hover:text-indigo-400">Manajemen User</a>
        <span>/</span>
        <span class="text-white">Edit User: {{ $user->name }}</span>
    </div>

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Edit Data Pengguna</h1>
            <p class="text-xs text-gray-400 mt-1">Perbarui data profil, peran sistem, atau ubah kata sandi pengguna.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold border border-gray-700 transition-all flex items-center gap-1.5">
            <span>← Kembali</span>
        </a>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label for="name" class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Lengkap <span class="text-rose-400">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required placeholder="Contoh: Admin CS / Ahmad Dani" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                @error('name')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-gray-300 mb-1.5">Alamat Email <span class="text-rose-400">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required placeholder="nama@email.com" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                <span class="text-[10px] text-gray-500 mt-1 block">Digunakan untuk login ke sistem</span>
                @error('email')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-gray-300 mb-1.5">Nomor WhatsApp / HP</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white font-mono focus:outline-none focus:border-cyan-500 transition-colors">
                <span class="text-[10px] text-gray-500 mt-1 block">Kontak WhatsApp aktif (opsional)</span>
                @error('phone')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Role Selection -->
        <div>
            <label class="block text-xs font-semibold text-gray-300 mb-2">Pilih Peran Akun (Role) <span class="text-rose-400">*</span></label>
            
            @if($user->id === auth()->id())
                <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs mb-3 flex items-center gap-2">
                    <span>⚠️</span>
                    <span>Ini adalah akun yang sedang Anda gunakan saat ini. Peran akun harus tetap <strong>Administrator</strong>.</span>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="relative flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-all {{ old('role', $user->role->value) === 'admin' ? 'bg-amber-500/10 border-amber-500/40 text-white' : 'bg-gray-900/40 border-gray-800 text-gray-300 hover:border-gray-700' }}">
                    <input type="radio" name="role" value="admin" {{ old('role', $user->role->value) === 'admin' ? 'checked' : '' }} class="mt-1 text-amber-500 focus:ring-0">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-amber-400 font-bold text-sm">⚡ Administrator</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Admin</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1 leading-relaxed">
                            Akses penuh ke dashboard admin, verifikasi pesanan, produk, promo, dan manajemen akun.
                        </p>
                    </div>
                </label>

                <label class="relative flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-all {{ old('role', $user->role->value) === 'customer' ? 'bg-cyan-500/10 border-cyan-500/40 text-white' : 'bg-gray-900/40 border-gray-800 text-gray-300 hover:border-gray-700' }}">
                    <input type="radio" name="role" value="customer" {{ old('role', $user->role->value) === 'customer' ? 'checked' : '' }} {{ $user->id === auth()->id() ? 'disabled' : '' }} class="mt-1 text-cyan-500 focus:ring-0">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-cyan-400 font-bold text-sm">👤 Customer (Pelanggan)</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">User</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1 leading-relaxed">
                            Pengguna pelanggan yang hanya bisa bertransaksi dan melihat histori pesanan miliknya.
                        </p>
                    </div>
                </label>
            </div>
            @error('role')
                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Inputs with Toggle -->
        <div class="pt-4 border-t border-gray-800 space-y-4">
            <div>
                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>🔒</span> Ganti Password Akun (Opsional)
                </h4>
                <p class="text-xs text-gray-400 mt-0.5">Biarkan kedua kolom di bawah kosong jika tidak ingin mengubah kata sandi pengguna.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-300 mb-1.5">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 pr-11 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                        <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-cyan-400 focus:outline-none transition-colors" title="Lihat/Sembunyikan Password" aria-label="Lihat Password">
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
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-300 mb-1.5">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 pr-11 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-cyan-400 focus:outline-none transition-colors" title="Lihat/Sembunyikan Konfirmasi Password" aria-label="Lihat Konfirmasi Password">
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

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-800">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-3 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-semibold transition-all">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-sm shadow-xl shadow-cyan-600/30 transition-all flex items-center gap-2">
                <span>Simpan Perubahan</span> ➜
            </button>
        </div>
    </form>
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

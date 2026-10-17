@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    <div class="glass-card rounded-3xl p-8 border border-gray-800 glow-effect">
        
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-cyan-600/20 border border-cyan-500/30 text-cyan-400 flex items-center justify-center font-bold text-2xl mx-auto mb-3">
                ✨
            </div>
            <h1 class="text-2xl font-extrabold text-white">Daftar Akun Baru</h1>
            <p class="text-xs text-gray-400 mt-1">Buat akun untuk mempermudah pengecekan riwayat pesanan digital Anda</p>
        </div>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Nama Anda" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                @error('name')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-gray-300 mb-1.5">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="nama@email.com" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                @error('email')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-gray-300 mb-1.5">No. WhatsApp</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
                @error('phone')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-gray-300 mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 pr-11 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
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
                <label for="password_confirmation" class="block text-xs font-semibold text-gray-300 mb-1.5">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi password" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-2.5 pr-11 text-sm text-white focus:outline-none focus:border-cyan-500 transition-colors">
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

            <!-- Cloudflare Turnstile Captcha -->
            <div class="my-3 flex flex-col items-center justify-center">
                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.key') }}" data-theme="dark"></div>
                @error('cf-turnstile-response')
                    <p class="text-xs text-rose-400 mt-1.5 text-center">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full mt-2 py-3.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold text-sm shadow-lg shadow-cyan-600/30 transition-all flex items-center justify-center gap-2">
                <span>Daftar Sekarang</span> ➜
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-gray-800 text-center text-xs text-gray-400">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-400 hover:text-indigo-300 ml-1">
                Masuk di sini
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
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

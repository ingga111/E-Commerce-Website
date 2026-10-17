@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 pb-20">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-cyan-400 transition-colors">Beranda</a>
        <span>/</span>
        <span class="text-white">Lacak Invoice</span>
        <span>/</span>
        <span class="text-cyan-400 font-mono font-bold">{{ $order->order_number }}</span>
    </div>

    <!-- Status Alert Header & 4-Step Stepper (solustion411 Tracking) -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800 mb-8 text-center relative overflow-hidden">
        <!-- Ambient Glow -->
        <div class="absolute -right-10 -top-10 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        @if($order->status->value === 'completed')
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-500/20 text-emerald-400 mb-3 text-3xl border border-emerald-500/30 shadow-lg shadow-emerald-500/20">
                ✓
            </div>
            <h1 class="text-2xl font-black text-white">Pesanan Berhasil Diselesaikan!</h1>
            @if($order->isAccount() || $order->isGameAccount())
                <p class="text-xs text-gray-400 mt-1">Pembayaran telah dikonfirmasi! Detail akun Anda akan dikirimkan oleh Admin langsung ke WhatsApp Anda.</p>
            @else
                <p class="text-xs text-gray-400 mt-1">Item pesanan Anda telah sukses diproses dan diselesaikan.</p>
            @endif
        @elseif($order->status->value === 'processing')
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-cyan-500/20 text-cyan-400 mb-3 text-3xl border border-cyan-500/30 shadow-lg shadow-cyan-500/20">
                ⚡
            </div>
            <h1 class="text-2xl font-black text-white">Pembayaran Diterima & Sedang Diproses</h1>
            @if($order->isAccount() || $order->isGameAccount())
                <p class="text-xs text-gray-400 mt-1">Pembayaran telah kami terima! Admin sedang menyiapkan akun dan akan segera mengirimkannya ke WhatsApp Anda.</p>
            @else
                <p class="text-xs text-gray-400 mt-1">Sistem sedang memproses pesanan ke ID Akun game Anda. Mohon ditunggu 1-3 detik.</p>
            @endif
        @elseif($order->status->value === 'pending')
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-500/20 text-amber-400 mb-3 text-3xl border border-amber-500/30 shadow-lg shadow-amber-500/20">
                💳
            </div>
            <h1 class="text-2xl font-black text-white">Menunggu Pembayaran</h1>
            <p class="text-xs text-gray-400 mt-1">Selesaikan transfer manual sesuai nominal di bawah untuk proses verifikasi pesanan.</p>
        @else
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-rose-500/20 text-rose-400 mb-3 text-3xl border border-rose-500/30">
                ✕
            </div>
            <h1 class="text-2xl font-black text-white">Pesanan {{ $order->status->label() }}</h1>
        @endif

        <!-- 4-Step Visual Progress Stepper (solustion411 Signature) -->
        <div class="mt-8 pt-6 border-t border-gray-800">
            <div class="grid grid-cols-4 gap-2 text-center">
                <!-- Step 1 -->
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black mb-1.5 {{ in_array($order->status->value, ['pending', 'processing', 'completed']) ? 'bg-cyan-500 text-black shadow-md shadow-cyan-500/30' : 'bg-gray-800 text-gray-400' }}">
                        1
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold {{ in_array($order->status->value, ['pending', 'processing', 'completed']) ? 'text-white' : 'text-gray-500' }}">
                        Menunggu Bayar
                    </span>
                </div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black mb-1.5 {{ in_array($order->status->value, ['processing', 'completed']) || $order->payment?->payment_proof ? 'bg-cyan-500 text-black shadow-md shadow-cyan-500/30' : 'bg-gray-800 text-gray-400' }}">
                        2
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold {{ in_array($order->status->value, ['processing', 'completed']) || $order->payment?->payment_proof ? 'text-white' : 'text-gray-500' }}">
                        Bukti Diunggah
                    </span>
                </div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black mb-1.5 {{ in_array($order->status->value, ['processing', 'completed']) ? 'bg-cyan-500 text-black shadow-md shadow-cyan-500/30' : 'bg-gray-800 text-gray-400' }}">
                        3
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold {{ in_array($order->status->value, ['processing', 'completed']) ? 'text-white' : 'text-gray-500' }}">
                        Diproses
                    </span>
                </div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black mb-1.5 {{ $order->status->value === 'completed' ? 'bg-emerald-500 text-black shadow-md shadow-emerald-500/30' : 'bg-gray-800 text-gray-400' }}">
                        4
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold {{ $order->status->value === 'completed' ? 'text-emerald-400' : 'text-gray-500' }}">
                        Selesai
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- WHATSAPP DELIVERY NOTICE CARD (For Account / Game Account products) -->
    @if($order->isAccount() || $order->isGameAccount())
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-emerald-500/40 bg-emerald-950/20 mb-8 glow-cyan">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-2xl flex-shrink-0">
                        📱
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-emerald-300">Pengiriman Akun Dilakukan via WhatsApp</h3>
                        <p class="text-xs text-gray-400">Akun tidak diberikan di web demi keamanan dan privasi kredensial Anda.</p>
                    </div>
                </div>
                <span class="self-start sm:self-auto text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    Pengiriman Langsung via WA
                </span>
            </div>

            <div class="bg-gray-950 border border-gray-800 rounded-2xl p-5 space-y-3">
                <div class="flex items-start gap-3">
                    <span class="text-emerald-400 text-xl mt-0.5">💬</span>
                    <div class="space-y-1">
                        <p class="text-xs sm:text-sm text-gray-200 leading-relaxed font-medium">
                            Admin kami akan menghubungi nomor WhatsApp Anda: <strong class="text-emerald-400 font-mono text-sm">{{ $order->customer_phone }}</strong> dan memberikan detail akun lengkap (Email/Password/PIN Profil).
                        </p>
                        <p class="text-[11px] text-gray-400">
                            Pastikan nomor WhatsApp Anda aktif dan dapat menerima chat. Anda juga dapat langsung menghubungi Admin jika membutuhkan bantuan segera.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <span class="text-[11px] text-gray-400">
                    Ada pertanyaan atau butuh konfirmasi kilat?
                </span>
                <a href="https://wa.me/6281234567890?text={{ urlencode('Halo Admin solustion411, saya sudah membayar pesanan #' . $order->order_number . ' untuk ' . ($order->items->first()?->product_name ?? 'Produk Digital') . '. Mohon konfirmasi pengiriman akun saya. Terima kasih!') }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/30 transition-all">
                    <span>Chat Admin di WhatsApp ↗</span>
                </a>
            </div>
        </div>
    @endif

    <!-- PAYMENT INSTRUCTIONS & PROOF UPLOAD CARD (For Pending) -->
    @if(!$order->isPaid() && $order->payment)
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-amber-500/30 mb-8 glow-amber">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-800 pb-4 mb-6 gap-2">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl">💳</span>
                        <h3 class="text-base font-extrabold text-white">Instruksi Transfer Pembayaran</h3>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">Saluran: <strong class="text-cyan-400">{{ $order->payment->payment_name }}</strong></p>
                </div>
                <div class="sm:text-right">
                    <span class="text-[10px] text-gray-400 block uppercase tracking-wider font-semibold">Total Tagihan Transfer</span>
                    <span class="text-2xl sm:text-3xl font-black text-cyan-400">Rp {{ number_format($order->payment->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Manual Account Details Box -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-5 border border-gray-800 mb-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-[11px] text-gray-400 block mb-1">Nomor Rekening / No. HP Tujuan:</span>
                        <div class="flex items-center justify-between bg-gray-950 px-3.5 py-2.5 rounded-xl border border-gray-800">
                            <span id="account-number" class="text-lg sm:text-xl font-mono font-extrabold text-cyan-400 tracking-wider">
                                {{ $order->payment->account_number ?? $order->payment->pay_code ?? 'Rekening Toko' }}
                            </span>
                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('account-number').innerText.trim()); alert('Nomor Rekening berhasil disalin!');" class="ml-2 px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 text-xs font-bold text-gray-200 border border-gray-700 transition-all flex items-center gap-1">
                                📋 <span>Salin</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] text-gray-400 block mb-1">Atas Nama Pemilik:</span>
                        <div class="bg-gray-950 px-3.5 py-2.5 rounded-xl border border-gray-800">
                            <span class="text-sm font-bold text-white block">
                                {{ $order->payment->account_holder ?? 'solustion411 Official' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($order->payment->qr_url)
                    <div class="flex flex-col items-center justify-center p-4 bg-gray-950 rounded-xl border border-gray-800">
                        <span class="text-xs text-gray-300 font-semibold mb-3">Scan QRIS Toko (Semua Bank & E-Wallet):</span>
                        <div class="p-2.5 bg-white rounded-xl shadow-lg">
                            <img src="{{ $order->payment->qr_url }}" alt="QRIS Toko" class="w-48 h-48 object-contain">
                        </div>
                    </div>
                @endif

                <p class="text-[11px] text-gray-400 leading-relaxed bg-amber-500/10 p-3 rounded-xl border border-amber-500/20 text-amber-300/90">
                    💡 <strong>Penting:</strong> Harap transfer tepat sejumlah <strong>Rp {{ number_format($order->payment->total_amount, 0, ',', '.') }}</strong> agar pesanan otomatis terdeteksi. Lampirkan foto bukti transfer di bawah ini.
                </p>
            </div>

            <!-- PAYMENT PROOF SECTION -->
            <div class="border-t border-gray-800 pt-6">
                @if($order->payment->payment_proof)
                    <div class="p-5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-400 text-lg">✓</span>
                                <h4 class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Bukti Transfer Berhasil Diunggah</h4>
                            </div>
                            <span class="text-[10px] text-gray-400">
                                {{ $order->payment->payment_proof_uploaded_at?->diffForHumans() ?? 'Baru saja' }}
                            </span>
                        </div>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ asset('storage/' . $order->payment->payment_proof) }}" target="_blank" class="block w-20 h-20 rounded-xl overflow-hidden border border-gray-700 flex-shrink-0 bg-gray-900 group relative">
                                    <img src="{{ asset('storage/' . $order->payment->payment_proof) }}" alt="Bukti Transfer" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                    <span class="absolute inset-0 bg-black/40 flex items-center justify-center text-[10px] text-white opacity-0 group-hover:opacity-100 transition-opacity">Lihat</span>
                                </a>
                                <div>
                                    <p class="text-xs text-gray-300 font-semibold">Bukti Pembayaran Terkirim</p>
                                    <a href="{{ asset('storage/' . $order->payment->payment_proof) }}" target="_blank" class="text-[11px] text-cyan-400 hover:text-cyan-300 font-medium mt-1 inline-block">
                                        🔍 Buka Gambar Ukuran Penuh
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-amber-300 bg-amber-500/10 p-3 rounded-xl border border-amber-500/20">
                            <span class="inline-block w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                            <span>Sedang diverifikasi admin. Layar ini otomatis diperbarui jika pembayaran telah disetujui.</span>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-900/60 rounded-2xl p-5 border border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-base">📤</span>
                            <h4 class="text-xs font-bold text-white uppercase tracking-wider">Unggah Bukti Pembayaran</h4>
                        </div>
                        <p class="text-[11px] text-gray-400 mb-4 leading-relaxed">
                            Kirimkan screenshot m-Banking, resi ATM, atau notifikasi transfer e-wallet untuk verifikasi instan.
                        </p>

                        <form action="{{ route('orders.upload-proof', $order->order_number) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-300 mb-1.5">File Foto Bukti Transfer <span class="text-rose-400">*</span></label>
                                <input type="file" name="payment_proof" accept="image/*,.pdf" required class="w-full text-xs text-gray-300 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-cyan-600 file:text-white hover:file:bg-cyan-500 cursor-pointer bg-gray-950 p-2 rounded-xl border border-gray-700">
                                @error('payment_proof')
                                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Catatan / Nama Pengirim (Opsional)</label>
                                <input type="text" name="notes" placeholder="Contoh: Transfer atas nama Budi" class="w-full bg-gray-950 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                            </div>

                            <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-xs shadow-lg shadow-cyan-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <span>Kirim Bukti Pembayaran Sekarang</span> ➜
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Invoice Details Card -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-gray-800">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-800 pb-5 mb-6">
            <div>
                <span class="text-[11px] text-gray-500 block uppercase font-medium">Nomor Invoice</span>
                <span class="font-mono font-black text-base sm:text-lg text-white">{{ $order->order_number }}</span>
            </div>
            <div>
                <span class="text-[11px] text-gray-500 block uppercase font-medium">Waktu Transaksi</span>
                <span class="text-xs font-semibold text-gray-300">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
            </div>
            <div>
                <span class="text-[11px] text-gray-500 block uppercase font-medium">Status</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                    @if($order->status->value === 'completed') bg-emerald-500/20 text-emerald-400 border border-emerald-500/30
                    @elseif($order->status->value === 'processing') bg-cyan-500/20 text-cyan-400 border border-cyan-500/30
                    @elseif($order->status->value === 'pending') bg-amber-500/20 text-amber-400 border border-amber-500/30
                    @else bg-rose-500/20 text-rose-400 border border-rose-500/30 @endif">
                    {{ $order->status->label() }}
                </span>
            </div>
        </div>

        <!-- Target Destination Information -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-gray-900/60 p-4 rounded-2xl border border-gray-800 mb-6">
            @if($order->isGame())
                <div>
                    <span class="text-gray-500 block">Tujuan Akun Game:</span>
                    <span class="font-mono font-bold text-cyan-400">{{ $order->target_user_id }} {{ $order->target_zone_id ? "({$order->target_zone_id})" : '' }}</span>
                </div>
            @elseif($order->isJoki())
                <div>
                    <span class="text-gray-500 block">Akun Joki:</span>
                    <span class="font-mono font-bold text-emerald-400">{{ $order->target_user_id }} (Via: {{ $order->target_zone_id }})</span>
                </div>
            @endif
            <div>
                <span class="text-gray-500 block">Nama Pemesan:</span>
                <span class="font-semibold text-white">{{ $order->customer_name }}</span>
            </div>
            <div>
                <span class="text-gray-500 block">No. WhatsApp:</span>
                <span class="font-semibold text-white">{{ $order->customer_phone }}</span>
            </div>
            <div>
                <span class="text-gray-500 block">Email Pengiriman:</span>
                <span class="font-semibold text-white">{{ $order->customer_email }}</span>
            </div>
        </div>

        <!-- Order Items Breakdown -->
        <div class="space-y-3 mb-6">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Rincian Pembelian</span>
            @foreach($order->items as $item)
                <div class="flex items-center justify-between text-xs p-3.5 bg-gray-900/40 rounded-2xl border border-gray-800">
                    <div>
                        <span class="font-bold text-white block text-sm">{{ $item->product_name }}</span>
                        <span class="text-cyan-400 text-xs font-semibold">{{ $item->variant_name }}</span>
                    </div>
                    <span class="font-black text-sm text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <!-- Price Totals -->
        <div class="space-y-2 text-xs border-t border-gray-800 pt-4 text-gray-400">
            <div class="flex justify-between">
                <span>Subtotal Item</span>
                <span class="text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="flex justify-between text-emerald-400 font-bold">
                    <span class="flex items-center gap-1.5">
                        <span>🎟️ Diskon Voucher</span>
                        @if($order->voucher_code)
                            <span class="text-[10px] bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20 font-mono">{{ $order->voucher_code }}</span>
                        @endif
                    </span>
                    <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between">
                <span>Biaya Layanan Admin</span>
                <span class="text-white">Rp {{ number_format($order->admin_fee, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between pt-3 border-t border-gray-800 font-bold text-base">
                <span class="text-white">Total Pembayaran</span>
                <span class="text-cyan-400 font-black text-xl">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- CS Help Button & Back -->
        <div class="mt-8 pt-4 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="text-xs font-semibold text-gray-400 hover:text-white transition-colors">
                ← Kembali ke Beranda
            </a>
            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20solustion411,%20saya%20ingin%20konfirmasi%20invoice%20{{ $order->order_number }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 text-xs font-bold transition-all flex items-center gap-1.5">
                <span>💬 Hubungi Admin via WhatsApp</span>
            </a>
        </div>
    </div>

</div>

@push('scripts')
@if(in_array($order->status->value, ['pending', 'processing']))
<script>
    const orderNumber = "{{ $order->order_number }}";
    const checkStatusUrl = "{{ route('orders.check-status', $order->order_number) }}";

    // Auto poll status
    const interval = setInterval(async () => {
        try {
            const res = await fetch(checkStatusUrl);
            const data = await res.json();
            if (data.is_paid || data.status === 'completed') {
                clearInterval(interval);
                window.location.reload();
            }
        } catch (e) {
            console.error('Status check error:', e);
        }
    }, 5000);
</script>
@endif
@endpush
@endsection

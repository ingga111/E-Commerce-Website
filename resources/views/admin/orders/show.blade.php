@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.orders.index') }}" class="hover:text-indigo-400">Pesanan</a>
        <span>/</span>
        <span class="text-white">#{{ $order->order_number }}</span>
    </div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Detail Pesanan #{{ $order->order_number }}</h1>
                @if($order->status->value === 'processing' && $order->payment?->payment_proof && $order->payment?->status?->value !== 'paid')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 animate-pulse">
                        Perlu Verifikasi Bukti
                    </span>
                @elseif($order->status->value === 'completed')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        ✓ Lunas & Selesai
                    </span>
                @elseif($order->status->value === 'pending')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                        Menunggu Pembayaran
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                        {{ ucfirst($order->status->value) }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-1">Dibuat pada {{ $order->created_at->format('d F Y, H:i:s') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-semibold border border-gray-700 transition-all">
                ← Kembali ke Daftar
            </a>
            <a href="{{ route('orders.show', $order->order_number) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-indigo-400 hover:text-indigo-300 text-xs font-semibold border border-gray-700 transition-all flex items-center gap-1.5">
                <span>Lihat Tampilan Customer ↗</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Payment Proof & Actions -->
        <div class="lg:col-span-6 space-y-6">
            
            <!-- Payment Proof Card -->
            <div class="glass-card rounded-3xl p-6 border border-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span>📷</span> Bukti Transfer Customer
                    </h3>
                    @if($order->payment?->payment_proof_uploaded_at)
                        <span class="text-[10px] text-gray-400">
                            Diunggah: {{ $order->payment->payment_proof_uploaded_at->format('d M, H:i') }}
                        </span>
                    @endif
                </div>

                @if($order->payment?->payment_proof)
                    <div class="rounded-2xl overflow-hidden border border-gray-800 bg-gray-950 flex flex-col items-center">
                        @php
                            $extension = pathinfo($order->payment->payment_proof, PATHINFO_EXTENSION);
                            $isPdf = strtolower($extension) === 'pdf';
                        @endphp

                        @if($isPdf)
                            <div class="p-8 text-center">
                                <div class="text-5xl mb-3">📄</div>
                                <span class="text-sm font-bold text-white block">Dokumen Bukti PDF</span>
                                <a href="{{ asset('storage/'.$order->payment->payment_proof) }}" target="_blank" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg transition-all">
                                    Buka File PDF ↗
                                </a>
                            </div>
                        @else
                            <a href="{{ asset('storage/'.$order->payment->payment_proof) }}" target="_blank" class="block group relative w-full text-center">
                                <img src="{{ asset('storage/'.$order->payment->payment_proof) }}" alt="Bukti Transfer" class="w-full max-h-96 object-contain rounded-t-xl group-hover:opacity-90 transition-opacity">
                                <div class="py-2.5 bg-gray-900/90 text-center text-xs font-semibold text-cyan-400 group-hover:text-cyan-300">
                                    🔍 Klik untuk Membuka Ukuran Asli Penuh ↗
                                </div>
                            </a>
                        @endif
                    </div>
                @else
                    <div class="p-8 text-center rounded-2xl border border-dashed border-gray-800 bg-gray-900/30">
                        <div class="text-4xl text-gray-600 mb-2">⏳</div>
                        <h4 class="text-sm font-bold text-gray-300">Belum Ada Bukti Pembayaran</h4>
                        <p class="text-xs text-gray-500 mt-1">Customer belum mengunggah foto atau file bukti transfer.</p>
                    </div>
                @endif
            </div>

            <!-- Verification Action Box -->
            @if(in_array($order->status->value, ['pending', 'processing']) && $order->status->value !== 'completed' && $order->payment?->status?->value !== 'paid')
                <div class="glass-card rounded-3xl p-6 border border-cyan-500/30 glow-effect space-y-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Aksi Verifikasi Admin</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Pastikan mutasi dana telah masuk ke rekening sebelum mengonfirmasi. Saat Anda mengonfirmasi, status pesanan otomatis berubah jadi <strong>Selesai</strong> dan stok akan dipotong. Detail akun dikirimkan oleh Admin langsung via WhatsApp ke nomor customer.
                    </p>

                    <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <form action="{{ route('admin.orders.confirm', $order->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin dana sebesar Rp {{ number_format($order->total_amount, 0, ',', '.') }} sudah masuk ke rekening? Pesanan akan ditandai selesai.');">
                            @csrf
                            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-sm shadow-xl shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                                <span>✓ Konfirmasi Pembayaran Valid</span>
                            </button>
                        </form>
                    </div>

                    <!-- Reject Section -->
                    <div class="pt-4 border-t border-gray-800">
                        <form action="{{ route('admin.orders.reject', $order->id) }}" method="POST" onsubmit="return confirm('Tolak bukti pembayaran ini? Status pesanan akan menjadi Ditolak.');">
                            @csrf
                            <label class="block text-xs font-semibold text-gray-400 mb-1.5">Alasan Penolakan (Akan tercatat di sistem)</label>
                            <div class="flex gap-2">
                                <input type="text" name="reason" placeholder="Contoh: Mutasi belum masuk / Bukti transfer palsu" class="flex-1 bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-rose-500">
                                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-950/60 hover:bg-rose-900 border border-rose-500/40 text-rose-400 font-bold text-xs transition-all">
                                    Tolak ✗
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="glass-card rounded-3xl p-6 border border-gray-800">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Status Final</span>
                    <p class="text-sm font-semibold text-white">
                        Pesanan ini berstatus <span class="text-emerald-400 uppercase font-bold">{{ $order->status->value }}</span> (Status Pembayaran: <span class="text-cyan-400 uppercase font-bold">{{ $order->payment?->status?->value ?? '-' }}</span>).
                    </p>
                    @if($order->payment?->admin_note)
                        <div class="mt-3 p-3 bg-gray-900/80 rounded-xl border border-gray-800 text-xs text-rose-300">
                            <strong>Catatan:</strong> {{ $order->payment->admin_note }}
                        </div>
                    @endif
                </div>
            @endif

            @php
                // Clean phone number for WhatsApp URL (convert 08xxx to 628xxx)
                $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62' . substr($cleanPhone, 1);
                } elseif (!str_starts_with($cleanPhone, '62') && !empty($cleanPhone)) {
                    $cleanPhone = '62' . $cleanPhone;
                }

                $firstItem = $order->items->first();
                $productLabel = $firstItem ? ($firstItem->product_name . ' - ' . $firstItem->variant_name) : 'Pesanan Akun';

                $waMessage = "Halo kak {$order->customer_name}! 👋\n"
                    . "Terima kasih telah order di solustion411.\n\n"
                    . "Pesanan Anda telah kami konfirmasi:\n"
                    . "• Invoice: #{$order->order_number}\n"
                    . "• Produk: {$productLabel}\n"
                    . "• Qty: " . ($firstItem?->quantity ?? 1) . "\n\n"
                    . "Berikut detail akun Anda:\n"
                    . "📧 Email / Username: \n"
                    . "🔑 Password: \n"
                    . "📌 PIN Profil / Catatan: \n\n"
                    . "Silakan langsung dicoba login ya kak. Jika ada kendala segera kabari kami. Terima kasih banyak! 🙏";

                $waLink = "https://wa.me/{$cleanPhone}?text=" . urlencode($waMessage);
            @endphp

            <!-- WHATSAPP DELIVERY HELPER FOR ADMIN -->
            <div class="glass-card rounded-3xl p-6 border border-emerald-500/30 bg-emerald-950/20 glow-effect space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">📱</span>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Kirim Akun via WhatsApp</h3>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        Admin WA
                    </span>
                </div>

                <p class="text-xs text-gray-300 leading-relaxed">
                    Pengiriman akun dilakukan oleh Admin via WhatsApp ke nomor customer:
                    <strong class="text-emerald-400 font-mono">{{ $order->customer_phone }}</strong>.
                </p>

                <!-- Draft Message Template -->
                <div class="relative bg-gray-950 border border-gray-800 rounded-2xl p-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Draft Pesan WhatsApp:</span>
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('wa-admin-template').innerText); alert('Draft pesan WA berhasil disalin ke clipboard!');" class="text-[10px] text-emerald-400 hover:text-emerald-300 font-bold flex items-center gap-1">
                            📋 <span>Salin Teks</span>
                        </button>
                    </div>
                    <pre id="wa-admin-template" class="text-xs font-mono text-emerald-300/90 whitespace-pre-wrap leading-relaxed select-all">{{ $waMessage }}</pre>
                </div>

                <div class="flex flex-col sm:flex-row gap-2.5 pt-1">
                    <a href="{{ $waLink }}" target="_blank" class="flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-xs shadow-xl shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                        <span>💬 Buka WhatsApp & Kirim Akun ↗</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- Right Column: Order Details & Customer Info -->
        <div class="lg:col-span-6 space-y-6">
            
            <!-- Customer Information Card -->
            <div class="glass-card rounded-3xl p-6 border border-gray-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span>👤</span> Data Pembeli & Tujuan
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1.5 border-b border-gray-800/80">
                        <span class="text-gray-400">Nama Lengkap:</span>
                        <span class="font-bold text-white">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-gray-800/80">
                        <span class="text-gray-400">Email:</span>
                        <span class="font-semibold text-white">{{ $order->customer_email }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-gray-800/80">
                        <span class="text-gray-400">WhatsApp:</span>
                        <a href="{{ $waLink }}" target="_blank" class="font-mono font-semibold text-emerald-400 hover:text-emerald-300 flex items-center gap-1">
                            {{ $order->customer_phone }} <span>↗</span>
                        </a>
                    </div>
                    @if($order->isJoki())
                        <div class="flex justify-between py-1.5 border-b border-gray-800/80">
                            <span class="text-gray-400">Login Via:</span>
                            <span class="font-bold text-amber-400">{{ $order->target_zone_id }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-800/80">
                            <span class="text-gray-400">Email/ID Akun:</span>
                            <span class="font-mono font-bold text-cyan-400">{{ $order->target_user_id }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-800/80">
                            <span class="text-gray-400">Password Akun:</span>
                            <span class="font-mono font-bold text-emerald-400 bg-gray-900 px-2 py-0.5 rounded border border-gray-700 select-all">{{ $order->target_nickname }}</span>
                        </div>
                        @if($order->notes)
                            <div class="py-1.5 border-b border-gray-800/80">
                                <span class="text-gray-400 block mb-0.5">Catatan / Request Hero:</span>
                                <span class="font-medium text-white italic bg-gray-900/60 p-2 rounded-lg block border border-gray-800 text-[11px]">{{ $order->notes }}</span>
                            </div>
                        @endif
                    @else
                        @if($order->target_user_id)
                            <div class="flex justify-between py-1.5 border-b border-gray-800/80">
                                <span class="text-gray-400">Target User ID:</span>
                                <span class="font-mono font-bold text-cyan-400">{{ $order->target_user_id }}</span>
                            </div>
                        @endif
                        @if($order->target_zone_id)
                            <div class="flex justify-between py-1.5 border-b border-gray-800/80">
                                <span class="text-gray-400">Target Zone ID:</span>
                                <span class="font-mono font-bold text-cyan-400">{{ $order->target_zone_id }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Items Purchased Card -->
            <div class="glass-card rounded-3xl p-6 border border-gray-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span>📦</span> Item yang Dipesan
                </h3>

                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="p-4 rounded-2xl bg-gray-900/60 border border-gray-800 space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-white text-sm block">{{ $item->product_name }}</span>
                                    <span class="text-xs text-gray-400">Varian: {{ $item->variant_name }}</span>
                                </div>
                                <span class="font-bold text-cyan-400 text-sm">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Delivered Account Stock Credentials if fulfilled -->
                            @if(!empty($item->credentials_delivered))
                                <div class="mt-3 pt-3 border-t border-gray-800">
                                    <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider block mb-1.5">
                                        ✓ Kredensial Akun / Lisensi Terkirim:
                                    </span>
                                    <div class="p-3 bg-gray-950 rounded-xl border border-emerald-500/20 font-mono text-xs text-emerald-300 break-all select-all whitespace-pre-wrap">{{ $item->credentials_delivered }}</div>
                                </div>
                            @elseif($order->isAccount() || $order->isGameAccount())
                                <div class="mt-3 pt-3 border-t border-gray-800 flex items-center justify-between">
                                    <span class="text-[11px] text-emerald-400 font-semibold flex items-center gap-1.5">
                                        <span>💬</span> Pengiriman Akun: Berikan langsung via WhatsApp pembeli
                                    </span>
                                    <a href="{{ $waLink }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-600/30 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/40 text-[10px] font-bold transition-all">
                                        Chat WA ↗
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Transfer & Payment Summary -->
            <div class="glass-card rounded-3xl p-6 border border-gray-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span>💳</span> Rekening Transfer & Tagihan
                </h3>

                <div class="space-y-2.5 text-xs text-gray-300">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Metode:</span>
                        <span class="font-bold text-white">{{ $order->payment_method }}</span>
                    </div>
                    @if($order->payment?->account_number)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Rekening Tujuan:</span>
                            <span class="font-mono font-bold text-cyan-300">{{ $order->payment->account_number }}</span>
                        </div>
                    @endif
                    @if($order->payment?->account_holder)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Atas Nama:</span>
                            <span class="font-semibold text-white">{{ $order->payment->account_holder }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between pt-2 border-t border-gray-800">
                        <span class="text-gray-400">Subtotal Item:</span>
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
                        <span class="text-gray-400">Biaya Admin:</span>
                        <span class="text-white">Rp {{ number_format($order->admin_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-800 font-extrabold text-sm">
                        <span class="text-white">Total Tagihan:</span>
                        <span class="text-cyan-400 text-base">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

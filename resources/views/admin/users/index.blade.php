@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400">Dashboard</a>
                <span>/</span>
                <span class="text-white">Manajemen User</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Kelola User & Administrator</h1>
            <p class="text-xs text-gray-400 mt-1">Daftar semua pengguna terdaftar, buat akun admin baru, dan atur hak akses.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold border border-gray-700 transition-all flex items-center gap-1.5">
                <span>⚡ Dashboard</span>
            </a>
            <a href="{{ route('admin.users.create') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold shadow-lg shadow-cyan-600/20 transition-all flex items-center gap-1.5">
                <span>+ Tambah User / Admin Baru</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="glass-card rounded-2xl p-5 border border-gray-800 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center text-2xl flex-shrink-0">
                👥
            </div>
            <div>
                <span class="text-xs text-gray-400 uppercase tracking-wider block font-semibold">Total Pengguna</span>
                <span class="text-2xl font-black text-white">{{ number_format($totalUsers) }}</span>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-amber-500/20 bg-amber-500/5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/30 text-amber-400 flex items-center justify-center text-2xl flex-shrink-0">
                ⚡
            </div>
            <div>
                <span class="text-xs text-amber-400 uppercase tracking-wider block font-semibold">Administrator</span>
                <span class="text-2xl font-black text-amber-300">{{ number_format($totalAdmins) }}</span>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-cyan-500/20 bg-cyan-500/5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-cyan-500/20 border border-cyan-500/30 text-cyan-400 flex items-center justify-center text-2xl flex-shrink-0">
                👤
            </div>
            <div>
                <span class="text-xs text-cyan-400 uppercase tracking-wider block font-semibold">Pelanggan (Customer)</span>
                <span class="text-2xl font-black text-cyan-300">{{ number_format($totalCustomers) }}</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="glass-card rounded-2xl p-4 border border-gray-800 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau no. WhatsApp..." class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-10 pr-4 py-2.5 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 transition-colors">
                <span class="absolute left-3.5 top-2.5 text-gray-500 text-sm">🔍</span>
            </div>

            <div class="w-full md:w-56">
                <select name="role" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500 transition-colors">
                    <option value="">Semua Peran (Role)</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>⚡ Administrator</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>👤 Customer (Pelanggan)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-md shadow-indigo-600/20">
                    Filter
                </button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="px-3.5 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-semibold border border-gray-700 transition-all" title="Reset Filter">
                        ✕ Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="glass-card rounded-3xl p-6 border border-gray-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Daftar Pengguna</h3>
            <span class="text-xs text-gray-400">Menampilkan {{ $users->total() }} total akun</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-300">
                <thead class="bg-gray-900/60 text-gray-400 uppercase tracking-wider text-[10px] border-b border-gray-800">
                    <tr>
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4">WhatsApp</th>
                        <th class="py-3.5 px-4">Peran (Role)</th>
                        <th class="py-3.5 px-4">Total Pesanan</th>
                        <th class="py-3.5 px-4">Tgl Bergabung</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/80">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-800/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs text-white flex-shrink-0 {{ $user->isAdmin() ? 'bg-amber-500/20 border border-amber-500/30 text-amber-300' : 'bg-cyan-500/20 border border-cyan-500/30 text-cyan-300' }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-white text-xs">{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Anda</span>
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-gray-500">ID: #{{ $user->id }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 font-mono text-gray-300">
                                {{ $user->email }}
                            </td>

                            <td class="py-3.5 px-4 font-mono text-gray-400">
                                {{ $user->phone ?? '-' }}
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide {{ $user->isAdmin() ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30' }}">
                                    {{ $user->isAdmin() ? '⚡ Admin' : '👤 Customer' }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-lg bg-gray-900 border border-gray-700 text-xs font-semibold {{ $user->orders_count > 0 ? 'text-indigo-400' : 'text-gray-500' }}">
                                    {{ $user->orders_count }} Transaksi
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-gray-400">
                                {{ $user->created_at->format('d M Y, H:i') }}
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="p-2 rounded-lg bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-300 border border-indigo-500/30 transition-all" title="Edit Pengguna">
                                        ✏️ Edit
                                    </a>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-rose-600/20 hover:bg-rose-600/40 text-rose-300 border border-rose-500/30 transition-all" title="Hapus Pengguna">
                                                🗑️
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <span class="text-2xl block mb-1">🔍</span>
                                Tidak ada data pengguna yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="mt-6 pt-4 border-t border-gray-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

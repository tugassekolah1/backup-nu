<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zm0 8c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm0-8c.343 0 .667.044.98.128M12 16v2m-6 0h12a2 2 0 002-2v-3a6 6 0 10-12 0v3a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Manajemen Infaq
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Catat dan pantau kas masuk & kas keluar infaq.</p>
                </div>
            </div>

            <a href="{{ route('admin.infaq.create') }}"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-xs hover:shadow-emerald-200 hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Catat Infaq</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @php
                $filtering = trim((string) $q) !== '' || !empty($status) || !empty($arah);
            @endphp

            {{-- Alert --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.34 16a2 2 0 001.73 3z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Kartu Rekap --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Saldo Kas</p>
                    <p class="text-2xl text-blue-600 font-black text-slate-900 mt-2">Rp {{ number_format($rekap['saldo'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Masuk (Lunas)</p>
                    <p class="text-2xl font-black text-emerald-700 mt-2">Rp {{ number_format($rekap['total_lunas'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Keluar (Lunas)</p>
                    <p class="text-2xl font-black text-rose-600 mt-2">Rp {{ number_format($rekap['total_keluar'], 0, ',', '.') }}</p>
                </div>
                
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Masuk Hari Ini</p>
                    <p class="text-2xl font-black text-slate-900 mt-2">Rp {{ number_format($rekap['hari_ini'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Masuk Bulan Ini</p>
                    <p class="text-2xl font-black text-slate-900 mt-2">Rp {{ number_format($rekap['bulan_ini'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Menunggu Bayar</p>
                    <p class="text-2xl font-black text-amber-600 mt-2">{{ $rekap['pending'] }} transaksi</p>
                </div>
            </div>

            {{-- Pencarian & Filter --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
                <form method="GET" action="{{ route('admin.infaq.index') }}"
                      class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                        </svg>
                        <input type="search" name="q" value="{{ $q }}"
                               placeholder="Cari kode, nama donatur, atau no HP..."
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 placeholder:text-slate-400 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none transition-all" />
                    </div>

                    <select name="arah"
                            class="sm:w-44 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none transition-all">
                        <option value="">Masuk + Keluar</option>
                        @foreach (\App\Models\Infaq::ARAH as $item)
                            <option value="{{ $item }}" {{ ($arah ?? '') === $item ? 'selected' : '' }}>
                                {{ $item === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status"
                            class="sm:w-48 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none transition-all">
                        <option value="">Semua Status</option>
                        @foreach (\App\Models\Infaq::STATUSES as $item)
                            <option value="{{ $item }}" {{ ($status ?? '') === $item ? 'selected' : '' }}>
                                {{ ucfirst($item) }}
                            </option>
                        @endforeach
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                            </svg>
                            <span>Cari</span>
                        </button>

                        @if ($filtering)
                            <a href="{{ route('admin.infaq.index') }}"
                               class="inline-flex items-center bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabel Transaksi --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800">
                        {{ $filtering ? 'Hasil Transaksi' : 'Seluruh Transaksi' }}
                    </h3>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">
                        {{ $filtering ? 'Ditemukan' : 'Total' }}: {{ $infaqs->total() }} Data
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-xs uppercase font-bold text-slate-500 tracking-wider">
                                <th class="px-6 py-4">Kode</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Arah</th>
                                <th class="px-6 py-4">Donatur / Keperluan</th>
                                <th class="px-6 py-4">Metode</th>
                                <th class="px-6 py-4 text-right">Nominal</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($infaqs as $infaq)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 font-mono text-xs text-slate-600">{{ $infaq->kode_transaksi }}</td>

                                    <td class="px-6 py-4 text-slate-600">
                                        {{ ($infaq->paid_at ?? $infaq->created_at)->format('d M Y') }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if (($infaq->arah ?? 'masuk') === 'keluar')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100/80 text-rose-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Keluar
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100/80 text-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Masuk
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900">{{ $infaq->nama_donatur }}</div>
                                        @if (($infaq->arah ?? 'masuk') === 'keluar')
                                            @if ($infaq->kategori)
                                                <div class="text-xs text-slate-500 mt-0.5">{{ ucwords(str_replace('_', ' ', $infaq->kategori)) }}</div>
                                            @endif
                                            @if ($infaq->catatan)
                                                <div class="text-xs text-slate-400 mt-0.5 line-clamp-2">{{ $infaq->catatan }}</div>
                                            @endif
                                            @if ($infaq->penanggung_jawab)
                                                <div class="text-xs text-slate-400 mt-0.5">PJ: {{ $infaq->penanggung_jawab }}</div>
                                            @endif
                                            @if ($infaq->bukti_path)
                                                <a href="{{ asset('storage/' . $infaq->bukti_path) }}" target="_blank" rel="noopener" class="text-xs font-bold text-blue-600 hover:underline">Lihat bukti</a>
                                            @endif
                                        @else
                                            @if ($infaq->no_hp)
                                                <div class="text-xs text-slate-400 mt-0.5">{{ $infaq->no_hp }}</div>
                                            @endif
                                            @if ($infaq->bukti_path)
                                                <a href="{{ asset('storage/' . $infaq->bukti_path) }}" target="_blank" rel="noopener" class="mt-1 inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:underline">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Lihat bukti bayar
                                                </a>
                                            @endif
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-slate-600">
                                        {{ str_replace('_', ' ', ucwords($infaq->metode_pembayaran, '_')) }}
                                    </td>

                                    <td class="px-6 py-4 text-right font-black text-slate-900">
                                        Rp {{ number_format($infaq->nominal, 0, ',', '.') }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($infaq->status === 'lunas')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100/80 text-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Lunas
                                            </span>
                                        @elseif ($infaq->status === 'pending')
                                            @if ($infaq->bukti_path)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 ring-1 ring-amber-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    Menunggu Verifikasi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100/80 text-amber-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Pending
                                                </span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100/80 text-rose-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Dibatalkan
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('admin.infaq.edit', $infaq) }}"
                                               title="Edit"
                                               class="p-1.5 rounded-lg text-slate-600 hover:bg-white hover:text-blue-600 border border-transparent hover:border-slate-200 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            @if ($infaq->status === 'pending')
                                                <form action="{{ route('admin.infaq.mark-lunas', $infaq) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Setujui {{ $infaq->bukti_path ? 'bukti bayar dan ' : '' }}tandai {{ $infaq->kode_transaksi }} lunas?')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs transition-all">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $infaq->bukti_path ? 'Setujui & Lunas' : 'Tandai Lunas' }}
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.infaq.mark-batal', $infaq) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Batalkan transaksi {{ $infaq->kode_transaksi }}? Status berubah menjadi dibatalkan.')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border border-rose-200 text-rose-600 hover:bg-rose-50 transition-all">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                        Batalkan
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.infaq.destroy', $infaq) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Hapus transaksi {{ $infaq->kode_transaksi }} ini? Data tidak bisa dikembalikan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus"
                                                        class="p-1.5 rounded-lg text-slate-600 hover:bg-white hover:text-rose-600 border border-transparent hover:border-slate-200 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <div class="p-4 bg-slate-100 text-slate-400 rounded-full">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            </div>
                                            <p class="text-slate-500 font-medium">
                                                @if ($filtering)
                                                    Tidak ada transaksi yang cocok dengan pencarian Anda.
                                                @else
                                                    Belum ada transaksi infaq.
                                                @endif
                                            </p>
                                            @if ($filtering)
                                                <a href="{{ route('admin.infaq.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
                                                    Reset pencarian
                                                </a>
                                            @else
                                                <a href="{{ route('admin.infaq.create') }}" class="text-xs font-bold text-blue-600 hover:underline">
                                                    + Catat transaksi pertama
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($infaqs->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $infaqs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

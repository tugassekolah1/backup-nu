<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start sm:items-center gap-3 min-w-0">
                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Manajemen Berita
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola semua artikel dan berita terbaru di sini.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('berita.public') }}" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-600 px-3.5 sm:px-4 py-2.5 rounded-xl text-sm font-semibold transition-all border border-slate-200/80 shadow-xs">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                    </svg>
                    <span class="hidden sm:inline">Beranda Berita</span>
                    <span class="sm:hidden">Beranda</span>
                </a>

                <a href="{{ route('berita.statistik') }}"
                   class="inline-flex items-center justify-center gap-2 bg-white hover:bg-teal-50 text-teal-700 px-3.5 sm:px-4 py-2.5 rounded-xl text-sm font-semibold transition-all border border-slate-200/80 shadow-xs">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                    <span class="hidden sm:inline">Statistik Grafik</span>
                    <span class="sm:hidden">Statistik</span>
                </a>

                <a href="{{ route('berita.create') }}"
                   class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-3.5 sm:px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-xs hover:shadow-emerald-200 hover:shadow-md w-full sm:w-auto">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Berita</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @php
                $filtering = trim((string) $q) !== '' || !empty($jenis);
            @endphp

            {{-- Alert Sukses --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Statistik Ringkas --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Total Berita --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex items-center gap-3.5">
                    <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-black text-slate-900 leading-none">{{ number_format($stats['total']) }}</p>
                        <p class="text-xs font-semibold text-slate-500 mt-1.5">Total Berita</p>
                    </div>
                </div>

                {{-- Terbit --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex items-center gap-3.5">
                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-black text-slate-900 leading-none">{{ number_format($stats['publish']) }}</p>
                        <p class="text-xs font-semibold text-slate-500 mt-1.5">Terbit · <span class="text-slate-400">{{ $stats['draft'] }} draft</span></p>
                    </div>
                </div>

                {{-- Total Pembaca --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex items-center gap-3.5">
                    <div class="p-2.5 bg-amber-50 text-amber-600 rounded-xl shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-black text-slate-900 leading-none">{{ number_format($stats['views']) }}</p>
                        <p class="text-xs font-semibold text-slate-500 mt-1.5">Total Dibaca</p>
                    </div>
                </div>

                {{-- Rata-rata Pembaca --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex items-center gap-3.5">
                    <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-black text-slate-900 leading-none">{{ number_format($stats['rataViews']) }}</p>
                        <p class="text-xs font-semibold text-slate-500 mt-1.5">Rata-rata / Berita</p>
                    </div>
                </div>
            </div>

            {{-- Pencarian, Filter & Urutan --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
                <form method="GET" action="{{ route('berita.index') }}"
                      class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <div class="relative flex-1 min-w-0">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                        </svg>
                        <input type="search" name="q" value="{{ $q }}"
                               placeholder="Cari judul, slug, atau isi berita..."
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 placeholder:text-slate-400 focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex gap-3 lg:gap-2">
                        <select name="jenis"
                                class="w-full lg:w-48 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                            <option value="">Semua Jenis</option>
                            @foreach (\App\Models\Berita::JENIS as $pilihan)
                                <option value="{{ $pilihan }}" {{ ($jenis ?? '') === $pilihan ? 'selected' : '' }}>
                                    {{ $pilihan }}
                                </option>
                            @endforeach
                        </select>

                        <select name="sort" onchange="this.form.submit()"
                                aria-label="Urutkan berita"
                                class="w-full lg:w-44 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 font-semibold focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                            @foreach (\App\Models\Berita::SORTS as $nilai => $label)
                                <option value="{{ $nilai }}" {{ ($sort ?? 'terbaru') === $nilai ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="inline-flex flex-1 lg:flex-none items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                            </svg>
                            <span>Cari</span>
                        </button>

                        @if ($filtering)
                            <a href="{{ route('berita.index') }}"
                               class="inline-flex flex-1 lg:flex-none items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Main Table Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800">
                        {{ $filtering ? 'Hasil Pencarian Berita' : 'Daftar Seluruh Berita' }}
                    </h3>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">
                        {{ $filtering ? 'Ditemukan' : 'Total' }}: {{ $beritas->total() }} Data
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-xs uppercase font-bold text-slate-500 tracking-wider">
                                <th class="px-4 sm:px-6 py-4">Gambar</th>
                                <th class="px-4 sm:px-6 py-4">Berita</th>
                                <th class="px-4 sm:px-6 py-4">Info</th>
                                <th class="px-4 sm:px-6 py-4">Tanggal</th>
                                <th class="px-4 sm:px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($beritas as $berita)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    {{-- Gambar --}}
                                    <td class="px-4 sm:px-6 py-4">
                                        @if ($berita->gambar)
                                            <img src="{{ $berita->gambar_utama }}"
                                                 alt="{{ $berita->judul }}"
                                                 class="w-12 h-12 sm:w-14 sm:h-14 object-cover rounded-lg shadow-sm border border-slate-200">
                                        @else
                                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-slate-100 border border-slate-200 rounded-lg flex items-center justify-center text-slate-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Berita (judul + detail di bawah) --}}
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="font-bold text-slate-900 max-w-[16rem] sm:max-w-xs truncate">{{ $berita->judul }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5 truncate">/{{ $berita->slug }}</div>
                                        <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100/80 text-blue-800">
                                                {{ $berita->jenis }}
                                            </span>
                                            @if ($berita->status)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100/80 text-emerald-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Publish
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100/80 text-slate-600">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Draft
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Info (penulis + dibaca) --}}
                                    <td class="px-4 sm:px-6 py-4 text-slate-600">
                                        <div class="font-medium text-sm truncate max-w-[10rem]">{{ $berita->user->name ?? '-' }}</div>
                                        <div class="mt-1 inline-flex items-center gap-1.5 text-xs font-bold text-slate-600">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ number_format($berita->views) }} dibaca
                                        </div>
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="px-4 sm:px-6 py-4 text-slate-600 font-medium whitespace-nowrap">
                                        {{ ($berita->tanggal_tampil ?? $berita->created_at)->format('d M Y') }}
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="px-4 sm:px-6 py-4 text-center">
                                        <div class="inline-flex items-center gap-1.5 bg-slate-100/80 p-1.5 rounded-xl border border-slate-200/60">
                                            <a href="{{ route('berita.edit', $berita) }}"
                                               title="Edit Data"
                                               class="p-1.5 rounded-lg text-slate-600 hover:bg-white hover:text-blue-600 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            <form action="{{ route('berita.destroy', $berita) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        title="Hapus Data"
                                                        class="p-1.5 rounded-lg text-slate-600 hover:bg-white hover:text-rose-600 transition-all">
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
                                    <td colspan="5" class="text-center py-12">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <div class="p-4 bg-slate-100 text-slate-400 rounded-full">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                                </svg>
                                            </div>
                                            <p class="text-slate-500 font-medium">
                                                @if ($filtering)
                                                    Tidak ada berita yang cocok dengan pencarian Anda.
                                                @else
                                                    Belum ada berita yang ditambahkan.
                                                @endif
                                            </p>
                                            @if ($filtering)
                                                <a href="{{ route('berita.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
                                                    Reset pencarian
                                                </a>
                                            @else
                                                <a href="{{ route('berita.create') }}" class="text-xs font-bold text-blue-600 hover:underline">
                                                    + Klik di sini untuk menambah berita pertama
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($beritas->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $beritas->links() }}
                    </div>
                @endif
            </div>

            {{-- Peringkat Paling Banyak Dibaca (hanya saat tidak ada filter aktif) --}}
            @if (! $filtering && $terpopuler->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center gap-3">
                        <div class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Paling Banyak Dibaca</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Lima berita teratas berdasarkan jumlah pembaca.</p>
                        </div>
                    </div>

                    <ol class="divide-y divide-slate-100">
                        @foreach ($terpopuler as $index => $item)
                            <li class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50/80 transition-colors">
                                <span class="w-7 shrink-0 text-center font-heading text-lg font-extrabold text-slate-300">
                                    {{ $index + 1 }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ $item->judul }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        {{ $item->jenis }} · terbit {{ ($item->tanggal_tampil ?? $item->created_at)->format('d M Y') }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center gap-1.5 shrink-0 rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ number_format($item->views) }}
                                </span>
                                <a href="{{ route('berita.edit', $item) }}"
                                   class="shrink-0 rounded-lg p-1.5 text-slate-400 hover:bg-white hover:text-blue-600 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
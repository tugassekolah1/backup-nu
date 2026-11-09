<x-app-layout>

<div class="py-8 bg-slate-50 min-h-[calc(100vh-4rem)]">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 font-figtree antialiased">
    
    {{-- Header Section dengan Sapaan & Tanggal --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl shadow-xs border border-slate-200/80 transition-all duration-300 hover:shadow-md">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-emerald-500/10 text-emerald-600 rounded-2xl hidden sm:block">
                {{-- SVG Icon Dashboard Header --}}
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Selamat Datang di Panel Admin 👋</h1>
                <p class="text-sm text-slate-500 font-medium mt-1">Pilih menu di sidebar untuk mengelola data anggota, pengurus, atau berita dengan mudah.</p>
            </div>
        </div>
        
        {{-- Badge Tanggal --}}
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 font-semibold text-sm self-start md:self-auto shadow-xs">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>
    {{-- Interactive Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
    
        {{-- Card: Total Infak --}}
        <a href="{{ auth()->user()->is_admin ? route('admin.infaq.index') : route('infaq.index') }}"
           aria-label="Lihat Total Infak"
           class="group relative p-6 rounded-2xl border border-slate-200/80 bg-white hover:bg-emerald-50/40 hover:border-emerald-300 transition-all duration-300 flex flex-col justify-between gap-5 shadow-xs hover:shadow-xl hover:-translate-y-1.5 active:scale-[0.99]">
    
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1 min-w-0">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest group-hover:text-emerald-700 transition-colors">
                        Keuangan & Sedekah
                    </span>
                    <h2 class="text-lg font-bold text-slate-800 group-hover:text-emerald-950">Total Infak</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Lihat jumlah infak terkumpul dan catat transaksi baru.</p>
                </div>
    
                <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-xs group-hover:rotate-6 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
    
            <div class="flex flex-wrap items-end justify-between gap-x-3 gap-y-2 pt-2">
                <div class="min-w-0 flex-1">
                    <h3 class="text-2xl font-black text-slate-900 group-hover:text-emerald-900 transition-colors tracking-tight tabular-nums truncate">
                        Rp {{ number_format($totalInfaq ?? 0, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Total infak terkumpul</p>
                </div>
    
                <span class="inline-flex shrink-0 items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-100/80 group-hover:bg-emerald-600 group-hover:text-white px-3 py-2 rounded-xl transition-all duration-300 shadow-xs">
                    <span>{{ auth()->user()->is_admin ? 'Kelola Infaq' : 'Lihat Infaq' }}</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            </div>
        </a>
    
        {{-- Card: Kelola Anggota --}}
        <a href="{{ route('members.index') }}"
           aria-label="Kelola Anggota"
           class="group relative p-6 rounded-2xl border border-slate-200/80 bg-white hover:bg-emerald-50/40 hover:border-emerald-300 transition-all duration-300 flex flex-col justify-between gap-5 shadow-xs hover:shadow-xl hover:-translate-y-1.5 active:scale-[0.99]">
    
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1 min-w-0">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest group-hover:text-emerald-700 transition-colors">
                        Data Utama
                    </span>
                    <h2 class="text-lg font-bold text-slate-800 group-hover:text-emerald-950">Total Anggota</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Data seluruh anggota, termasuk menyetujui pendaftar baru.</p>
                </div>
                <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-xs group-hover:rotate-6 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
    
            <div class="flex flex-wrap items-end justify-between gap-x-3 gap-y-2 pt-2">
                <div class="min-w-0 flex-1">
                    <h3 class="text-3xl font-black text-slate-900 group-hover:text-emerald-900 transition-colors tracking-tight tabular-nums">
                        {{ $totalMembers ?? 0 }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Orang terdaftar</p>
                </div>
    
                <span class="inline-flex shrink-0 items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-100/80 group-hover:bg-emerald-600 group-hover:text-white px-3 py-2 rounded-xl transition-all duration-300 shadow-xs">
                    <span>Kelola Anggota</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            </div>
        </a>
    
        {{-- Card: Kelola Pengurus & Banom --}}
        <a href="{{ route('pengurus.index') }}"
           aria-label="Kelola Pengurus dan Banom"
           class="group relative p-6 rounded-2xl border border-slate-200/80 bg-white hover:bg-emerald-50/40 hover:border-emerald-300 transition-all duration-300 flex flex-col justify-between gap-5 shadow-xs hover:shadow-xl hover:-translate-y-1.5 active:scale-[0.99]">
    
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1 min-w-0">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest group-hover:text-emerald-700 transition-colors">
                        Struktur Organisasi
                    </span>
                    <h2 class="text-lg font-bold text-slate-800 group-hover:text-emerald-950">Pengurus & Banom</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Susunan pengurus tiap organisasi dan badan otonom.</p>
                </div>
    
                <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-xs group-hover:rotate-6 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V5"/>
                    </svg>
                </div>
            </div>
    
            <div class="flex flex-wrap items-end justify-between gap-x-3 gap-y-2 pt-2">
                <div class="min-w-0 flex-1">
                    <h3 class="text-3xl font-black text-slate-900 group-hover:text-emerald-900 transition-colors tracking-tight tabular-nums">
                        {{ $totalPengurus ?? 0 }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Pengurus aktif</p>
                </div>
    
                <span class="inline-flex shrink-0 items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-100/80 group-hover:bg-emerald-600 group-hover:text-white px-3 py-2 rounded-xl transition-all duration-300 shadow-xs">
                    <span>Kelola Pengurus</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            </div>
        </a>
    
        {{-- Card: Kelola Publikasi / Berita --}}
        <a href="{{ route('berita.index') }}"
           aria-label="Kelola Publikasi Berita"
           class="group relative p-6 rounded-2xl border border-slate-200/80 bg-white hover:bg-teal-50/40 hover:border-teal-300 transition-all duration-300 flex flex-col justify-between gap-5 shadow-xs hover:shadow-xl hover:-translate-y-1.5 active:scale-[0.99]">
    
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1 min-w-0">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest group-hover:text-teal-700 transition-colors">
                        Informasi & Artikel
                    </span>
                    <h2 class="text-lg font-bold text-slate-800 group-hover:text-teal-950">Total Publikasi</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Buat, ubah, dan terbitkan berita kegiatan.</p>
                </div>
                <div class="p-4 rounded-2xl bg-teal-50 text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-all duration-300 shadow-xs group-hover:rotate-6 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
            </div>
    
            <div class="flex flex-wrap items-end justify-between gap-x-3 gap-y-2 pt-2">
                <div class="min-w-0 flex-1">
                    <h3 class="text-3xl font-black text-slate-900 group-hover:text-teal-900 transition-colors tracking-tight tabular-nums">
                        {{ $totalNews ?? 0 }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Berita diterbitkan</p>
                    <p class="mt-2 inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 bg-amber-50 rounded-lg px-2 py-1 transition-colors group-hover:bg-amber-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ number_format($totalNewsViews ?? 0) }} kali dibaca
                    </p>
                </div>
    
                <span class="inline-flex shrink-0 items-center gap-2 text-xs font-bold text-teal-700 bg-teal-100/80 group-hover:bg-teal-600 group-hover:text-white px-3 py-2 rounded-xl transition-all duration-300 shadow-xs">
                    <span>Kelola Berita</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            </div>
        </a>
    
        {{-- Card: Kelola Galeri --}}
        <a href="{{ route('gallery.index') }}"
           aria-label="Kelola Galeri Kegiatan"
           class="group relative p-6 rounded-2xl border border-slate-200/80 bg-white hover:bg-teal-50/40 hover:border-teal-300 transition-all duration-300 flex flex-col justify-between gap-5 shadow-xs hover:shadow-xl hover:-translate-y-1.5 active:scale-[0.99]">
    
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1 min-w-0">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest group-hover:text-teal-700 transition-colors">
                        Dokumentasi & Foto
                    </span>
                    <h2 class="text-lg font-bold text-slate-800 group-hover:text-teal-950">Galeri Kegiatan</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Simpan foto dokumentasi kegiatan.</p>
                </div>
                <div class="p-4 rounded-2xl bg-teal-50 text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-all duration-300 shadow-xs group-hover:rotate-6 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
    
            <div class="flex flex-wrap items-end justify-between gap-x-3 gap-y-2 pt-2">
                <div class="min-w-0 flex-1">
                    <h3 class="text-3xl font-black text-slate-900 group-hover:text-teal-900 transition-colors tracking-tight tabular-nums">
                        {{ $totalGaleri ?? 0 }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Foto diunggah</p>
                </div>
    
                <span class="inline-flex shrink-0 items-center gap-2 text-xs font-bold text-teal-700 bg-teal-100/80 group-hover:bg-teal-600 group-hover:text-white px-3 py-2 rounded-xl transition-all duration-300 shadow-xs">
                    <span>Kelola Galeri</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            </div>
        </a>
    
        {{-- Card: Kelola Agenda --}}
        <a href="{{ route('agenda.index') }}"
           aria-label="Kelola Agenda Kegiatan"
           class="group relative p-6 rounded-2xl border border-slate-200/80 bg-white hover:bg-teal-50/40 hover:border-teal-300 transition-all duration-300 flex flex-col justify-between gap-5 shadow-xs hover:shadow-xl hover:-translate-y-1.5 active:scale-[0.99]">
    
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1 min-w-0">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest group-hover:text-teal-700 transition-colors">
                        Jadwal Kegiatan
                    </span>
                    <h2 class="text-lg font-bold text-slate-800 group-hover:text-teal-950">Agenda Kegiatan</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Atur jadwal dan agenda kegiatan.</p>
                </div>
                <div class="p-4 rounded-2xl bg-teal-50 text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-all duration-300 shadow-xs group-hover:rotate-6 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
    
            <div class="flex flex-wrap items-end justify-between gap-x-3 gap-y-2 pt-2">
                <div class="min-w-0 flex-1">
                    <h3 class="text-3xl font-black text-slate-900 group-hover:text-teal-900 transition-colors tracking-tight tabular-nums">
                        {{ $totalAgenda ?? 0 }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Kegiatan terdaftar</p>
                </div>
    
                <span class="inline-flex shrink-0 items-center gap-2 text-xs font-bold text-teal-700 bg-teal-100/80 group-hover:bg-teal-600 group-hover:text-white px-3 py-2 rounded-xl transition-all duration-300 shadow-xs">
                    <span>Kelola Agenda</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            </div>
        </a>
    </div>
    {{-- Section Panduan & Akses Cepat --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <h2 class="text-base font-bold text-slate-800">Menu Akses Cepat</h2>
        </div>
        
        <p class="text-xs text-slate-500">Klik tombol di bawah ini untuk menuju halaman pengelolaan secara langsung:</p>

        <div class="flex flex-wrap gap-3 pt-1">
            <a href="{{ route('members.index') }}" 
               class="inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-sm shadow-sm hover:shadow-emerald-200 hover:shadow-lg transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>Kelola Anggota</span>
            </a>

            <a href="{{ route('pengurus.index') }}" 
               class="inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-emerald-800 hover:bg-emerald-900 active:scale-95 text-white font-bold text-sm shadow-sm hover:shadow-emerald-300 hover:shadow-lg transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Kelola Pengurus</span>
            </a>

            <a href="{{ route('berita.index') }}" 
               class="inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 active:scale-95 text-white font-bold text-sm shadow-sm hover:shadow-teal-200 hover:shadow-lg transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Kelola Berita</span>
            </a>

            <a href="{{ route('agenda.index') }}"
               class="inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 active:scale-95 text-white font-bold text-sm shadow-sm hover:shadow-amber-200 hover:shadow-lg transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Kelola Agenda</span>
            </a>

            @if (auth()->user()->is_admin)
                <a href="{{ route('admin.infaq.create', ['arah' => 'keluar']) }}"
                   class="inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold text-sm shadow-sm hover:shadow-rose-200 hover:shadow-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zm0 8c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zM12 6V4m0 2v2m0 10v2m0-2v2"/>
                    </svg>
                    <span>Catat Pengeluaran</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Section: Anggota per Organisasi / Banom (dengan filter status) --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h2 class="text-base font-bold text-slate-800">Anggota per Organisasi / Banom</h2>
            </div>
            <a href="{{ route('members.index') }}" class="text-xs font-bold text-emerald-700 hover:underline">Kelola anggota</a>
        </div>

        @php
            $statusFilter = $statusFilter ?? 'all';
            $perOrg = $membersPerOrganisasi ?? collect();
            $pendingPerOrg = $pendingPerOrganisasi ?? collect();
            $approvedPerOrg = $approvedPerOrganisasi ?? collect();
            $filterTabs = [
                'all' => 'Semua',
                'pending' => 'Menunggu Disetujui',
                'approved' => 'Sudah Disetujui',
            ];
            $filterCounts = [
                'all' => (int) ($pendingCount ?? 0) + (int) ($approvedCount ?? 0),
                'pending' => (int) ($pendingCount ?? 0),
                'approved' => (int) ($approvedCount ?? 0),
            ];
        @endphp

        {{-- Filter status persetujuan --}}
        <div class="flex flex-wrap gap-2">
            @foreach ($filterTabs as $value => $label)
                <a href="{{ route('dashboard', $value === 'all' ? [] : ['status' => $value]) }}"
                   @class([
                       'px-3.5 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 inline-flex items-center gap-1.5',
                       'bg-emerald-600 text-white shadow-xs' => $statusFilter === $value,
                       'bg-white text-slate-600 border border-slate-200 hover:border-emerald-300 hover:text-emerald-700' => $statusFilter !== $value,
                   ])>
                    {{ $label }}
                    <span @class([
                        'px-1.5 py-0.5 rounded-md text-[11px] font-black',
                        'bg-white/20 text-white' => $statusFilter === $value,
                        'bg-slate-100 text-slate-600' => $statusFilter !== $value,
                    ])>{{ $filterCounts[$value] }}</span>
                </a>
            @endforeach
        </div>

        @foreach (\App\Models\NuMember::ORGANISASI_GROUPS as $group => $items)
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">{{ $group }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($items as $code => $desc)
                        @php
                            $jumlah = (int) ($perOrg[$code] ?? 0);
                            $cardUrl = route('members.index', array_filter([
                                'banom' => $code,
                                'status' => $statusFilter === 'all' ? null : $statusFilter,
                            ]));
                        @endphp
                        <a href="{{ $cardUrl }}"
                           class="group p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 hover:bg-emerald-50/50 hover:border-emerald-300 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md active:scale-[0.99]">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-800 group-hover:text-emerald-900 truncate" title="{{ \App\Models\Pengurus::BANOMS[$code]['label'] ?? $code }} — {{ $desc }}">
                                        {{ \App\Models\Pengurus::BANOMS[$code]['label'] ?? $code }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5 truncate" title="{{ $desc }}">{{ $desc }}</p>
                                </div>
                                <span class="text-2xl font-black text-slate-900 group-hover:text-emerald-700 tracking-tight shrink-0">{{ $jumlah }}</span>
                            </div>
                            <div class="mt-2.5 flex items-center justify-between gap-2">
                                @if ($statusFilter === 'all')
                                    <span class="text-[11px] font-semibold text-slate-500">
                                        <span class="text-amber-600 font-bold">{{ (int) ($pendingPerOrg[$code] ?? 0) }} menunggu</span>
                                        <span class="text-slate-300 mx-1">•</span>
                                        <span class="text-emerald-700 font-bold">{{ (int) ($approvedPerOrg[$code] ?? 0) }} disetujui</span>
                                    </span>
                                @else
                                    <span class="text-[11px] font-semibold {{ $statusFilter === 'pending' ? 'text-amber-600' : 'text-emerald-700' }}">
                                        {{ $statusFilter === 'pending' ? 'Menunggu persetujuan' : 'Sudah disetujui' }}
                                    </span>
                                @endif
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 group-hover:gap-2 transition-all">
                                    Lihat
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if (($membersTanpaOrganisasi ?? 0) > 0)
            <a href="{{ route('members.index', array_filter(['banom' => 'none', 'status' => $statusFilter === 'all' ? null : $statusFilter])) }}"
               class="flex items-center gap-3 pt-3 border-t border-slate-100 group">
                <span class="text-sm font-medium text-slate-400 italic group-hover:text-emerald-700">Tanpa organisasi</span>
                <div class="flex-1"></div>
                <span class="text-sm font-black text-slate-500">{{ $membersTanpaOrganisasi }} anggota →</span>
            </a>
        @endif
    </div>

    @if (auth()->user()->is_admin)
        {{-- Section: Transaksi Infaq Terbaru --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h2 class="text-base font-bold text-slate-800">Transaksi Infaq Terbaru</h2>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
                    <a href="{{ route('admin.infaq.index') }}" class="text-slate-600 hover:underline">Lihat semua</a>
                    <a href="{{ route('admin.infaq.create', ['arah' => 'keluar']) }}"
                       class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white transition-colors">
                        Catat Pengeluaran
                    </a>
                    <a href="{{ route('admin.infaq.create', ['arah' => 'masuk']) }}"
                       class="px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-800 text-white transition-colors">
                        Catat Pemasukan
                    </a>
                </div>
            </div>

            @if (($infaqTerbaru ?? collect())->isEmpty())
                <p class="text-sm text-slate-500">Belum ada transaksi infaq. Yuk, catat transaksi pertama.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-xs uppercase font-bold text-slate-400 border-b border-slate-100">
                                <th class="py-2 pr-4">Kode</th>
                                <th class="py-2 pr-4">Donatur</th>
                                <th class="py-2 pr-4">Tanggal</th>
                                <th class="py-2 pr-4 text-right">Nominal</th>
                                <th class="py-2 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($infaqTerbaru as $trx)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-2.5 pr-4 font-mono text-xs text-slate-500">{{ $trx->kode_transaksi }}</td>
                                    <td class="py-2.5 pr-4 font-semibold text-slate-700">{{ $trx->nama_donatur }}</td>
                                    <td class="py-2.5 pr-4 text-slate-500">{{ ($trx->paid_at ?? $trx->created_at)->format('d M Y') }}</td>
                                    <td class="py-2.5 pr-4 text-right font-bold text-slate-900">
                                        Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 text-right">
                                        @if ($trx->status === 'lunas')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Lunas</span>
                                        @elseif ($trx->status === 'pending')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Pending</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">Dibatalkan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

</div>
</div>
</x-app-layout>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <x-public-head title="Berita & Warta NU — Kecamatan Cilongok" />

    <style>
        /* Entrance animation (hormati reduced motion) */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1),
                        transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-stagger > * {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1),
                        transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .reveal-stagger.revealed > *:nth-child(1) { transition-delay: 0ms; }
        .reveal-stagger.revealed > *:nth-child(2) { transition-delay: 60ms; }
        .reveal-stagger.revealed > *:nth-child(3) { transition-delay: 120ms; }
        .reveal-stagger.revealed > *:nth-child(4) { transition-delay: 180ms; }
        .reveal-stagger.revealed > *:nth-child(5) { transition-delay: 240ms; }
        .reveal-stagger.revealed > *:nth-child(6) { transition-delay: 300ms; }
        .reveal-stagger.revealed > * {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            .reveal,
            .reveal-stagger > * {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }

        .bg-grid-light {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.12) 1px, transparent 0);
            background-size: 22px 22px;
        }
    </style>
</head>

<body class="bg-warm-bg text-charcoal antialiased selection:bg-muted-sage selection:text-nu-deep">
    <x-navbar></x-navbar>

    <main>
        <!-- HERO -->
        <header class="relative overflow-hidden bg-nu-deep text-white pt-32 sm:pt-36 pb-16 sm:pb-20">
            <div class="absolute inset-0 bg-grid-light pointer-events-none"></div>
            <div class="absolute -top-20 -right-16 w-96 h-96 rounded-full bg-nu-500/15 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-28 -left-16 w-80 h-80 rounded-full bg-muted-gold/15 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-8">
                <nav aria-label="Navigasi balik" class="mb-6 text-sm text-white/70">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a class="hover:text-white hover:underline underline-offset-4 transition-colors" href="{{ route('landing') }}">Beranda</a></li>
                        <li aria-hidden="true">›</li>
                        <li class="text-white font-semibold" aria-current="page">Berita</li>
                    </ol>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-end">
                    <div class="lg:col-span-7">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest backdrop-blur-sm">
                            <span class="material-symbols-outlined text-[16px] text-muted-gold">newspaper</span>
                            <span>Warta &amp; Informasi Resmi</span>
                        </span>

                        <h1 class="mt-6 font-heading text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.1]">
                            Berita &amp; Warta NU
                        </h1>

                        <p class="mt-5 text-base sm:text-lg text-white/80 leading-relaxed max-w-xl">
                            Informasi kegiatan, pengumuman, dan tulisan dari PRNU Kecamatan Cilongok dan
                            lembaga di bawah naungannya — disusun rapi dan mudah dibaca.
                        </p>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="rounded-container-r bg-white/10 border border-white/15 backdrop-blur-md p-6 sm:p-8 shadow-elevated">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <span class="block text-4xl font-extrabold text-white leading-none">{{ $totalBerita }}</span>
                                    <span class="block text-xs font-semibold uppercase tracking-wider text-white/70 mt-2">Berita Terbit</span>
                                </div>
                                <div>
                                    <span class="block text-4xl font-extrabold text-muted-gold leading-none">{{ $kanalCounts->count() }}</span>
                                    <span class="block text-xs font-semibold uppercase tracking-wider text-white/70 mt-2">Kanal Redaksi</span>
                                </div>
                            </div>
                            <div class="mt-6 pt-5 border-t border-white/15 flex items-center gap-2.5 text-xs text-white/75">
                                <span class="material-symbols-outlined text-[18px] text-muted-gold">verified</span>
                                <span>Dikelola sekretariat PRNU Kecamatan Cilongok.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- KANAL + PENCARIAN (pil judul melayang di batas hero) -->
        <section class="relative max-w-7xl mx-auto px-4 sm:px-8 pt-16 pb-2 reveal">
            <span class="absolute left-1/2 -translate-x-1/2 -top-5 z-20 inline-flex items-center gap-1.5 rounded-full bg-warm-card px-4 py-2 text-charcoal text-xs font-bold uppercase tracking-wider border border-border-neutral shadow-elevated">
                <span class="material-symbols-outlined text-[16px] text-muted-charcoal">filter_alt</span>
                <span>Kanal &amp; Pencarian</span>
            </span>

            <form method="GET" action="{{ route('berita.public') }}" class="flex flex-col gap-5">
                {{-- Pencarian --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="relative flex-1">
                        <input type="search" name="q" value="{{ $q }}"
                               placeholder="Cari berita... misal: pengajian, banjir, qurban"
                               aria-label="Cari berita"
                               class="w-full min-h-[48px] pl-5 pr-12 py-3 rounded-full bg-warm-card border border-border-neutral text-base text-charcoal placeholder:text-muted-charcoal/70 focus:outline-none focus:border-nu-deep focus:ring-2 focus:ring-nu-deep/15 transition-all"/>
                        <span class="material-symbols-outlined text-[20px] text-muted-charcoal absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
                    </div>
                    @if (!empty($jenis))
                        <input type="hidden" name="jenis" value="{{ $jenis }}"/>
                    @endif
                    <input type="hidden" name="sort" value="{{ $sort ?? 'terbaru' }}"/>
                    <button type="submit"
                            class="min-h-[48px] px-7 rounded-full bg-nu-deep text-white text-sm font-semibold hover:bg-[#113725] shadow-subtle transition-all active:scale-95">
                        Cari
                    </button>
                    @if (trim((string) $q) !== '' || !empty($jenis))
                        <a href="{{ route('berita.public') }}"
                           class="min-h-[48px] px-6 inline-flex items-center justify-center rounded-full bg-warm-card border border-border-neutral text-charcoal text-sm font-semibold hover:bg-warm-beige/60 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>

                {{-- Tab kanal --}}
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-muted-charcoal mr-1">
                        <span class="material-symbols-outlined text-[16px]">label</span>
                        Kanal:
                    </span>
                    <a href="{{ route('berita.public', array_filter(['q' => $q, 'sort' => $sort])) }}"
                       class="{{ empty($jenis) ? 'bg-nu-deep text-white border-nu-deep' : 'bg-warm-card text-charcoal border-border-neutral hover:bg-warm-beige/60' }} min-h-[44px] inline-flex items-center gap-2 px-5 rounded-full border text-sm font-semibold transition-colors">
                        Semua
                    </a>
                    @foreach (\App\Models\Berita::JENIS as $pilihan)
                        <a href="{{ route('berita.public', array_filter(['q' => $q, 'jenis' => $pilihan, 'sort' => $sort])) }}"
                           class="{{ ($jenis ?? '') === $pilihan ? 'bg-nu-deep text-white border-nu-deep' : 'bg-warm-card text-charcoal border-border-neutral hover:bg-warm-beige/60' }} min-h-[44px] inline-flex items-center gap-2 px-5 rounded-full border text-sm font-semibold transition-colors">
                            {{ $pilihan }}
                            @isset($kanalCounts[$pilihan])
                                <span class="{{ ($jenis ?? '') === $pilihan ? 'bg-white/20 text-white' : 'bg-muted-sage text-muted-charcoal' }} text-[11px] font-bold px-2 py-0.5 rounded-full">{{ $kanalCounts[$pilihan] }}</span>
                            @endisset
                        </a>
                    @endforeach
                </div>

                {{-- Urutan tampil: Terbaru / Terpopuler / Terlama --}}
                <div class="flex flex-wrap items-center gap-2.5 pt-1 border-t border-border-neutral">
                    <span class="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-muted-charcoal mr-1 mt-2">
                        <span class="material-symbols-outlined text-[16px]">sort</span>
                        Urutkan:
                    </span>
                    @php
                        $ikonSort = ['terbaru' => 'schedule', 'terpopuler' => 'trending_up', 'terlama' => 'history'];
                    @endphp
                    @foreach (\App\Models\Berita::SORTS as $nilai => $label)
                        <a href="{{ route('berita.public', array_filter(['q' => $q, 'jenis' => $jenis, 'sort' => $nilai])) }}"
                           @class([
                               'mt-2 min-h-[44px] inline-flex items-center gap-2 px-5 rounded-full border text-sm font-semibold transition-colors',
                               'bg-nu-deep text-white border-nu-deep' => ($sort ?? 'terbaru') === $nilai,
                               'bg-warm-card text-charcoal border-border-neutral hover:bg-warm-beige/60' => ($sort ?? 'terbaru') !== $nilai,
                           ])>
                            <span class="material-symbols-outlined text-[16px]">{{ $ikonSort[$nilai] }}</span>
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </form>
        </section>

        @if ($isFiltering)
            {{-- ========================================== --}}
            {{-- HASIL PENCARIAN / FILTER KANAL --}}
            {{-- ========================================== --}}
            <section class="max-w-7xl mx-auto px-4 sm:px-8 py-10 reveal">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-3">
                    <div>
                        <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-charcoal tracking-tight">
                            Hasil Pencarian
                        </h2>
                        <p class="text-sm text-muted-charcoal mt-1">
                            @if (trim((string) $q) !== '')
                                Kata kunci “{{ $q }}”{{ !empty($jenis) ? ' · kanal ' . $jenis : '' }}
                            @else
                                Kanal {{ $jenis }}
                            @endif
                        </p>
                    </div>
                    <span class="w-fit text-sm font-semibold text-muted-charcoal bg-warm-card border border-border-neutral rounded-full px-4 py-1.5">
                        {{ $newsList->total() }} berita ditemukan
                    </span>
                </div>

                @if ($newsList->isEmpty())
                    <div class="bg-warm-card rounded-container-r border border-border-neutral shadow-subtle p-12 flex flex-col items-center text-center">
                        <span class="material-symbols-outlined text-muted-charcoal text-5xl mb-3">search_off</span>
                        <p class="text-lg font-bold text-charcoal">Tidak ada berita yang cocok</p>
                        <p class="text-sm text-muted-charcoal mt-1.5 max-w-md">
                            Coba kata kunci lain, atau pilih kanal “Semua” untuk melihat seluruh berita.
                        </p>
                        <a class="mt-6 inline-flex items-center gap-2 px-6 py-3 min-h-[44px] rounded-full bg-nu-deep text-white text-sm font-semibold hover:bg-[#113725] transition-all active:scale-95"
                           href="{{ route('berita.public') }}">
                            <span class="material-symbols-outlined text-[18px]">refresh</span>
                            <span>Lihat Semua Berita</span>
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 reveal-stagger">
                        @foreach ($newsList as $berita)
                            <x-news-card :berita="$berita" />
                        @endforeach
                    </div>
                @endif
            </section>

        @else

            {{-- ========================================== --}}
            {{-- LEAD STORY (menyesuaikan urutan yang dipilih) --}}
            {{-- ========================================== --}}
            @php
                $judulLead = [
                    'terbaru' => ['label' => 'Berita Utama', 'ikon' => 'star'],
                    'terpopuler' => ['label' => 'Paling Banyak Dibaca', 'ikon' => 'trending_up'],
                    'terlama' => ['label' => 'Arsip Terlama', 'ikon' => 'history'],
                ][$sort ?? 'terbaru'];
            @endphp
            @if ($newsList->isNotEmpty())
                @php $featuredNews = $newsList->first(); @endphp
                <section class="max-w-7xl mx-auto px-4 sm:px-8 py-10 reveal">
                    <article class="group bg-warm-card rounded-container-r border border-border-neutral shadow-subtle overflow-hidden lg:grid lg:grid-cols-12 hover:shadow-elevated transition-shadow duration-300">
                        <div class="lg:col-span-8 relative aspect-[16/10] sm:aspect-[16/9] overflow-hidden bg-warm-beige">
                            @if ($featuredNews->gambar_utama)
                                <img src="{{ $featuredNews->gambar_utama }}"
                                     alt="{{ $featuredNews->judul }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"/>
                            @else
                                <div class="w-full h-full flex items-center justify-center text-muted-charcoal/60">
                                    <span class="material-symbols-outlined text-7xl">article</span>
                                </div>
                            @endif
                            <span class="absolute top-4 left-4 inline-flex items-center gap-1.5 rounded-full bg-nu-deep/90 backdrop-blur-sm text-white text-[11px] font-bold uppercase tracking-wider px-3 py-1.5 border border-white/15">
                                <span class="w-1.5 h-1.5 rounded-full bg-muted-gold"></span>
                                {{ $judulLead['label'] }}
                            </span>
                        </div>

                        <div class="lg:col-span-4 p-7 sm:p-9 flex flex-col">
                            <div class="flex flex-wrap items-center gap-2.5 mb-3">
                                <span class="px-3 py-1 rounded-full bg-muted-sage text-charcoal text-xs font-bold uppercase tracking-wider border border-border-neutral">
                                    {{ $featuredNews->jenis }}
                                </span>
                                <span class="text-xs font-semibold text-muted-charcoal">
                                    {{ $featuredNews->created_at->locale('id')->translatedFormat('d F Y') }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-muted-charcoal">
                                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                                    {{ number_format($featuredNews->views) }} kali dibaca
                                </span>
                            </div>

                            <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-charcoal tracking-tight leading-snug group-hover:text-nu-deep transition-colors">
                                <a href="{{ route('berita.show', $featuredNews->slug) }}">{{ $featuredNews->judul }}</a>
                            </h2>

                            <p class="text-sm sm:text-base text-muted-charcoal leading-relaxed mt-3">
                                {{ Str::limit(strip_tags($featuredNews->isi), 200) }}
                            </p>

                            <div class="mt-auto pt-7">
                                <a class="inline-flex items-center gap-2 px-6 py-3 min-h-[48px] rounded-full bg-nu-deep text-white text-sm font-semibold hover:bg-[#113725] shadow-subtle transition-all active:scale-95"
                                   href="{{ route('berita.show', $featuredNews->slug) }}">
                                    <span>Baca selengkapnya</span>
                                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </article>
                </section>
            @endif

            {{-- ========================================== --}}
            {{-- DAFTAR BERITA + SIDEBAR --}}
            {{-- ========================================== --}}
            @php
                $judulBagian = [
                    'terbaru' => ['badge' => 'Terbaru dari Redaksi', 'judul' => 'Berita Terbaru', 'deskripsi' => 'Kabar terkini dari ranting dan lembaga Kecamatan Cilongok.', 'ikon' => 'history'],
                    'terpopuler' => ['badge' => 'Paling Banyak Dibaca', 'judul' => 'Berita Terpopuler', 'deskripsi' => 'Berita yang paling sering dibaca pengunjung.', 'ikon' => 'trending_up'],
                    'terlama' => ['badge' => 'Arsip Awal', 'judul' => 'Berita Terlama', 'deskripsi' => 'Berita awal yang menjadi cikal bakal informasi ini.', 'ikon' => 'history'],
                ][$sort ?? 'terbaru'];
            @endphp
            <section class="max-w-7xl mx-auto px-4 sm:px-8 py-6 pb-4 reveal">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-muted-sage text-charcoal text-xs font-semibold uppercase tracking-wider mb-3 border border-border-neutral">
                            <span class="material-symbols-outlined text-[16px] text-muted-charcoal">{{ $judulBagian['ikon'] }}</span>
                            {{ $judulBagian['badge'] }}
                        </span>
                        <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-charcoal tracking-tight">{{ $judulBagian['judul'] }}</h2>
                        <p class="text-base text-muted-charcoal mt-2">{{ $judulBagian['deskripsi'] }}</p>
                    </div>
                    <span class="text-sm font-semibold text-muted-charcoal bg-warm-card border border-border-neutral rounded-full px-4 py-1.5">
                        {{ $newsList->total() }} berita
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    {{-- Kolom utama: kartu seragam --}}
                    <div class="lg:col-span-8">
                        @if ($newsList->count() > 1)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 reveal-stagger">
                                @foreach ($newsList->skip(1) as $berita)
                                    <x-news-card :berita="$berita" />
                                @endforeach
                            </div>
                        @else
                            <div class="bg-warm-card rounded-container-r border border-border-neutral shadow-subtle p-10 text-center">
                                <span class="material-symbols-outlined text-muted-charcoal text-4xl mb-2">construction</span>
                                <p class="text-sm font-semibold text-charcoal">Belum ada berita lain untuk ditampilkan.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Sidebar --}}
                    <aside class="lg:col-span-4 flex flex-col gap-6">
                        {{-- Terpopuler --}}
                        <div class="bg-warm-card rounded-card border border-border-neutral shadow-subtle p-6">
                            <h3 class="flex items-center gap-2 font-heading text-lg font-extrabold text-charcoal mb-4">
                                <span class="material-symbols-outlined text-[20px] text-muted-gold">trending_up</span>
                                Paling Banyak Dibaca
                            </h3>

                            @if ($popular->isEmpty())
                                <p class="text-sm text-muted-charcoal">Belum ada data pembaca.</p>
                            @else
                                <ol class="flex flex-col">
                                    @foreach ($popular as $index => $berita)
                                        <li class="{{ $loop->first ? '' : 'border-t border-border-neutral' }}">
                                            <a class="group flex items-start gap-3.5 py-3.5" href="{{ route('berita.show', $berita->slug) }}">
                                                <span class="font-heading text-2xl font-extrabold leading-none w-7 shrink-0 {{ $index < 3 ? 'text-muted-gold' : 'text-border-subtle' }}">
                                                    {{ $index + 1 }}
                                                </span>
                                                <span class="min-w-0">
                                                    <span class="block text-sm font-bold text-charcoal leading-snug group-hover:text-nu-deep transition-colors">{{ $berita->judul }}</span>
                                                    <span class="mt-1 block text-[11px] font-semibold text-muted-charcoal">
                                                        {{ $berita->created_at->locale('id')->translatedFormat('d F Y') }}
                                                        · {{ number_format($berita->views) }} dibaca
                                                    </span>
                                                </span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </div>

                        {{-- Kanal --}}
                        <div class="bg-warm-card rounded-card border border-border-neutral shadow-subtle p-6">
                            <h3 class="flex items-center gap-2 font-heading text-lg font-extrabold text-charcoal mb-4">
                                <span class="material-symbols-outlined text-[20px] text-muted-gold">grid_view</span>
                                Jelajahi Kanal
                            </h3>
                            <ul class="flex flex-col">
                                @foreach (\App\Models\Berita::JENIS as $pilihan)
                                    <li class="{{ $loop->first ? '' : 'border-t border-border-neutral' }}">
                                        <a class="flex items-center justify-between py-3 group" href="{{ route('berita.public', array_filter(['jenis' => $pilihan])) }}">
                                            <span class="text-sm font-semibold text-charcoal group-hover:text-nu-deep transition-colors">{{ $pilihan }}</span>
                                            <span class="inline-flex items-center gap-2 text-xs font-bold text-muted-charcoal">
                                                {{ $kanalCounts[$pilihan] ?? 0 }} berita
                                                <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">chevron_right</span>
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Kutipan --}}
                        <div class="rounded-card bg-warm-beige/70 border border-border-subtle p-6 border-l-4 border-l-muted-gold">
                            <span class="text-[11px] font-bold uppercase tracking-widest text-nu-deep block mb-3">Kutipan Pengurus</span>
                            <blockquote class="font-heading text-lg text-charcoal leading-snug mb-2">
                                “Merawat kebersamaan ranting adalah menyalakan obor ketentraman di setiap sudut desa kita.”
                            </blockquote>
                            <span class="text-xs font-semibold text-muted-charcoal">— Rais Syuriyah MWC NU Kecamatan</span>
                        </div>
                    </aside>
                </div>
            </section>
        @endif

        {{-- ========================================== --}}
        {{-- PAGINATION --}}
        {{-- ========================================== --}}
        @if ($newsList instanceof \Illuminate\Pagination\LengthAwarePaginator && $newsList->hasPages())
            <section class="max-w-7xl mx-auto px-4 sm:px-8 py-10 flex flex-col items-center gap-3 reveal">
                <nav role="navigation" aria-label="Navigasi halaman berita" class="flex flex-wrap items-center justify-center gap-2">
                    @if ($newsList->onFirstPage())
                        <span class="px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full bg-warm-beige/60 text-muted-charcoal text-sm font-semibold border border-border-subtle cursor-not-allowed opacity-60">‹ Sebelumnya</span>
                    @else
                        <a href="{{ $newsList->previousPageUrl() }}" rel="prev" class="px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full bg-warm-card text-charcoal text-sm font-semibold border border-border-subtle hover:bg-warm-beige/60 transition-colors">‹ Sebelumnya</a>
                    @endif

                    <div class="flex flex-wrap items-center justify-center gap-2">
                        @foreach ($newsList->links()->elements as $element)
                            @if (is_string($element))
                                <span class="px-2 text-muted-charcoal text-sm">{{ $element }}</span>
                            @endif
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $newsList->currentPage())
                                        <span aria-current="page" class="px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full bg-nu-deep text-white text-sm font-bold">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full bg-warm-card text-charcoal text-sm font-semibold border border-border-subtle hover:bg-warm-beige/60 transition-colors">{{ $page }}</a>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </div>

                    @if ($newsList->hasMorePages())
                        <a href="{{ $newsList->nextPageUrl() }}" rel="next" class="px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full bg-warm-card text-charcoal text-sm font-semibold border border-border-subtle hover:bg-warm-beige/60 transition-colors">Selanjutnya ›</a>
                    @else
                        <span class="px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full bg-warm-beige/60 text-muted-charcoal text-sm font-semibold border border-border-subtle cursor-not-allowed opacity-60">Selanjutnya ›</span>
                    @endif
                </nav>
                <span class="text-xs text-muted-charcoal">
                    Menampilkan {{ $newsList->firstItem() ?? 0 }}–{{ $newsList->lastItem() ?? 0 }} dari {{ $newsList->total() }} berita
                </span>
            </section>
        @endif
    </main>

    <x-footer />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const targets = document.querySelectorAll('.reveal, .reveal-stagger');

            if (reduceMotion || !('IntersectionObserver' in window)) {
                targets.forEach((el) => el.classList.add('revealed'));
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

            targets.forEach((el) => observer.observe(el));
        });
    </script>
</body>
</html>

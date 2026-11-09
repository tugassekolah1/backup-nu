<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <x-public-head title="Agenda Kegiatan — NU Kecamatan Cilongok" />

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
    <x-navbar />

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
                        <li class="text-white font-semibold" aria-current="page">Agenda</li>
                    </ol>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-end">
                    <div class="lg:col-span-7">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest backdrop-blur-sm">
                            <span class="material-symbols-outlined text-[16px] text-nu-500">event_upcoming</span>
                            <span>Jadwal Kegiatan Ranting</span>
                        </span>

                        <h1 class="mt-6 font-heading text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.1]">
                            Agenda &amp;<br>Majelis Taklim
                        </h1>

                        <p class="mt-5 text-base sm:text-lg text-white/80 leading-relaxed max-w-xl">
                            Ikuti kegiatan rutin, pengajian kitab, dan agenda sosial Nahdlatul Ulama Kecamatan Cilongok.
                            Semua terbuka untuk warga dan jamaah.
                        </p>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="rounded-container-r bg-white/10 border border-white/15 backdrop-blur-md p-6 sm:p-8 shadow-elevated">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <span class="block text-4xl font-extrabold text-white leading-none">{{ $upcomingAgenda->total() }}</span>
                                    <span class="block text-xs font-semibold uppercase tracking-wider text-white/70 mt-2">Agenda Mendatang</span>
                                </div>
                                <div>
                                    <span class="block text-4xl font-extrabold text-muted-gold leading-none">{{ $pastAgenda->count() }}</span>
                                    <span class="block text-xs font-semibold uppercase tracking-wider text-white/70 mt-2">Terakhir Berlangsung</span>
                                </div>
                            </div>
                            <div class="mt-6 pt-5 border-t border-white/15 flex items-center gap-2.5 text-xs text-white/75">
                                <span class="material-symbols-outlined text-[18px] text-muted-gold">groups</span>
                                <span>Terbuka untuk umum — putra &amp; putri, tanpa biaya tiket.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- AGENDA TERDEKAT -->
        <section class="relative max-w-7xl mx-auto px-4 sm:px-8 py-16 sm:py-24 reveal" id="terdekat">
            {{-- Pil judul melayang di batas hero --}}
            <span class="absolute left-1/2 -translate-x-1/2 -top-5 z-20 inline-flex items-center gap-1.5 rounded-full bg-warm-card px-4 py-2 text-charcoal text-xs font-bold uppercase tracking-wider border border-border-neutral shadow-elevated">
                <span class="material-symbols-outlined text-[16px] text-muted-charcoal">calendar_month</span>
                <span>Agenda Terdekat</span>
            </span>
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-charcoal tracking-tight">Kegiatan yang Akan Datang</h2>
                    <p class="text-base text-muted-charcoal mt-2">Susunan acara terbaru dari sekretariat PRNU Kecamatan Cilongok.</p>
                </div>
                <span class="text-sm font-semibold text-muted-charcoal bg-warm-card border border-border-neutral rounded-full px-4 py-1.5">
                    {{ $upcomingAgenda->total() }} agenda terjadwal
                </span>
            </div>

            @if ($upcomingAgenda->isNotEmpty())
                @php $featured = $upcomingAgenda->first(); @endphp

                <!-- FEATURED -->
                <article class="relative bg-warm-card rounded-container-r border border-border-neutral shadow-subtle overflow-hidden mb-10 lg:grid lg:grid-cols-12 hover:shadow-elevated transition-shadow duration-300">
                    <div class="relative lg:col-span-4 bg-nu-deep text-white p-8 sm:p-10 flex flex-col justify-center overflow-hidden">
                        <div class="absolute inset-0 bg-grid-light opacity-50 pointer-events-none"></div>
                        <div class="absolute -bottom-16 -right-16 w-48 h-48 rounded-full bg-muted-gold/20 blur-3xl pointer-events-none"></div>
                        <span class="relative text-xs font-bold uppercase tracking-widest text-muted-gold mb-5">Agenda Utama Terdekat</span>
                        <span class="relative block text-7xl font-extrabold leading-none">{{ $featured->event_date->format('d') }}</span>
                        <span class="relative block text-xl font-bold uppercase tracking-widest mt-3 text-white/85">
                            {{ strtoupper($featured->event_date->locale('id')->translatedFormat('M')) }}
                        </span>
                        <span class="relative block text-sm font-semibold text-white/70 mt-1.5">
                            {{ $featured->event_date->locale('id')->translatedFormat('l') }}
                        </span>
                        <span class="relative inline-flex w-fit items-center gap-1.5 mt-7 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold">
                            <span class="material-symbols-outlined text-[15px]">schedule</span>
                            {{ $featured->event_time ?? 'Waktu menyusul' }}
                        </span>
                    </div>

                    <div class="lg:col-span-8 p-8 sm:p-10 flex flex-col">
                        <div class="flex flex-wrap items-center gap-2.5 mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-muted-sage text-charcoal text-xs font-bold uppercase tracking-wider border border-border-neutral">
                                <span class="w-1.5 h-1.5 rounded-full bg-nu-700"></span>
                                {{ $featured->category ?: 'Kegiatan' }}
                            </span>
                            <span class="text-xs text-muted-charcoal">
                                {{ $featured->event_date->locale('id')->translatedFormat('d F Y') }}
                            </span>
                        </div>

                        <h3 class="font-heading text-2xl sm:text-3xl font-extrabold text-charcoal tracking-tight leading-snug">
                            {{ $featured->title }}
                        </h3>

                        @if ($featured->description)
                            <p class="text-sm sm:text-base text-muted-charcoal leading-relaxed mt-3">
                                {{ Str::limit($featured->description, 320) }}
                            </p>
                        @endif

                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-card bg-warm-bg border border-border-neutral text-sm">
                            @if ($featured->location)
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($featured->location) }}" 
           target="_blank" 
           rel="noopener noreferrer"
           class="flex items-start gap-2.5 group/loc hover:bg-warm-beige/60 -m-1 p-1 rounded-lg transition-colors"
           title="Buka petunjuk arah ke lokasi di Google Maps">
            <span class="material-symbols-outlined text-nu-deep text-[20px] shrink-0">directions</span>
            <span class="font-medium text-charcoal group-hover/loc:text-nu-deep group-hover/loc:underline underline-offset-2">{{ $featured->location }}</span>
            <span class="material-symbols-outlined text-[14px] text-muted-charcoal shrink-0 mt-0.5">open_in_new</span>
        </a>
                            @endif
                            <div class="flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-muted-charcoal text-[20px] shrink-0">event</span>
                                <span class="font-medium text-charcoal">
                                    {{ $featured->event_date->locale('id')->translatedFormat('l, d F Y') }}
                                    @if ($featured->event_time) • {{ $featured->event_time }} @endif
                                </span>
                            </div>
                        </div>

                        @if ($featured->embed_url)
                            <div class="mt-4 overflow-hidden rounded-card border border-border-neutral">
                                <iframe title="Peta lokasi {{ $featured->title }}"
                                        src="{{ $featured->embed_url }}"
                                        class="w-full h-56 sm:h-64" style="border:0" loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                            </div>
                        @endif

                        <div class="mt-auto pt-8 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="text-xs font-medium text-muted-charcoal">Terbuka untuk umum</span>
                                @if ($featured->maps_link)
                                    <a class="inline-flex items-center gap-1.5 px-4 py-2 min-h-[40px] rounded-full bg-muted-sage text-charcoal text-xs font-bold hover:bg-warm-beige transition-all active:scale-95 border border-border-neutral"
                                       href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($featured->location) }}" target="_blank" rel="noopener noreferrer">
                                        <span class="material-symbols-outlined text-[16px]">directions</span>
                                        <span>Buka di Google Maps</span>
                                    </a>
                                @endif
                            </div>
                            <a class="inline-flex items-center gap-2 px-6 py-3 min-h-[48px] rounded-full bg-nu-deep text-white text-sm font-semibold hover:bg-[#113725] shadow-subtle transition-all active:scale-95"
                               href="https://wa.me/{{ config('kontak.whatsapp') }}?text={{ urlencode('Assalamualaikum, saya ingin bertanya tentang agenda: ' . $featured->title) }}"
                               rel="noopener noreferrer" target="_blank">
                                <span>Tanya Jadwal</span>
                                <span class="material-symbols-outlined text-[18px]">chat</span>
                            </a>
                        </div>
                    </div>
                </article>

                <!-- Grid agenda lainnya -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 reveal-stagger">
                    @foreach ($upcomingAgenda->skip(1) as $agenda)
                        <article class="group bg-warm-card rounded-card border border-border-neutral shadow-subtle p-6 flex flex-col hover:-translate-y-1.5 hover:shadow-elevated hover:border-border-subtle transition-all duration-300">
                            <div class="flex items-start justify-between gap-3 mb-5">
                                <div class="w-16 shrink-0 py-2.5 rounded-[18px] bg-nu-deep text-white text-center shadow-subtle group-hover:bg-nu-night transition-colors">
                                    <span class="block text-2xl font-extrabold leading-none">{{ $agenda->event_date->format('d') }}</span>
                                    <span class="block text-[10px] font-bold uppercase tracking-widest mt-1.5 text-white/75">
                                        {{ strtoupper($agenda->event_date->locale('id')->translatedFormat('M')) }}
                                    </span>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-warm-beige text-charcoal text-[11px] font-bold uppercase tracking-wider border border-border-subtle">
                                    <span class="w-1.5 h-1.5 rounded-full bg-muted-gold"></span>
                                    {{ $agenda->category ?: 'Kegiatan' }}
                                </span>
                            </div>

                            <span class="text-xs font-semibold text-muted-charcoal">
                                {{ $agenda->event_date->locale('id')->translatedFormat('l, d F Y') }}
                            </span>

                            <h3 class="text-lg font-bold text-charcoal leading-snug mt-2 group-hover:text-nu-deep transition-colors">
                                {{ $agenda->title }}
                            </h3>

                            @if ($agenda->description)
                                <p class="text-sm text-muted-charcoal leading-relaxed mt-2 line-clamp-3">
                                    {{ Str::limit($agenda->description, 150) }}
                                </p>
                            @endif

                            <div class="mt-auto pt-5 mt-5 border-t border-border-neutral flex flex-col gap-2 text-xs font-medium text-muted-charcoal">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                                    <span>{{ $agenda->event_time ?? 'Waktu menyusul' }}</span>
                                </div>
                                <a href="{{ $agenda->maps_link }}" target="_blank" rel="noopener noreferrer"
                                   class="flex items-center gap-2 hover:text-nu-deep transition-colors group/loc"
                                   title="Buka lokasi di Google Maps">
                                    <span class="material-symbols-outlined text-[16px]">location_on</span>
                                    <span class="truncate group-hover/loc:underline underline-offset-2">{{ $agenda->location ?? 'Sekretariat PRNU Kecamatan Cilongok' }}</span>
                                    <span class="material-symbols-outlined text-[13px] shrink-0">open_in_new</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($upcomingAgenda->hasPages())
                    <nav class="mt-12 flex flex-col items-center gap-3" aria-label="Navigasi halaman agenda">
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            @if ($upcomingAgenda->onFirstPage())
                                <span class="px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full bg-warm-beige/60 text-muted-charcoal text-sm font-semibold border border-border-subtle cursor-not-allowed opacity-60">‹ Sebelumnya</span>
                            @else
                                <a href="{{ $upcomingAgenda->previousPageUrl() }}" rel="prev" class="px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full bg-warm-card text-charcoal text-sm font-semibold border border-border-subtle hover:bg-warm-beige/60 transition-colors">‹ Sebelumnya</a>
                            @endif

                            @for ($page = max(1, $upcomingAgenda->currentPage() - 2); $page <= min($upcomingAgenda->lastPage(), $upcomingAgenda->currentPage() + 2); $page++)
                                @if ($page == $upcomingAgenda->currentPage())
                                    <span aria-current="page" class="px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full bg-nu-deep text-white text-sm font-bold">{{ $page }}</span>
                                @else
                                    <a href="{{ $upcomingAgenda->url($page) }}" class="px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full bg-warm-card text-charcoal text-sm font-semibold border border-border-subtle hover:bg-warm-beige/60 transition-colors">{{ $page }}</a>
                                @endif
                            @endfor

                            @if ($upcomingAgenda->hasMorePages())
                                <a href="{{ $upcomingAgenda->nextPageUrl() }}" rel="next" class="px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full bg-warm-card text-charcoal text-sm font-semibold border border-border-subtle hover:bg-warm-beige/60 transition-colors">Selanjutnya ›</a>
                            @else
                                <span class="px-4 py-2.5 min-h-[44px] inline-flex items-center rounded-full bg-warm-beige/60 text-muted-charcoal text-sm font-semibold border border-border-subtle cursor-not-allowed opacity-60">Selanjutnya ›</span>
                            @endif
                        </div>
                        <span class="text-xs text-muted-charcoal">
                            Halaman {{ $upcomingAgenda->currentPage() }} dari {{ $upcomingAgenda->lastPage() }}
                        </span>
                    </nav>
                @endif
            @else
                <div class="bg-warm-card rounded-container-r border border-border-neutral shadow-subtle p-12 flex flex-col items-center text-center">
                    <span class="material-symbols-outlined text-muted-charcoal text-5xl mb-3">event_busy</span>
                    <p class="text-lg font-bold text-charcoal">Belum ada agenda terdekat</p>
                    <p class="text-sm text-muted-charcoal mt-1.5 max-w-md">
                        Jadwal kegiatan berikutnya akan segera diumumkan. Silakan hubungi sekretariat untuk informasi kegiatan rutin mingguan.
                    </p>
                    <a class="mt-6 inline-flex items-center gap-2 px-6 py-3 min-h-[44px] rounded-full bg-nu-deep text-white text-sm font-semibold hover:bg-[#113725] transition-all active:scale-95"
                       href="https://wa.me/{{ config('kontak.whatsapp') }}?text={{ urlencode('Assalamualaikum, mohon info jadwal kegiatan NU Kecamatan Cilongok') }}"
                       rel="noopener noreferrer" target="_blank">
                        <span class="material-symbols-outlined text-[18px]">chat</span>
                        <span>Tanya Jadwal via WhatsApp</span>
                    </a>
                </div>
            @endif
        </section>

        <!-- ARSIP -->
        <section class="relative w-full bg-warm-card border-y border-border-neutral py-16 sm:py-24 reveal" id="arsip">
            {{-- Pil judul melayang di batas atas section arsip --}}
            <span class="absolute left-1/2 -translate-x-1/2 -top-5 z-20 inline-flex items-center gap-1.5 rounded-full bg-warm-card px-4 py-2 text-charcoal text-xs font-bold uppercase tracking-wider border border-border-neutral shadow-elevated">
                <span class="material-symbols-outlined text-[16px] text-muted-charcoal">history</span>
                <span>Arsip Kegiatan</span>
            </span>
            <div class="max-w-7xl mx-auto px-4 sm:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                    <div>
                        <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-charcoal tracking-tight">Agenda Telah Berlangsung</h2>
                        <p class="text-base text-muted-charcoal mt-2">Rangkaian kegiatan terakhir bersama jamaah Kecamatan Cilongok.</p>
                    </div>
                </div>

                @if ($pastAgenda->isNotEmpty())
                    <div class="relative flex flex-col gap-5 before:absolute before:left-[27px] before:top-4 before:bottom-4 before:w-px before:bg-border-subtle">
                        @foreach ($pastAgenda as $agenda)
                            <article class="relative pl-16 sm:pl-20">
                                <div class="absolute left-0 top-2 w-14 shrink-0 py-1.5 rounded-[16px] bg-warm-beige border border-border-subtle text-charcoal text-center shadow-subtle">
                                    <span class="block text-xl font-extrabold leading-none">{{ $agenda->event_date->format('d') }}</span>
                                    <span class="block text-[10px] font-bold uppercase tracking-widest mt-0.5 text-muted-charcoal">
                                        {{ strtoupper($agenda->event_date->locale('id')->translatedFormat('M')) }}
                                    </span>
                                </div>
                                <div class="bg-warm-bg rounded-card border border-border-neutral p-5 sm:p-6 hover:border-border-subtle hover:shadow-subtle transition-all">
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-semibold text-muted-charcoal">
                                        <span>{{ $agenda->event_date->locale('id')->translatedFormat('l, d F Y') }}</span>
                                        <span class="px-2.5 py-0.5 rounded-full bg-muted-sage text-charcoal text-[11px] font-bold uppercase tracking-wider border border-border-neutral">
                                            {{ $agenda->category ?: 'Kegiatan' }}
                                        </span>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-bold text-charcoal leading-snug mt-2">{{ $agenda->title }}</h3>
                                    @if ($agenda->description)
                                        <p class="text-sm text-muted-charcoal leading-relaxed mt-1.5 line-clamp-2">{{ Str::limit($agenda->description, 160) }}</p>
                                    @endif
                                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1.5 text-xs font-medium text-muted-charcoal">
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[16px]">schedule</span>
                                            {{ $agenda->event_time ?? 'Waktu menyusul' }}
                                        </span>
                                        <a href="{{ $agenda->maps_link }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1.5 hover:text-nu-deep transition-colors group/loc"
                                           title="Buka lokasi di Google Maps">
                                            <span class="material-symbols-outlined text-[16px]">location_on</span>
                                            <span class="truncate max-w-55 group-hover/loc:underline underline-offset-2">{{ $agenda->location ?? 'Sekretariat PRNU Kecamatan Cilongok' }}</span>
                                            <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="bg-warm-bg rounded-card border border-border-neutral p-10 text-center">
                        <span class="material-symbols-outlined text-muted-charcoal text-4xl mb-2">history</span>
                        <p class="text-sm font-semibold text-charcoal">Belum ada arsip kegiatan.</p>
                    </div>
                @endif
            </div>
        </section>

        <!-- CTA -->
        <section class="max-w-7xl mx-auto px-4 sm:px-8 py-16 sm:py-24 reveal">
            <div class="relative overflow-hidden rounded-container-r bg-warm-beige/70 border border-border-subtle p-8 sm:p-12 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
                <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-muted-gold/10 blur-3xl pointer-events-none"></div>
                <div class="relative max-w-xl">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-muted-sage text-charcoal text-xs font-bold uppercase tracking-wider mb-4 border border-border-neutral">
                        <span class="material-symbols-outlined text-[16px] text-muted-charcoal">support_agent</span>
                        Layanan Sekretariat
                    </span>
                    <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-charcoal tracking-tight mb-3">
                        Ingin Ikut Kegiatan atau Mengusulkan Agenda?
                    </h2>
                    <p class="text-base text-muted-charcoal leading-relaxed">
                        Hubungi pengurus untuk informasi tempat, waktu, dan kebutuhan kepanitiaan. Kami siap membantu jamaah.
                    </p>
                </div>
                <div class="relative shrink-0">
                    <a class="inline-flex items-center justify-center gap-3 px-8 py-4 min-h-[52px] rounded-full bg-nu-deep hover:bg-[#113725] text-white font-bold text-base shadow-subtle transition-all active:scale-95"
                       href="https://wa.me/{{ config('kontak.whatsapp') }}?text={{ urlencode('Assalamualaikum, saya ingin ikut / mengusulkan agenda kegiatan NU Kecamatan Cilongok') }}"
                       rel="noopener noreferrer" target="_blank">
                        <span class="material-symbols-outlined text-[22px]">chat</span>
                        <span>Hubungi Sekretariat</span>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Floating WhatsApp -->
    <div class="fixed bottom-6 right-6 z-40">
        <a aria-label="Hubungi WhatsApp Pengurus"
           class="flex items-center gap-2.5 px-4 py-3 min-h-[44px] rounded-full bg-warm-card/95 border border-border-subtle text-charcoal shadow-subtle hover:shadow-elevated hover:-translate-y-0.5 active:scale-95 transition-all"
           href="https://wa.me/{{ config('kontak.whatsapp') }}" rel="noopener noreferrer" target="_blank">
            <div class="w-8 h-8 rounded-full bg-nu-deep text-white flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[19px]">chat</span>
            </div>
            <span class="hidden sm:inline text-xs font-bold text-charcoal">Sekretariat NU</span>
        </a>
    </div>

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

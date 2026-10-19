<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <x-public-head title="Organisasi &amp; Badan Otonom — MWCNU Cilongok" />

    <style>
        [x-cloak] {
            display: none !important;
        }

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

        @media (prefers-reduced-motion: reduce) {
            .reveal {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }

        .bg-grid-light {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.12) 1px, transparent 0);
            background-size: 22px 22px;
        }

        /* Navigasi logo: geser horizontal halus di mobile */
        .logo-nav {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .logo-nav::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="bg-[#F8F9F6] text-slate-900 antialiased selection:bg-emerald-100 selection:text-emerald-900">
    <x-navbar />

    <main x-data="{
        activeModal: null,
        openModal(slug) {
            this.activeModal = slug;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.activeModal = null;
            document.body.classList.remove('overflow-hidden');
        }
    }" @keydown.escape.window="closeModal()">

        <!-- HERO SECTION (APPLE INSPIRED ELEGAN) -->
        <header class="relative overflow-hidden bg-[#16452F] text-white pt-32 sm:pt-36 pb-16 sm:pb-20">
            <div class="absolute inset-0 bg-grid-light pointer-events-none"></div>
            <div class="absolute -top-20 -right-16 w-96 h-96 rounded-full bg-emerald-400/15 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-28 -left-16 w-80 h-80 rounded-full bg-amber-400/15 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav aria-label="Navigasi balik" class="mb-6 text-sm text-white/75">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a class="hover:text-white hover:underline underline-offset-4 transition-colors" href="{{ route('landing') }}">Beranda</a></li>
                        <li aria-hidden="true">›</li>
                        <li class="text-white font-semibold" aria-current="page">Organisasi</li>
                    </ol>
                </nav>

                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest backdrop-blur-md">
                    <span class="material-symbols-outlined text-[16px] text-amber-300">account_tree</span>
                    <span>Direktori Organisasi &amp; Badan Otonom</span>
                </span>

                <h1 class="mt-6 font-heading text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.15]">
                    Organisasi &amp; Badan Otonom
                </h1>

                <p class="mt-4 text-base sm:text-lg text-white/85 leading-relaxed max-w-2xl font-normal">
                    Kenali jajaran pengurus, badan otonom, lembaga, dan jaringan organisasi Nahdlatul Ulama di wilayah kami secara lengkap dan terpercaya.
                </p>

                <div class="mt-8 inline-flex items-center gap-4 rounded-3xl bg-white/10 border border-white/20 backdrop-blur-md px-6 py-4 shadow-sm">
                    <span class="block text-4xl sm:text-5xl font-extrabold text-white leading-none">{{ $totalSemua }}</span>
                    <span class="block text-xs sm:text-sm font-semibold uppercase tracking-wider text-white/80 leading-snug">Organisasi<br>Terverifikasi</span>
                </div>
            </div>
        </header>

        <!-- NAVIGASI LOGO LINGKARAN (JELAJAHI CEPAT ALA APPLE APP ICONS) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-12">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500">Jelajahi Cepat Banom</p>
                <span class="text-xs font-medium text-slate-400 sm:hidden">Geser ke samping ›</span>
            </div>
            <div class="logo-nav flex gap-3.5 sm:gap-4 overflow-x-auto pb-3 -mx-4 px-4 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-6 lg:grid-cols-11 sm:overflow-visible">
                @foreach ($navigasi as $org)
                    <a href="{{ route('organisasi.show', $org['slug']) }}"
                       class="group shrink-0 flex flex-col items-center gap-2 w-20 sm:w-auto text-center"
                       aria-label="Lihat {{ $org['nama'] }}">
                        <span class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-white border border-slate-200/80 shadow-sm overflow-hidden flex items-center justify-center p-2.5 group-hover:scale-105 group-hover:border-emerald-600 group-hover:shadow-md transition-all duration-200">
                            <img src="{{ asset('images/logo.webp') }}" alt="Logo {{ $org['singkatan'] }}" loading="lazy"
                                 class="w-full h-full object-contain">
                        </span>
                        <span class="text-xs font-bold text-slate-800 group-hover:text-emerald-700 transition-colors line-clamp-1 leading-tight">{{ $org['singkatan'] }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- PENCARIAN & FILTER KATEGORI (APPLE SEGMENTED CONTROL) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-4 sm:p-6 space-y-4">
                <!-- Form Pencarian -->
                <form action="{{ route('organisasi') }}" method="GET" class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[22px]">search</span>
                    <input type="search" name="q" value="{{ $q }}" maxlength="100"
                           placeholder="Cari nama organisasi, misal: Muslimat, Ansor, LAZISNU..."
                           class="min-h-[52px] w-full rounded-2xl border border-slate-200 bg-slate-50/70 pl-12 pr-4 py-3 text-base text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 transition-all" />
                </form>

                <!-- Filter Kategori Segmented Control iOS -->
                <div class="space-y-2">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Pilih Kategori:</p>
                    <div class="bg-slate-100 p-1.5 rounded-2xl border border-slate-200/60 flex flex-wrap gap-1.5" role="tablist" aria-label="Filter kategori">
                        @foreach (config('organisasi.kategori') as $kode => $label)
                            <a href="{{ route('organisasi', array_filter(['kategori' => $kode === 'semua' ? null : $kode, 'q' => $q !== '' ? $q : null])) }}"
                               role="tab" aria-selected="{{ $kategoriAktif === $kode ? 'true' : 'false' }}"
                               class="px-5 py-3 min-h-[48px] inline-flex items-center justify-center rounded-xl text-sm sm:text-base font-bold transition-all active:scale-95
                                      {{ $kategoriAktif === $kode
                                          ? 'bg-white text-emerald-900 shadow-sm border border-slate-200/80'
                                          : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- GRID KARTU ORGANISASI (APPLE CARD DESIGN) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            @if ($daftar->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 reveal">
                    @foreach ($daftar as $org)
                        <article class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 p-6 sm:p-7 flex flex-col justify-between">
                            <div>
                                <!-- Header Kartu: Logo & Kategori Badge -->
                                <div class="flex items-start justify-between gap-3">
                                    <span class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 p-2 overflow-hidden flex items-center justify-center shrink-0 shadow-xs">
                                        <img src="{{ asset('images/logo.webp') }}" alt="Logo {{ $org['singkatan'] }}" loading="lazy"
                                             class="w-full h-full object-contain">
                                    </span>
                                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider border border-slate-200/60">
                                        {{ config('organisasi.kategori')[$org['kategori']] }}
                                    </span>
                                </div>

                                <!-- Nama Organisasi & Singkatan -->
                                <h2 class="mt-4 text-xl font-extrabold text-slate-900 leading-snug group-hover:text-emerald-800 transition-colors">
                                    {{ $org['nama'] }}
                                </h2>
                                <p class="text-sm font-bold text-emerald-700 mt-1 flex items-center gap-1">
                                    <span>{{ $org['singkatan'] }}</span>
                                    @if ($org['terverifikasi'])
                                        <span class="material-symbols-outlined text-[16px] text-emerald-600" title="Terverifikasi">verified</span>
                                    @endif
                                </p>

                                <!-- Deskripsi (Warna text-slate-600 mudah dibaca oleh orang tua) -->
                                <p class="mt-3 text-base text-slate-600 leading-relaxed line-clamp-3">
                                    {{ $org['deskripsi'] }}
                                </p>
                            </div>

                            <!-- Tombol Aksi di Bawah Kartu -->
                            <div class="mt-6 pt-5 border-t border-slate-100 space-y-2.5">
                                <button type="button"
                                        @click="openModal('{{ $org['slug'] }}')"
                                        class="w-full min-h-[48px] px-5 py-3 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-base flex items-center justify-center gap-2 shadow-sm active:scale-95 transition-all">
                                    <span class="material-symbols-outlined text-[20px]">groups</span>
                                    <span>Lihat Pengurus</span>
                                </button>

                                <a href="{{ route('organisasi.show', $org['slug']) }}"
                                   class="w-full min-h-[44px] px-4 py-2.5 rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-sm flex items-center justify-center gap-1.5 border border-slate-200/70 active:scale-95 transition-all">
                                    <span>Profil &amp; Program Kerja</span>
                                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <!-- KONDISI JIKA TIDAK DITEMUKAN PENCARIAN -->
                <div class="bg-white rounded-3xl border border-dashed border-slate-300 p-10 sm:p-14 flex flex-col items-center text-center max-w-xl mx-auto shadow-sm">
                    <span class="material-symbols-outlined text-slate-400 text-5xl mb-3">search_off</span>
                    <h3 class="text-xl font-extrabold text-slate-900">Tidak ada organisasi yang cocok</h3>
                    <p class="text-base text-slate-600 mt-2 leading-relaxed">
                        Coba gunakan kata kunci pencarian yang lain atau pilih kategori yang berbeda.
                    </p>
                    <a href="{{ route('organisasi') }}"
                       class="mt-6 inline-flex items-center gap-2 px-6 py-3 min-h-[48px] rounded-2xl bg-emerald-700 text-white text-base font-bold hover:bg-emerald-800 transition-all active:scale-95 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">refresh</span>
                        <span>Tampilkan Semua Organisasi</span>
                    </a>
                </div>
            @endif
        </section>

        <!-- ================================================================= -->
        <!-- MODAL / POP-UP DETAIL PENGURUS (APPLE iOS BOTTOM SHEET)           -->
        <!-- ================================================================= -->
        @foreach ($daftar as $org)
            @php
                $daftarPengurus = $org['banom'] ? ($semuaPengurus->get($org['banom']) ?? collect()) : collect();

                $ketuaList = $daftarPengurus->filter(function($p) {
                    $j = mb_strtolower(trim($p->jabatan));
                    return str_contains($j, 'ketua') || str_contains($j, 'rais') || str_contains($j, 'pimpinan') || str_contains($j, 'komandan');
                });

                $sekretarisList = $daftarPengurus->filter(function($p) {
                    $j = mb_strtolower(trim($p->jabatan));
                    return str_contains($j, 'sekret') || str_contains($j, 'katib');
                });

                $bendaharaList = $daftarPengurus->filter(function($p) {
                    $j = mb_strtolower(trim($p->jabatan));
                    return str_contains($j, 'bendahara') || str_contains($j, 'bendaraha');
                });

                // Jika ketuaList kosong tapi ada pengurus, jadikan yang pertama sebagai pimpinan
                if ($ketuaList->isEmpty() && $daftarPengurus->isNotEmpty()) {
                    $ketuaList = collect([$daftarPengurus->first()]);
                    $daftarSisa = $daftarPengurus->slice(1);
                } else {
                    $daftarSisa = $daftarPengurus;
                }

                $pengurusLain = $daftarSisa->reject(function($p) use ($ketuaList, $sekretarisList, $bendaharaList) {
                    return $ketuaList->contains('id', $p->id) || $sekretarisList->contains('id', $p->id) || $bendaharaList->contains('id', $p->id);
                });
            @endphp

            <div x-show="activeModal === '{{ $org['slug'] }}'"
                 x-cloak
                 class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="modal-title-{{ $org['slug'] }}">

                <!-- Backdrop Gelap dengan Efek Kaca (Blur) -->
                <div x-show="activeModal === '{{ $org['slug'] }}'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeModal()"
                     class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>

                <!-- Kontainer iOS Bottom Sheet -->
                <div x-show="activeModal === '{{ $org['slug'] }}'"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="translate-y-full sm:translate-y-8 sm:scale-95 opacity-0"
                     x-transition:enter-end="translate-y-0 sm:scale-100 opacity-100"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="translate-y-0 sm:scale-100 opacity-100"
                     x-transition:leave-end="translate-y-full sm:translate-y-8 sm:scale-95 opacity-0"
                     class="relative bg-white w-full sm:max-w-xl max-h-[90vh] rounded-t-[32px] sm:rounded-3xl flex flex-col shadow-2xl overflow-hidden border border-slate-100 z-10">

                    <!-- iOS Drag Handle Bar (Mobile) -->
                    <div class="sm:hidden pt-3 pb-1 flex justify-center">
                        <div class="w-12 h-1.5 bg-slate-300 rounded-full"></div>
                    </div>

                    <!-- Header Modal: Logo, Nama Banom, dan Tombol Tutup (X) yang Jelas -->
                    <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <span class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 p-1.5 shrink-0 flex items-center justify-center">
                                <img src="{{ asset('images/logo.webp') }}" alt="Logo {{ $org['singkatan'] }}" class="w-full h-full object-contain">
                            </span>
                            <div class="min-w-0">
                                <h3 id="modal-title-{{ $org['slug'] }}" class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight truncate">
                                    {{ $org['nama'] }}
                                </h3>
                                <p class="text-xs sm:text-sm font-semibold text-emerald-700">
                                    {{ $org['singkatan'] }} — Susunan Pengurus
                                </p>
                            </div>
                        </div>

                        <!-- Tombol Tutup (X) yang Jelas di Pojok Kanan Atas -->
                        <button type="button"
                                @click="closeModal()"
                                aria-label="Tutup detail pengurus"
                                class="min-w-[48px] min-h-[48px] rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-xl font-bold transition-colors active:scale-95 shrink-0">
                            <span class="material-symbols-outlined text-[24px]">close</span>
                        </button>
                    </div>

                    <!-- Konten Daftar Pengurus (Scrollable) -->
                    <div class="p-5 sm:p-6 overflow-y-auto space-y-5 flex-1">
                        @if ($daftarPengurus->isNotEmpty())
                            <!-- KETUA DI PALING ATAS DENGAN KARTU MENONJOL -->
                            @if ($ketuaList->isNotEmpty())
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-2">Pimpinan Tertinggi</p>
                                    @foreach ($ketuaList as $ketua)
                                        <div class="bg-emerald-50/90 border-2 border-emerald-500/30 rounded-2xl p-4 sm:p-5 flex items-center gap-4 shadow-sm mb-3">
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-emerald-100 border border-emerald-200 shrink-0 flex items-center justify-center">
                                                @if ($ketua->foto)
                                                    <img src="{{ asset('storage/' . $ketua->foto) }}" alt="{{ $ketua->nama }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-2xl font-black text-emerald-800">
                                                        {{ collect(explode(' ', trim($ketua->nama)))->filter()->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('') ?: 'NU' }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <span class="inline-flex px-3 py-1 rounded-full bg-emerald-700 text-white text-xs font-bold uppercase tracking-wider">
                                                    {{ $ketua->jabatan }}
                                                </span>
                                                <h4 class="mt-2 text-lg sm:text-xl font-extrabold text-emerald-950 leading-snug">
                                                    {{ $ketua->nama }}
                                                </h4>
                                                @if ($ketua->label_banom)
                                                    <p class="text-xs font-semibold text-emerald-800/80 mt-0.5">{{ $ketua->label_banom }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- SEKRETARIS & BENDAHARA DI BAWAHNYA -->
                            @if ($sekretarisList->isNotEmpty() || $bendaharaList->isNotEmpty())
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Pengurus Harian</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach ($sekretarisList->concat($bendaharaList) as $inti)
                                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 sm:p-4 flex items-center gap-3.5">
                                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-white border border-slate-200 shrink-0 flex items-center justify-center">
                                                    @if ($inti->foto)
                                                        <img src="{{ asset('storage/' . $inti->foto) }}" alt="{{ $inti->nama }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-sm font-bold text-slate-700">
                                                            {{ collect(explode(' ', trim($inti->nama)))->filter()->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('') ?: 'NU' }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-800 text-xs font-bold">
                                                        {{ $inti->jabatan }}
                                                    </span>
                                                    <h5 class="mt-1 text-base font-bold text-slate-900 leading-snug truncate">
                                                        {{ $inti->nama }}
                                                    </h5>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- JAJARAN LAINNYA / BIDANG -->
                            @if ($pengurusLain->isNotEmpty())
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Jajaran Lainnya ({{ $pengurusLain->count() }})</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                        @foreach ($pengurusLain as $lain)
                                            <div class="bg-slate-50/70 border border-slate-200/80 rounded-xl p-3 flex items-center gap-3">
                                                <span class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-600 flex items-center justify-center shrink-0">
                                                    {{ collect(explode(' ', trim($lain->nama)))->filter()->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('') ?: 'NU' }}
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-bold text-slate-900 truncate">{{ $lain->nama }}</p>
                                                    <p class="text-xs text-slate-500 truncate">{{ $lain->jabatan }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- KONDISI JIKA DATA PENGURUS BELUM DIINPUT DI DATABASE -->
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/80 p-8 text-center my-2">
                                <span class="material-symbols-outlined text-5xl text-slate-400">groups</span>
                                <h4 class="mt-3 text-lg font-bold text-slate-800">Data Pengurus Sedang Dihimpun</h4>
                                <p class="mt-1.5 text-base text-slate-600 leading-relaxed max-w-sm mx-auto">
                                    Susunan pengurus untuk <strong>{{ $org['nama'] }}</strong> sedang dalam tahap pemutakhiran oleh sekretariat.
                                </p>
                                <p class="mt-2 text-sm text-slate-500">
                                    Untuk konfirmasi atau keperluan mendesak, silakan langsung menghubungi sekretariat via WhatsApp di bawah.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- FOOTER MODAL: TOMBOL WHATSAPP SANGAT JELAS, BESAR, DAN MENONJOL -->
                    
                </div>
            </div>
        @endforeach
    </main>

    <x-footer />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const targets = document.querySelectorAll('.reveal');

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

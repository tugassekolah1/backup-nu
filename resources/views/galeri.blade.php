<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <x-public-head title="Galeri Kegiatan - NU Kecamatan Cilongok" />

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
                    <li class="text-white font-semibold" aria-current="page">Galeri</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-end">
                <div class="lg:col-span-7">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest backdrop-blur-sm">
                        <span class="material-symbols-outlined text-[16px] text-muted-gold">photo_library</span>
                        <span>Dokumentasi &amp; Arsip</span>
                    </span>

                    <h1 class="mt-6 font-heading text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.1]">
                        Galeri <span class="text-muted-gold">Kegiatan NU</span>
                    </h1>

                    <p class="mt-5 text-base sm:text-lg text-white/80 leading-relaxed max-w-xl">
                        Dokumentasi rekam jejak kegiatan keagamaan, sosial, dan keorganisasian
                        Nahdlatul Ulama Kecamatan Cilongok.
                    </p>
                </div>

                <div class="lg:col-span-5">
                    <div class="rounded-container-r bg-white/10 border border-white/15 backdrop-blur-md p-6 sm:p-8 shadow-elevated">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <span class="block text-4xl font-extrabold text-white leading-none">{{ $galleries->count() }}</span>
                                <span class="block text-xs font-semibold uppercase tracking-wider text-white/70 mt-2">Foto Terdokumentasi</span>
                            </div>
                            <div>
                                <span class="block text-4xl font-extrabold text-muted-gold leading-none">{{ $galleries->pluck('created_at')->unique('Y-m')->count() }}</span>
                                <span class="block text-xs font-semibold uppercase tracking-wider text-white/70 mt-2">Periode Dokumentasi</span>
                            </div>
                        </div>
                        <div class="mt-6 pt-5 border-t border-white/15 flex items-center gap-2.5 text-xs text-white/75">
                            <span class="material-symbols-outlined text-[18px] text-muted-gold">history_edu</span>
                            <span>Klik foto untuk melihat ukuran penuh.</span>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header> 

    <!-- GALERI GRID SECTION -->
    <section class="relative max-w-7xl mx-auto px-4 sm:px-8 py-16 sm:py-20">
        <div class="reveal mb-8 flex flex-wrap items-end justify-between gap-4 border-b border-border-neutral pb-6">
            <div>
                <h2 class="font-heading text-2xl sm:text-3xl font-extrabold tracking-tight text-nu-deep">
                    {{ $organisasiAktif !== '' ? 'Dokumentasi '.$namaOrganisasiAktif : 'Semua Dokumentasi' }}
                </h2>
                <p class="mt-2 text-sm sm:text-base text-muted-charcoal">
                    Klik salah satu foto untuk melihat ukuran penuh.
                </p>
            </div>

            <span class="inline-flex items-center gap-2 rounded-full border border-border-subtle bg-warm-card px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-muted-charcoal">
                <span class="material-symbols-outlined text-[16px] text-nu-deep">photo_camera</span>
                {{ $galleries->count() }} Foto
            </span>
        </div>

        {{-- Filter per organisasi: hanya organisasi yang punya foto yang ditampilkan. --}}
        @php($organisasiBerfoto = collect($daftarOrganisasi)->filter(fn (array $org) => (int) ($jumlahPerOrganisasi[$org['slug']] ?? 0) > 0))

        @if ($organisasiBerfoto->isNotEmpty())
            <nav aria-label="Filter foto per organisasi" class="reveal mb-10">
                <p class="mb-3 text-xs font-bold uppercase tracking-widest text-muted-charcoal">Filter per Organisasi</p>

                <div class="flex flex-wrap gap-2">
                    @php($kelasAktif = 'bg-nu-deep text-white border-nu-deep shadow-subtle')
                    @php($kelasPasif = 'bg-warm-card text-muted-charcoal border-border-subtle hover:bg-warm-beige hover:text-nu-deep')

                    <a href="{{ route('galeri.index') }}"
                       @class([
                           'inline-flex min-h-[44px] items-center gap-1.5 rounded-full border px-4 py-2 text-sm font-semibold transition',
                           $organisasiAktif === '' ? $kelasAktif : $kelasPasif,
                       ])
                       @if ($organisasiAktif === '') aria-current="page" @endif>
                        <span class="material-symbols-outlined text-[18px]">photo_library</span>
                        Semua
                        <span @class([
                            'rounded-full px-2 py-0.5 text-[11px] font-bold',
                            'bg-white/20' => $organisasiAktif === '',
                            'bg-nu-deep/10 text-nu-deep' => $organisasiAktif !== '',
                        ])>{{ $jumlahSemua }}</span>
                    </a>

                    @foreach ($organisasiBerfoto as $opsi)
                        @php($jumlah = (int) ($jumlahPerOrganisasi[$opsi['slug']] ?? 0))
                        @php($aktif = $organisasiAktif === $opsi['slug'])

                        <a href="{{ route('galeri.index', ['organisasi' => $opsi['slug']]) }}"
                           @class([
                               'inline-flex min-h-[44px] items-center gap-1.5 rounded-full border px-4 py-2 text-sm font-semibold transition',
                               $aktif ? $kelasAktif : $kelasPasif,
                           ])
                           title="{{ $opsi['nama'] }}"
                           @if ($aktif) aria-current="page" @endif>
                            {{ $opsi['singkatan'] }}
                            <span @class([
                                'rounded-full px-2 py-0.5 text-[11px] font-bold',
                                'bg-white/20' => $aktif,
                                'bg-nu-deep/10 text-nu-deep' => ! $aktif,
                            ])>{{ $jumlah }}</span>
                        </a>
                    @endforeach
                </div>
            </nav>
        @endif

        @if ($galleries->isEmpty())
            <div class="reveal rounded-container-r border border-dashed border-border-subtle bg-warm-card px-6 py-20 text-center shadow-subtle">
                <span class="material-symbols-outlined text-6xl text-nu-deep/60">photo_library</span>
                <h3 class="mt-5 font-heading text-xl font-bold text-nu-deep">
                    @if ($organisasiAktif !== '')
                        Belum Ada Foto dari {{ $namaOrganisasiAktif }}
                    @else
                        Belum Ada Foto
                    @endif
                </h3>
                <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-muted-charcoal">
                    @if ($organisasiAktif !== '')
                        Belum ada dokumentasi yang diunggah untuk organisasi ini.
                    @else
                        Dokumentasi kegiatan Nahdlatul Ulama Kecamatan Cilongok akan muncul di sini
                        setelah diunggah oleh pengurus.
                    @endif
                </p>

                @if ($organisasiAktif !== '')
                    <a href="{{ route('galeri.index') }}"
                       class="mt-7 inline-flex items-center gap-2 rounded-btn border border-border-subtle bg-white px-6 py-3 text-sm font-bold text-nu-deep transition hover:bg-warm-beige active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        Lihat Semua Foto
                    </a>
                @else
                    <a href="{{ route('landing') }}"
                       class="mt-7 inline-flex items-center gap-2 rounded-btn bg-nu-deep px-6 py-3 text-sm font-bold text-white transition hover:bg-nu-700 active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        Kembali ke Beranda
                    </a>
                @endif
            </div>
        @else
            <div class="columns-1 gap-5 sm:columns-2 lg:columns-3 xl:columns-4">
                @foreach ($galleries as $gallery)
                    <figure class="reveal group mb-5 break-inside-avoid overflow-hidden rounded-card border border-border-subtle bg-warm-card shadow-subtle transition duration-300 hover:-translate-y-1 hover:shadow-elevated">
                        <button type="button"
                                class="block w-full cursor-zoom-in"
                                data-galeri-modal
                                data-src="{{ $gallery->url }}"
                                data-judul="{{ $gallery->judul }}"
                                data-deskripsi="{{ $gallery->deskripsi }}"
                                data-organisasi="{{ $gallery->organisasiLabel() }}"
                                data-tanggal="{{ $gallery->created_at?->translatedFormat('d F Y') }}"
                                aria-label="Buka foto {{ $gallery->judul }}">
                            <img src="{{ $gallery->url }}"
                                 alt="{{ $gallery->judul }}"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                        </button>

                        <figcaption class="px-4 py-3">
                            @if ($gallery->organisasiLabel() !== '')
                                <span class="mb-2 inline-flex items-center gap-1 rounded-full bg-nu-deep/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-nu-deep">
                                    <span class="material-symbols-outlined text-[13px]">groups</span>
                                    {{ $gallery->organisasiLabel() }}
                                </span>
                            @endif

                            <h3 class="text-sm font-bold text-nu-deep">{{ $gallery->judul }}</h3>
                            @if ($gallery->deskripsi)
                                <p class="mt-1.5 text-xs leading-relaxed text-muted-charcoal">{{ $gallery->deskripsi }}</p>
                            @endif
                            @if ($gallery->created_at)
                                <p class="mt-2 flex items-center gap-1.5 text-[11px] uppercase tracking-wider text-muted-charcoal/80">
                                    <span class="material-symbols-outlined text-[14px] text-nu-600">calendar_today</span>
                                    {{ $gallery->created_at->translatedFormat('d F Y') }}
                                </p>
                            @endif
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        @endif
    </section>

    <x-footer />

    <!-- LIGHTBOX MODAL -->
    <div id="galeri-modal"
         class="fixed inset-0 z-[90] hidden items-center justify-center p-4"
         role="dialog"
         aria-modal="true"
         aria-labelledby="galeri-modal-judul">
        <div id="galeri-modal-backdrop" class="absolute inset-0 bg-charcoal/85 backdrop-blur-sm"></div>

        <div class="relative z-10 w-full max-w-4xl overflow-hidden rounded-container-r border border-white/10 bg-warm-card shadow-elevated">
            <div class="flex items-start justify-between gap-4 border-b border-border-neutral px-5 py-4">
                <div class="min-w-0">
                    <h2 id="galeri-modal-judul" class="truncate font-heading text-base font-bold text-nu-deep sm:text-lg"></h2>
                    <div class="mt-1 flex flex-wrap items-center gap-2">
                        <span id="galeri-modal-organisasi"
                              class="inline-flex items-center gap-1 rounded-full bg-nu-deep/10 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider text-nu-deep">
                            <span class="material-symbols-outlined text-[13px]">groups</span>
                            <span data-organisasi-teks></span>
                        </span>
                        <p id="galeri-modal-tanggal" class="text-xs uppercase tracking-wider text-muted-charcoal"></p>
                    </div>
                </div>

                <button type="button" id="galeri-modal-close"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-border-subtle text-muted-charcoal transition hover:bg-warm-beige hover:text-nu-deep"
                        aria-label="Tutup foto">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="flex items-center justify-center bg-warm-beige/60 px-3 py-3">
                <img id="galeri-modal-img" src="" alt="" class="max-h-[62vh] w-auto rounded-card object-contain">
            </div>

            <div class="px-5 py-4">
                <p id="galeri-modal-deskripsi" class="text-sm leading-relaxed text-muted-charcoal"></p>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-border-neutral pt-4">
                    <div class="flex items-center gap-2">
                        <button type="button" id="galeri-modal-prev"
                                class="inline-flex h-10 items-center gap-1.5 rounded-btn border border-border-subtle px-4 text-xs font-bold text-nu-deep transition hover:bg-warm-beige">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                            Sebelumnya
                        </button>
                        <button type="button" id="galeri-modal-next"
                                class="inline-flex h-10 items-center gap-1.5 rounded-btn border border-border-subtle px-4 text-xs font-bold text-nu-deep transition hover:bg-warm-beige">
                            Berikutnya
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <span id="galeri-modal-count" class="text-xs font-semibold uppercase tracking-wider text-muted-charcoal"></span>
                        <a id="galeri-modal-open" href="#" target="_blank" rel="noopener"
                           class="inline-flex h-10 items-center gap-1.5 rounded-btn bg-nu-deep px-4 text-xs font-bold text-white transition hover:bg-nu-700">
                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                            Buka Foto
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Foto galeri boleh berasal dari URL eksternal (data seeder). Bila gagal
        // dimuat (mis. koneksi putus), tampilkan placeholder agar tidak muncul
        // ikon gambar rusak yang membuat halaman terlihat kosong.
        (function () {
            const PLACEHOLDER = 'data:image/svg+xml;utf8,' + encodeURIComponent(
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300">' +
                '<rect width="400" height="300" fill="#EDE8DD"/>' +
                '<text x="200" y="155" text-anchor="middle" font-family="Inter,sans-serif" font-size="14" fill="#7B8780">Foto tidak tersedia</text>' +
                '</svg>'
            );

            document.addEventListener('error', function (event) {
                const img = event.target;
                if (!(img instanceof HTMLImageElement) || img.dataset.fallback === '1') return;
                img.dataset.fallback = '1';
                img.src = PLACEHOLDER;
            }, true);
        })();
    </script>

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

        // Lightbox galeri: klik foto → modal, dengan navigasi sebelum/berikutnya
        (function () {
            const modal = document.getElementById('galeri-modal');
            if (!modal) return;

            const img = document.getElementById('galeri-modal-img');
            const judul = document.getElementById('galeri-modal-judul');
            const deskripsi = document.getElementById('galeri-modal-deskripsi');
            const tanggal = document.getElementById('galeri-modal-tanggal');
            const organisasi = document.getElementById('galeri-modal-organisasi');
            const count = document.getElementById('galeri-modal-count');
            const openLink = document.getElementById('galeri-modal-open');
            const btnClose = document.getElementById('galeri-modal-close');
            const btnPrev = document.getElementById('galeri-modal-prev');
            const btnNext = document.getElementById('galeri-modal-next');
            const backdrop = document.getElementById('galeri-modal-backdrop');
            const triggers = Array.from(document.querySelectorAll('[data-galeri-modal]'));
            let activeIndex = 0;
            let lastFocused = null;

            function render(index) {
                activeIndex = (index + triggers.length) % triggers.length;
                const el = triggers[activeIndex];
                img.src = el.dataset.src;
                img.alt = el.dataset.judul || 'Foto galeri';
                judul.textContent = el.dataset.judul || '';
                deskripsi.textContent = el.dataset.deskripsi || '';
                deskripsi.style.display = el.dataset.deskripsi ? '' : 'none';
                tanggal.textContent = el.dataset.tanggal || '';

                // Label organisasi: sembunyikan badge bila foto tidak terikat satu banom.
                const namaOrganisasi = el.dataset.organisasi || '';
                organisasi.style.display = namaOrganisasi ? '' : 'none';
                organisasi.querySelector('[data-organisasi-teks]').textContent = namaOrganisasi;

                count.textContent = 'Foto ' + (activeIndex + 1) + ' dari ' + triggers.length;
                openLink.href = el.dataset.src;
                const single = triggers.length < 2;
                btnPrev.style.display = single ? 'none' : '';
                btnNext.style.display = single ? 'none' : '';
            }

            function open(index, trigger) {
                lastFocused = trigger || document.activeElement;
                render(index);
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
                btnClose.focus();
            }

            function close() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
                img.removeAttribute('src');
                if (lastFocused && lastFocused.focus) lastFocused.focus();
            }

            triggers.forEach((el, index) => {
                el.addEventListener('click', () => open(index, el));
            });
            btnClose.addEventListener('click', close);
            backdrop.addEventListener('click', close);
            btnPrev.addEventListener('click', (e) => { e.stopPropagation(); render(activeIndex - 1); });
            btnNext.addEventListener('click', (e) => { e.stopPropagation(); render(activeIndex + 1); });

            document.addEventListener('keydown', (e) => {
                if (modal.classList.contains('hidden')) return;
                if (e.key === 'Escape') close();
                if (e.key === 'ArrowLeft') render(activeIndex - 1);
                if (e.key === 'ArrowRight') render(activeIndex + 1);
            });
        })();
    </script>
</body>
</html>

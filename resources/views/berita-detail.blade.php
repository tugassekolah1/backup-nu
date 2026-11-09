<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <x-public-head title="{{ $berita->judul }} — Berita NU Kecamatan Cilongok" />

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
        .reveal-stagger.revealed > * { opacity: 1; transform: translateY(0); transition-delay: calc(var(--i, 0) * 60ms); }

        @media (prefers-reduced-motion: reduce) {
            .reveal, .reveal-stagger > * {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }

        .bg-grid-light {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.12) 1px, transparent 0);
            background-size: 22px 22px;
        }

        /* Konten artikel: nyaman dibaca (design.md §8 Body) */
        .article-body {
            font-size: 18px;
            line-height: 1.75;
            color: #2b332d;
        }
        .article-body[data-size="sedang"] { font-size: 20px; }
        .article-body[data-size="besar"]  { font-size: 22px; }
        .article-body p { margin-bottom: 1.1em; }
        .article-body p:last-child { margin-bottom: 0; }
        .article-body a { color: #1F5A3F; text-decoration: underline; text-underline-offset: 3px; }
    </style>
</head>

<body class="bg-warm-bg text-charcoal antialiased selection:bg-muted-sage selection:text-nu-deep">
    <x-navbar></x-navbar>

    <main>
        <!-- HEADER ARTIKEL -->
        <header class="relative overflow-hidden bg-nu-deep text-white pt-32 sm:pt-36 pb-14 sm:pb-16">
            <div class="absolute inset-0 bg-grid-light pointer-events-none"></div>
            <div class="absolute -top-20 -right-16 w-96 h-96 rounded-full bg-nu-500/15 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-16 w-72 h-72 rounded-full bg-muted-gold/15 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-8">
                <nav aria-label="Navigasi balik" class="mb-6 text-sm text-white/70">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a class="hover:text-white hover:underline underline-offset-4 transition-colors" href="{{ route('landing') }}">Beranda</a></li>
                        <li aria-hidden="true">›</li>
                        <li><a class="hover:text-white hover:underline underline-offset-4 transition-colors" href="{{ route('berita.public') }}">Berita</a></li>
                        <li aria-hidden="true">›</li>
                        <li class="text-white font-semibold truncate max-w-[40ch]" aria-current="page">{{ Str::limit($berita->judul, 40) }}</li>
                    </ol>
                </nav>

                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest backdrop-blur-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-muted-gold"></span>
                    {{ $berita->jenis ?? 'Berita' }}
                </span>

                <h1 class="mt-5 font-heading text-3xl sm:text-4xl lg:text-[42px] lg:leading-[1.15] font-extrabold tracking-tight">
                    {{ $berita->judul }}
                </h1>

                <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-white/75">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[17px]">account_circle</span>
                        {{ $berita->user->name ?? 'Sekretariat PRNU' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[17px]">event</span>
                        {{ $berita->created_at->locale('id')->translatedFormat('d F Y') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[17px]">visibility</span>
                        {{ number_format($berita->views) }} kali dibaca
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[17px]">schedule</span>
                        {{ $berita->estimasi_baca }}
                    </span>
                </div>
            </div>
        </header>

        <!-- KONTEN ARTIKEL -->
        <article class="max-w-3xl mx-auto px-4 sm:px-8 -mt-8 pb-4 reveal">
            <div class="bg-warm-card rounded-container-r border border-border-neutral shadow-elevated overflow-hidden">
                @if ($berita->gambar_utama)
                    <div class="aspect-[16/10] sm:aspect-[16/9] overflow-hidden bg-warm-beige">
                        <img src="{{ $berita->gambar_utama }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="p-6 sm:p-10">
                    {{-- Kontrol ukuran teks (ramah pengguna yang sulit membaca) --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-5 mb-6 border-b border-border-neutral">
                        <span class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-muted-charcoal">
                            <span class="material-symbols-outlined text-[17px] text-muted-gold">menu_book</span>
                            Isi Berita
                        </span>

                        <div class="flex items-center gap-2" role="group" aria-label="Ukuran teks">
                            <span class="hidden sm:inline text-xs font-semibold text-muted-charcoal mr-1">Ukuran teks:</span>
                            <button type="button" id="teks-kecil" aria-label="Perkecil ukuran teks"
                                    class="min-w-[44px] min-h-[44px] px-3 rounded-full bg-warm-bg border border-border-neutral text-charcoal text-sm font-bold hover:bg-warm-beige/70 transition-colors">A−</button>
                            <button type="button" id="teks-normal" aria-label="Kembalikan ukuran teks normal"
                                    class="min-w-[44px] min-h-[44px] px-3 rounded-full bg-warm-bg border border-border-neutral text-charcoal text-sm font-bold hover:bg-warm-beige/70 transition-colors">A</button>
                            <button type="button" id="teks-besar" aria-label="Perbesar ukuran teks"
                                    class="min-w-[44px] min-h-[44px] px-3 rounded-full bg-warm-bg border border-border-neutral text-charcoal text-sm font-bold hover:bg-warm-beige/70 transition-colors">A+</button>
                        </div>
                    </div>

                    <div class="article-body" id="article-body" data-size="normal">
                        {!! nl2br(e($berita->isi)) !!}
                    </div>

                    {{-- Bagikan --}}
                    <div class="mt-8 pt-6 border-t border-border-neutral">
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-muted-charcoal mr-1">Bagikan:</span>
                            <a class="inline-flex items-center justify-center h-11 w-11 shrink-0 rounded-full bg-nu-deep text-white hover:bg-[#113725] shadow-subtle transition-all active:scale-95"
                               href="https://wa.me/?text={{ urlencode($berita->judul . ' — ' . route('berita.show', $berita->slug)) }}"
                               rel="noopener noreferrer" target="_blank"
                               aria-label="Bagikan ke WhatsApp" title="Bagikan ke WhatsApp">
                                <span class="material-symbols-outlined text-[20px]">chat</span>
                            </a>
                            <button type="button" id="salin-tautan"
                                    class="inline-flex items-center justify-center gap-2 h-11 px-4 sm:px-5 rounded-full bg-warm-bg border border-border-neutral text-charcoal text-sm font-semibold hover:bg-warm-beige/60 transition-colors shrink-0">
                                <span class="material-symbols-outlined text-[18px]">link</span>
                                <span id="salin-label">Salin Tautan</span>
                            </button>
                            <a class="inline-flex items-center justify-center gap-2 h-11 px-4 sm:px-5 rounded-full bg-warm-bg border border-border-neutral text-charcoal text-sm font-semibold hover:bg-warm-beige/60 transition-colors"
                               href="{{ route('berita.public') }}">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                <span>Kembali ke Berita</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <!-- WARTA TERBARU -->
        <section class="max-w-7xl mx-auto px-4 sm:px-8 py-14 sm:py-20 reveal">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-muted-sage text-charcoal text-xs font-semibold uppercase tracking-wider mb-3 border border-border-neutral">
                        <span class="material-symbols-outlined text-[16px] text-muted-charcoal">history</span>
                        Warta Terbaru
                    </span>
                    <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-charcoal tracking-tight">Berita Lainnya</h2>
                    <p class="text-base text-muted-charcoal mt-2">Kabar terkini dari ranting dan lembaga Kecamatan Cilongok.</p>
                </div>
                <a href="{{ route('berita.public') }}"
                   class="w-fit text-sm font-semibold text-nu-deep bg-warm-card border border-border-neutral rounded-full px-4 py-1.5 hover:bg-warm-beige/60 transition-colors">
                    Lihat Semua ›
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 reveal-stagger">
                @forelse ($newsList as $index => $item)
                    <x-news-card :berita="$item" style="--i: {{ $index }}" />
                @empty
                    <div class="col-span-full bg-warm-card rounded-container-r border border-border-neutral shadow-subtle p-10 text-center">
                        <span class="material-symbols-outlined text-muted-charcoal text-4xl mb-2">article</span>
                        <p class="text-sm font-semibold text-charcoal">Belum ada berita lainnya saat ini.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    <x-footer />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            /* --- Entrance animation --- */
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const targets = document.querySelectorAll('.reveal, .reveal-stagger');

            if (reduceMotion || !('IntersectionObserver' in window)) {
                targets.forEach((el) => el.classList.add('revealed'));
            } else {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('revealed');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
                targets.forEach((el) => observer.observe(el));
            }

            /* --- Kontrol ukuran teks (teringat preferensi pengunjung) --- */
            const body = document.getElementById('article-body');
            const levels = ['normal', 'sedang', 'besar'];
            const saved = localStorage.getItem('ukuranTeks');
            if (levels.includes(saved)) body.setAttribute('data-size', saved);

            const setSize = (size) => {
                body.setAttribute('data-size', size);
                localStorage.setItem('ukuranTeks', size);
            };
            const currentIndex = () => Math.max(0, levels.indexOf(body.getAttribute('data-size')));
            document.getElementById('teks-kecil').addEventListener('click', () => setSize(levels[Math.max(0, currentIndex() - 1)]));
            document.getElementById('teks-normal').addEventListener('click', () => setSize('normal'));
            document.getElementById('teks-besar').addEventListener('click', () => setSize(levels[Math.min(levels.length - 1, currentIndex() + 1)]));

            /* --- Salin tautan --- */
            const salinBtn = document.getElementById('salin-tautan');
            const salinLabel = document.getElementById('salin-label');
            salinBtn.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText('{{ route('berita.show', $berita->slug) }}');
                    salinLabel.textContent = 'Tautan tersalin!';
                } catch (e) {
                    salinLabel.textContent = 'Gagal menyalin';
                }
                setTimeout(() => { salinLabel.textContent = 'Salin Tautan'; }, 2000);
            });
        });
    </script>
</body>
</html>

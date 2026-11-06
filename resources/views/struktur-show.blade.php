<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <x-public-head title="{{ $pengurus->nama }} — {{ $pengurus->jabatan }} — NU Banjaranyar" />

    <style>
        .bg-grid-light {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.12) 1px, transparent 0);
            background-size: 22px 22px;
        }
    </style>
</head>

<body class="bg-warm-bg text-charcoal antialiased selection:bg-muted-sage selection:text-nu-deep">
    <x-navbar />

    <main>
        <!-- HERO PENDEK -->
        <header class="relative overflow-hidden bg-nu-deep text-white pt-32 sm:pt-36 pb-14">
            <div class="absolute -top-16 -right-10 w-80 h-80 rounded-full bg-nu-500/15 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-muted-gold/15 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-6xl mx-auto px-5">
                <nav aria-label="Navigasi balik" class="mb-5 text-sm text-white/70">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a class="hover:text-white hover:underline underline-offset-4 transition-colors" href="{{ route('landing') }}">Beranda</a></li>
                        <li aria-hidden="true">›</li>
                        <li><a class="hover:text-white hover:underline underline-offset-4 transition-colors" href="{{ route('organisasi') }}">Organisasi</a></li>
                        <li aria-hidden="true">›</li>
                        <li class="text-white font-semibold" aria-current="page">{{ $pengurus->nama }}</li>
                    </ol>
                </nav>

                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest backdrop-blur-sm">
                    <span class="material-symbols-outlined text-[16px] text-muted-gold">badge</span>
                    <span>Profil Pengurus</span>
                </span>

                <h1 class="mt-4 max-w-3xl text-3xl sm:text-5xl font-extrabold leading-tight tracking-tight">
                    {{ $pengurus->nama }}
                </h1>
                <p class="mt-3 max-w-2xl text-base sm:text-lg leading-relaxed text-white/80">
                    {{ $pengurus->jabatan }} — {{ $pengurus->label_banom ?? 'Pengurus NU Banjaranyar' }}
                </p>
            </div>
        </header>

        <!-- KARTU PROFIL -->
        <section class="max-w-6xl mx-auto px-5 py-12 sm:py-16">
            <div class="bg-warm-card rounded-container-r border border-border-neutral shadow-subtle overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-0">
                    <div class="md:col-span-4 bg-nu-deep p-8 sm:p-10 flex flex-col items-center justify-center text-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-grid-light opacity-50 pointer-events-none"></div>
                        @if ($pengurus->foto)
                            <img src="{{ $pengurus->foto_url }}" alt="Foto {{ $pengurus->nama }}"
                                 class="relative w-44 h-44 sm:w-52 sm:h-52 rounded-full object-cover border-4 border-muted-gold/60 shadow-elevated">
                        @else
                            <span class="relative w-44 h-44 sm:w-52 sm:h-52 rounded-full bg-white/10 border-4 border-white/15 text-white inline-flex items-center justify-center text-5xl font-extrabold">
                                {{ collect(explode(' ', trim($pengurus->nama)))->filter()->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('') }}
                            </span>
                        @endif
                        <span class="relative mt-5 inline-flex items-center px-4 py-1.5 rounded-full bg-muted-gold text-nu-night text-xs font-bold uppercase tracking-wider">
                            {{ $pengurus->jabatan }}
                        </span>
                    </div>

                    <div class="md:col-span-8 p-8 sm:p-10">
                        <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-charcoal tracking-tight">{{ $pengurus->nama }}</h2>

                        <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div class="p-4 rounded-card bg-warm-bg border border-border-neutral">
                                <dt class="text-xs font-bold uppercase tracking-wider text-muted-charcoal">Jabatan</dt>
                                <dd class="mt-1 font-bold text-charcoal">{{ $pengurus->jabatan }}</dd>
                            </div>
                            <div class="p-4 rounded-card bg-warm-bg border border-border-neutral">
                                <dt class="text-xs font-bold uppercase tracking-wider text-muted-charcoal">Organisasi</dt>
                                <dd class="mt-1 font-bold text-charcoal">{{ $pengurus->label_banom ?? '—' }}</dd>
                            </div>
                        </dl>

                        <p class="mt-6 text-sm sm:text-base text-muted-charcoal leading-relaxed">
                            Beliau mengemban amanah sebagai {{ strtolower($pengurus->jabatan) }}
                            pada {{ $pengurus->label_banom ?? 'kepengurusan NU Banjaranyar' }} masa khidmat berjalan,
                            melayani jamaah dan mengoordinasikan kegiatan organisasi.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            @if ($org)
                                <a class="inline-flex items-center gap-2 px-6 py-3 min-h-[48px] rounded-full bg-nu-deep text-white text-sm font-semibold hover:bg-[#113725] transition-all active:scale-95"
                                   href="{{ route('organisasi.show', ['slug' => $org['slug'], 'tab' => 'struktur']) }}">
                                    <span class="material-symbols-outlined text-[18px]">account_tree</span>
                                    <span>Lihat Struktur {{ $org['singkatan'] }}</span>
                                </a>
                                <a class="inline-flex items-center gap-2 px-6 py-3 min-h-[48px] rounded-full bg-warm-bg text-charcoal text-sm font-semibold border border-border-subtle hover:bg-warm-beige/60 transition-all active:scale-95"
                                   href="{{ route('organisasi.show', ['slug' => $org['slug'], 'tab' => 'profil']) }}">
                                    <span class="material-symbols-outlined text-[18px]">groups</span>
                                    <span>Profil {{ $org['singkatan'] }}</span>
                                </a>
                            @else
                                <a class="inline-flex items-center gap-2 px-6 py-3 min-h-[48px] rounded-full bg-nu-deep text-white text-sm font-semibold hover:bg-[#113725] transition-all active:scale-95"
                                   href="{{ route('organisasi') }}">
                                    <span class="material-symbols-outlined text-[18px]">account_tree</span>
                                    <span>Lihat Semua Organisasi</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if ($lainnya->isNotEmpty())
            <!-- PENGURUS LAINNYA -->
            <section class="max-w-6xl mx-auto px-5 pb-16 sm:pb-24">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-3">
                    <div>
                        <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-charcoal tracking-tight">
                            Pengurus {{ $pengurus->label_banom ?? 'Lainnya' }}
                        </h2>
                        <p class="text-sm sm:text-base text-muted-charcoal mt-1">Rekan seperjuangan dalam kepengurusan yang sama.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($lainnya as $p)
                        @include('partials.pengurus-card', ['p' => $p])
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <x-footer />
</body>
</html>

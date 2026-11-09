<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <x-public-head title="Kontak & Alamat Sekretariat — NU Kecamatan Cilongok" />

    <style>
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
                        <li class="text-white font-semibold" aria-current="page">Kontak</li>
                    </ol>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-end">
                    <div class="lg:col-span-7">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest backdrop-blur-sm">
                            <span class="material-symbols-outlined text-[16px] text-muted-gold">support_agent</span>
                            <span>Hubungi Pengurus Ranting</span>
                        </span>

                        <h1 class="mt-6 font-heading text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.1]">
                            Kontak &amp;<br>Alamat Sekretariat
                        </h1>

                        <p class="mt-5 text-base sm:text-lg text-white/80 leading-relaxed max-w-xl">
                            Silakan hubungi sekretariat PRNU Kecamatan Cilongok untuk informasi kegiatan,
                            keanggotaan, dan layanan administrasi lainnya.
                        </p>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="rounded-container-r bg-white/10 border border-white/15 backdrop-blur-md p-6 sm:p-8 shadow-elevated">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full bg-nu-deep text-white flex items-center justify-center shrink-0 border border-white/15">
                                    <span class="material-symbols-outlined text-[22px]">chat</span>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-white/70">WhatsApp Sekretariat</p>
                                    <p class="text-lg font-extrabold text-white tracking-tight">+62 812-3456-7890</p>
                                </div>
                            </div>
                            <a class="mt-5 inline-flex w-full items-center justify-center gap-2 px-6 py-3.5 min-h-[48px] rounded-full bg-white text-nu-deep text-sm font-bold hover:bg-muted-sage transition-all active:scale-95"
                               href="https://wa.me/{{ config('kontak.whatsapp') }}?text={{ urlencode('Assalamualaikum, saya ingin bertanya tentang NU Kecamatan Cilongok') }}"
                               rel="noopener noreferrer" target="_blank">
                                <span class="material-symbols-outlined text-[18px]">chat</span>
                                <span>Chat WhatsApp Sekarang</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- INFO KONTAK -->
        <section class="relative max-w-7xl mx-auto px-4 sm:px-8 py-16 sm:py-24 reveal">
            <span class="absolute left-1/2 -translate-x-1/2 -top-5 z-20 inline-flex items-center gap-1.5 rounded-full bg-warm-card px-4 py-2 text-charcoal text-xs font-bold uppercase tracking-wider border border-border-neutral shadow-elevated">
                <span class="material-symbols-outlined text-[16px] text-muted-charcoal">contact_mail</span>
                <span>Informasi Kontak</span>
            </span>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 reveal-stagger">
                <div class="bg-warm-card rounded-card border border-border-neutral shadow-subtle p-6 hover:-translate-y-1 hover:shadow-elevated transition-all duration-300">
                    <div class="w-11 h-11 rounded-2xl bg-muted-sage text-nu-deep flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[22px]">location_on</span>
                    </div>
                    <h2 class="text-base font-bold text-charcoal">Alamat Sekretariat</h2>
                    <p class="text-sm text-muted-charcoal leading-relaxed mt-2">
                        Gedung Sekretariat PRNU, Jl. Raya Cilongok No. 12, Kecamatan Cilongok, Banyumas, Jawa Tengah 53162
                    </p>
                    <a class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-nu-deep hover:underline underline-offset-2"
                       href="https://www.google.com/maps/search/?api=1&query={{ urlencode('Sekretariat PRNU Kecamatan Cilongok Cilongok Banyumas') }}"
                       rel="noopener noreferrer" target="_blank">
                        <span>Buka di Google Maps</span>
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </a>
                </div>

                <div class="bg-warm-card rounded-card border border-border-neutral shadow-subtle p-6 hover:-translate-y-1 hover:shadow-elevated transition-all duration-300">
                    <div class="w-11 h-11 rounded-2xl bg-muted-sage text-nu-deep flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[22px]">call</span>
                    </div>
                    <h2 class="text-base font-bold text-charcoal">Telepon / WhatsApp</h2>
                    <p class="text-sm text-muted-charcoal leading-relaxed mt-2">
                        +62 812-3456-7890<br>Respon tercepat melalui WhatsApp pada jam khidmat.
                    </p>
                    <a class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-nu-deep hover:underline underline-offset-2"
                       href="https://wa.me/{{ config('kontak.whatsapp') }}"
                       rel="noopener noreferrer" target="_blank">
                        <span>Chat Sekarang</span>
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </a>
                </div>

                <div class="bg-warm-card rounded-card border border-border-neutral shadow-subtle p-6 hover:-translate-y-1 hover:shadow-elevated transition-all duration-300">
                    <div class="w-11 h-11 rounded-2xl bg-muted-sage text-nu-deep flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[22px]">mail</span>
                    </div>
                    <h2 class="text-base font-bold text-charcoal">Email</h2>
                    <p class="text-sm text-muted-charcoal leading-relaxed mt-2">
                        sekretariat@nukecamatancilongok.or.id<br>Untuk surat resmi dan kerja sama kelembagaan.
                    </p>
                    <a class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-nu-deep hover:underline underline-offset-2"
                       href="mailto:sekretariat@nukecamatancilongok.or.id">
                        <span>Kirim Email</span>
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </a>
                </div>

                <div class="bg-warm-card rounded-card border border-border-neutral shadow-subtle p-6 hover:-translate-y-1 hover:shadow-elevated transition-all duration-300">
                    <div class="w-11 h-11 rounded-2xl bg-muted-sage text-nu-deep flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[22px]">schedule</span>
                    </div>
                    <h2 class="text-base font-bold text-charcoal">Jam Khidmat</h2>
                    <p class="text-sm text-muted-charcoal leading-relaxed mt-2">
                        Setiap Hari Ahad &amp; Rabu<br>Pukul 08.00 – 16.00 WIB
                    </p>
                    <a class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-nu-deep hover:underline underline-offset-2"
                       href="{{ route('agenda.public') }}">
                        <span>Lihat Jadwal Kegiatan</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- PETA -->
        <section class="relative w-full bg-warm-card border-y border-border-neutral py-16 sm:py-24 reveal">
            <span class="absolute left-1/2 -translate-x-1/2 -top-5 z-20 inline-flex items-center gap-1.5 rounded-full bg-warm-card px-4 py-2 text-charcoal text-xs font-bold uppercase tracking-wider border border-border-neutral shadow-elevated">
                <span class="material-symbols-outlined text-[16px] text-muted-charcoal">map</span>
                <span>Lokasi Sekretariat</span>
            </span>
            <div class="max-w-7xl mx-auto px-4 sm:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                    <div class="lg:col-span-7 overflow-hidden rounded-container-r border border-border-neutral shadow-subtle">
                        <iframe title="Peta lokasi Sekretariat PRNU Kecamatan Cilongok"
                                src="https://maps.google.com/maps?q={{ urlencode('Kecamatan Cilongok, Banyumas') }}&z=14&output=embed"
                                class="w-full h-80 sm:h-96" style="border:0" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    </div>
                    <div class="lg:col-span-5 flex flex-col justify-center">
                        <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-charcoal tracking-tight">Datang Langsung ke Sekretariat</h2>
                        <p class="mt-3 text-base text-muted-charcoal leading-relaxed">
                            Pengurus menyambut setiap warga yang ingin bersilaturahmi, mendaftar anggota,
                            atau mengurus keperluan administrasi. Sebaiknya hubungi kami terlebih dahulu
                            agar ada pengurus yang mendampingi.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a class="inline-flex items-center gap-2 px-6 py-3 min-h-[48px] rounded-full bg-nu-deep text-white text-sm font-semibold hover:bg-[#113725] transition-all active:scale-95"
                               href="https://www.google.com/maps/search/?api=1&query={{ urlencode('Sekretariat PRNU Kecamatan Cilongok Cilongok Banyumas') }}"
                               rel="noopener noreferrer" target="_blank">
                                <span class="material-symbols-outlined text-[18px]">directions</span>
                                <span>Petunjuk Arah</span>
                            </a>
                            <a class="inline-flex items-center gap-2 px-6 py-3 min-h-[48px] rounded-full bg-warm-bg text-charcoal text-sm font-semibold border border-border-subtle hover:bg-warm-beige/60 transition-all active:scale-95"
                               href="{{ route('aspirasi.index') }}">
                                <span class="material-symbols-outlined text-[18px]">chat_bubble</span>
                                <span>Kirim Aspirasi</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="max-w-7xl mx-auto px-4 sm:px-8 py-16 sm:py-24 reveal">
            <div class="relative overflow-hidden rounded-container-r bg-warm-beige/70 border border-border-subtle p-8 sm:p-12 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
                <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-muted-gold/10 blur-3xl pointer-events-none"></div>
                <div class="relative max-w-xl">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-muted-sage text-charcoal text-xs font-bold uppercase tracking-wider mb-4 border border-border-neutral">
                        <span class="material-symbols-outlined text-[16px] text-muted-charcoal">how_to_reg</span>
                        Bergabung Bersama Kami
                    </span>
                    <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-charcoal tracking-tight mb-3">
                        Belum Menjadi Anggota? Daftar Sekarang
                    </h2>
                    <p class="text-base text-muted-charcoal leading-relaxed">
                        Pendaftaran anggota terbuka untuk seluruh warga. Isi formulir daring atau datang langsung ke sekretariat.
                    </p>
                </div>
                <div class="relative shrink-0">
                    <a class="inline-flex items-center justify-center gap-3 px-8 py-4 min-h-[52px] rounded-full bg-nu-deep hover:bg-[#113725] text-white font-bold text-base shadow-subtle transition-all active:scale-95"
                       href="{{ route('members.register-form') }}">
                        <span class="material-symbols-outlined text-[22px]">how_to_reg</span>
                        <span>Daftar Anggota</span>
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

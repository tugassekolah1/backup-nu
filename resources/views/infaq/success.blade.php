<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <x-public-head title="Infak Berhasil — NU Kecamatan Cilongok" />
</head>

<body class="bg-[#F5F7F4] text-[#17392D] antialiased selection:bg-[#E7ECE4] selection:text-[#16452F]">
    <x-navbar />

    <main>
        <!-- HERO PENDEK -->
        <header class="relative overflow-hidden bg-[#16452F] text-white pt-32 sm:pt-36 pb-12">
            <div class="absolute -top-16 -right-10 w-80 h-80 rounded-full bg-[#8bc14b]/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-6xl mx-auto px-5">
                <nav aria-label="Navigasi balik" class="mb-5 text-sm text-white/70">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a class="hover:text-white hover:underline underline-offset-4" href="{{ route('landing') }}">Beranda</a></li>
                        <li aria-hidden="true">›</li>
                        <li><a class="hover:text-white hover:underline underline-offset-4" href="{{ route('infaq.index') }}">Infaq</a></li>
                        <li aria-hidden="true">›</li>
                        <li class="text-white font-semibold" aria-current="page">Berhasil</li>
                    </ol>
                </nav>

                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest">
                    Pembayaran Diterima
                </span>

                <h1 class="mt-4 max-w-3xl text-3xl sm:text-5xl font-extrabold leading-tight tracking-tight">
                    Alhamdulillah
                </h1>
                <p class="mt-3 max-w-2xl text-base sm:text-lg leading-relaxed text-white/80">
                    Infak Anda telah kami terima. Terima kasih telah menyisihkan sebagian rezeki untuk warga.
                </p>
            </div>
        </header>

        <section class="mx-auto max-w-2xl px-5 py-12">
            <div class="rounded-[28px] border border-[#DCE4DE] bg-white p-6 text-center shadow-[0_8px_24px_rgba(23,24,22,0.05)] sm:p-8">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-[#1F5A3F]/20 bg-[#E8F2EA]">
                    <span class="material-symbols-outlined text-3xl text-[#1F5A3F]">check_circle</span>
                </div>

                <h2 class="mt-4 text-2xl font-bold text-[#17392D]">Terima Kasih, {{ $infaq->nama_donatur }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-[#526158]">
                    Semoga menjadi amal jariyah yang bermanfaat bagi seluruh warga Nahdlatul Ulama.
                </p>

                <dl class="mt-7 space-y-3 rounded-2xl border border-[#DCE4DE] bg-[#F5F7F4] p-5 text-left text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-[#7B8780]">Kode Transaksi</dt>
                        <dd class="font-mono font-bold text-[#17392D]">{{ $infaq->kode_transaksi }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-[#7B8780]">Nominal</dt>
                        <dd class="font-bold text-[#1F5A3F]">Rp {{ number_format($infaq->nominal, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-[#7B8780]">Waktu Bayar</dt>
                        <dd class="font-semibold text-[#17392D]">{{ $infaq->paid_at?->format('d M Y, H:i') }} WIB</dd>
                    </div>
                </dl>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('landing') }}" class="min-h-[44px] flex-1 rounded-[14px] bg-[#1F5A3F] px-6 py-3 text-center text-sm font-semibold text-white transition hover:bg-[#16452F] active:scale-[0.98]">
                        Kembali ke Beranda
                    </a>
                    <a href="{{ route('infaq.index') }}" class="min-h-[44px] flex-1 rounded-[14px] border border-[#DCE4DE] bg-white px-6 py-3 text-center text-sm font-semibold text-[#16452F] transition hover:bg-[#E8F2EA] active:scale-[0.98]">
                        Infak Lagi
                    </a>
                </div>
            </div>
        </section>
    </main>

    <x-footer />
</body>
</html>

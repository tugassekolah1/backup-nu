<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <x-public-head title="Selesaikan Pembayaran — NU Banjaranyar" />
</head>

<body class="bg-[#F5F7F4] text-[#17392D] antialiased selection:bg-[#E7ECE4] selection:text-[#16452F]">
    <x-navbar />

    <main>
        <!-- HERO PENDEK -->
        <header class="relative overflow-hidden bg-[#16452F] text-white pt-32 sm:pt-36 pb-12">
            <div class="absolute -top-16 -right-10 w-80 h-80 rounded-full bg-[#C99A2E]/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-6xl mx-auto px-5">
                <nav aria-label="Navigasi balik" class="mb-5 text-sm text-white/70">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a class="hover:text-white hover:underline underline-offset-4" href="{{ route('landing') }}">Beranda</a></li>
                        <li aria-hidden="true">›</li>
                        <li><a class="hover:text-white hover:underline underline-offset-4" href="{{ route('infaq.index') }}">Infaq</a></li>
                        <li aria-hidden="true">›</li>
                        <li class="text-white font-semibold" aria-current="page">Pembayaran</li>
                    </ol>
                </nav>

                <span class="inline-flex items-center gap-2 rounded-full border border-[#C99A2E]/40 bg-[#C99A2E]/20 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-[#F3DFAE]">
                    Menunggu Pembayaran
                </span>

                <h1 class="mt-4 max-w-3xl text-3xl sm:text-5xl font-extrabold leading-tight tracking-tight">
                    Instruksi Pembayaran
                </h1>
                <p class="mt-3 max-w-2xl text-base sm:text-lg leading-relaxed text-white/80">
                    Selesaikan pembayaran sesuai metode yang Anda pilih, lalu simpan kode transaksi berikut.
                </p>
            </div>
        </header>

        <section class="mx-auto max-w-2xl px-5 py-12">
            <div class="rounded-[28px] border border-[#DCE4DE] bg-white p-6 shadow-[0_8px_24px_rgba(23,24,22,0.05)] sm:p-8">
                <div class="text-center">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#7B8780]">Total Pembayaran</p>
                    <p class="mt-1 text-3xl font-extrabold text-[#1F5A3F] sm:text-4xl">
                        Rp {{ number_format($infaq->nominal, 0, ',', '.') }}
                    </p>
                    <p class="mt-3 inline-flex rounded-full border border-[#DCE4DE] bg-[#F5F7F4] px-4 py-1.5 font-mono text-xs font-bold text-[#526158]">
                        Kode: {{ $infaq->kode_transaksi }}
                    </p>
                </div>

                <dl class="mt-7 space-y-3 border-y border-[#DCE4DE] py-5 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-[#7B8780]">Donatur</dt>
                        <dd class="text-right font-semibold text-[#17392D]">{{ $infaq->nama_donatur }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-[#7B8780]">Metode</dt>
                        <dd class="text-right font-semibold uppercase text-[#17392D]">{{ str_replace('_', ' ', $infaq->metode_pembayaran) }}</dd>
                    </div>
                    @if ($infaq->catatan)
                        <div class="flex items-start justify-between gap-3">
                            <dt class="shrink-0 text-[#7B8780]">Pesan</dt>
                            <dd class="text-right text-[#526158]">{{ $infaq->catatan }}</dd>
                        </div>
                    @endif
                </dl>

                @if (session('success'))
                    <div role="status" class="mt-6 flex items-start gap-3 rounded-2xl border border-[#8bc14b]/40 bg-[#8bc14b]/15 p-4 text-sm text-[#17392D]">
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div role="alert" class="mt-6 rounded-2xl border border-rose-300 bg-rose-50 p-4 text-sm font-semibold text-rose-700">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div role="alert" class="mt-6 rounded-2xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-700">
                        <ul class="list-disc list-inside space-y-1 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($infaq->status === 'pending' && ! $infaq->bukti_path)
                    {{-- Instruksi bayar sesuai metode --}}
                    @if ($infaq->metode_pembayaran === 'qris')
                        <div class="mt-6 rounded-2xl border border-[#DCE4DE] bg-[#F5F7F4] p-6 text-center">
                            <p class="text-xs font-bold uppercase tracking-widest text-[#7B8780]">Scan QRIS Berikut</p>
                            <div class="mx-auto mt-4 w-fit rounded-2xl border border-[#DCE4DE] bg-white p-4 shadow-sm">
                                {!! $qrCode !!}
                            </div>
                            <p class="mt-3 font-mono text-xs font-bold text-[#526158]">DUMMY-QRIS • {{ $infaq->kode_transaksi }}</p>
                            <p class="mt-1 text-xs text-[#7B8780]">QR contoh untuk simulasi — bayar sesuai nominal di atas.</p>
                        </div>
                    @elseif ($infaq->metode_pembayaran === 'transfer_bank')
                        <div class="mt-6 rounded-2xl border border-[#DCE4DE] bg-[#F5F7F4] p-6 text-center">
                            <p class="text-xs font-bold uppercase tracking-widest text-[#7B8780]">Transfer ke Rekening Berikut</p>
                            <p class="mt-3 font-mono text-2xl font-extrabold tracking-wider text-[#17392D]">8888 0101 2345 6789</p>
                            <p class="mt-1 text-sm font-semibold text-[#526158]">Bank Contoh — a.n. LAZISNU Banjaranyar (DUMMY)</p>
                            <p class="mt-1 text-xs text-[#7B8780]">Cantumkan kode <span class="font-mono font-bold">{{ $infaq->kode_transaksi }}</span> pada berita transfer.</p>
                        </div>
                    @else
                        <div class="mt-6 rounded-2xl border border-[#DCE4DE] bg-[#F5F7F4] p-6 text-center">
                            <p class="text-xs font-bold uppercase tracking-widest text-[#7B8780]">Pembayaran Tunai</p>
                            <p class="mt-3 text-sm leading-relaxed text-[#526158]">
                                Serahkan uang tunai langsung ke sekretariat atau pengurus
                                pada jam khidmat (Ahad &amp; Rabu, 08.00–16.00 WIB),
                                lalu kirim bukti/foto serah terima di bawah ini.
                            </p>
                        </div>
                    @endif

                    {{-- Konfirmasi + kirim bukti --}}
                    <form action="{{ route('infaq.proof', $infaq->kode_transaksi) }}" method="POST" enctype="multipart/form-data" class="mt-6 rounded-2xl border border-[#1F5A3F]/25 bg-[#E8F2EA]/60 p-5">
                        @csrf
                        <label for="bukti" class="block text-sm font-bold text-[#17392D]">
                            Sudah bayar? Kirim bukti pembayaran <span class="text-rose-600">*</span>
                        </label>
                        <input id="bukti" name="bukti" type="file" accept="image/jpeg,image/png" required
                               class="mt-2 block w-full text-sm text-[#526158] file:mr-3 file:rounded-full file:border-0 file:bg-white file:px-4 file:py-2 file:text-xs file:font-bold file:text-[#1F5A3F] file:shadow-sm hover:file:bg-[#F5F7F4]" />
                        <p class="mt-1.5 text-xs text-[#7B8780]">Foto/struk transfer, JPG/PNG maksimal 2 MB.</p>
                        <button type="submit" class="mt-4 min-h-[44px] w-full rounded-[14px] bg-[#1F5A3F] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#16452F] active:scale-[0.98]">
                            Saya Sudah Bayar &amp; Kirim Bukti
                        </button>
                    </form>

                    @if (app()->environment('local'))
                        <div class="mt-6 rounded-2xl border border-[#C99A2E]/30 bg-[#FBF4E4] p-5 text-center">
                            <p class="text-sm leading-relaxed text-[#526158]">
                                <strong class="font-bold text-[#17392D]">Mode sandbox:</strong>
                                lewati verifikasi admin untuk simulasi instan.
                            </p>

                            <form action="{{ route('infaq.simulate', $infaq->kode_transaksi) }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit" class="min-h-[44px] w-full rounded-[14px] border border-[#C99A2E]/50 bg-white px-6 py-3 text-sm font-semibold text-[#7B8780] transition hover:bg-[#FBF4E4] active:scale-[0.98]">
                                    Simulasikan Pembayaran Lunas
                                </button>
                            </form>
                        </div>
                    @endif
                @elseif ($infaq->status === 'pending' && $infaq->bukti_path)
                    {{-- Menunggu verifikasi admin --}}
                    <div class="mt-6 rounded-2xl border border-amber-300 bg-amber-50 p-6 text-center">
                        <p class="text-base font-extrabold text-[#17392D]">Bukti Terkirim — Menunggu Verifikasi Admin</p>
                        <p class="mt-1.5 text-sm leading-relaxed text-[#526158]">
                            Terima kasih! Pengurus akan memeriksa bukti Anda.
                            Status otomatis berubah <strong>lunas</strong> setelah disetujui.
                        </p>
                        <a href="{{ $infaq->bukti_url }}" target="_blank" rel="noopener"
                           class="mx-auto mt-4 block w-fit overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm">
                            <img src="{{ $infaq->bukti_url }}" alt="Bukti pembayaran {{ $infaq->kode_transaksi }}" class="max-h-56 w-auto object-contain" />
                        </a>
                        <p class="mt-2 text-xs text-[#7B8780]">Klik gambar untuk memperbesar. Simpan kode transaksi Anda untuk pengecekan.</p>
                    </div>
                @endif

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('infaq.index') }}" class="min-h-[44px] flex-1 rounded-[14px] border border-[#DCE4DE] bg-white px-6 py-3 text-center text-sm font-semibold text-[#16452F] transition hover:bg-[#E8F2EA] active:scale-[0.98]">
                        Kembali ke Formulir
                    </a>
                    <a href="{{ route('landing') }}" class="min-h-[44px] flex-1 rounded-[14px] px-6 py-3 text-center text-sm font-semibold text-[#526158] transition hover:bg-[#E8F2EA] hover:text-[#16452F] active:scale-[0.98]">
                        Ke Beranda
                    </a>
                </div>
            </div>
        </section>
    </main>

    <x-footer />
</body>
</html>

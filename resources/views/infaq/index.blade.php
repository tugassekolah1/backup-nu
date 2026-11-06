<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <x-public-head title="Infak & Sedekah — NU Banjaranyar" />
</head>

<body class="bg-[#F5F7F4] text-[#17392D] antialiased selection:bg-[#E7ECE4] selection:text-[#16452F]">
    <x-navbar />

    <main>
        <!-- HERO -->
        <header class="relative overflow-hidden bg-[#16452F] text-white pt-32 sm:pt-36 pb-14">
            <div class="absolute -top-16 -right-10 w-80 h-80 rounded-full bg-[#8bc14b]/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-[#C99A2E]/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-6xl mx-auto px-5">
                <nav aria-label="Navigasi balik" class="mb-5 text-sm text-white/70">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a class="hover:text-white hover:underline underline-offset-4" href="{{ route('landing') }}">Beranda</a></li>
                        <li aria-hidden="true">›</li>
                        <li class="text-white font-semibold" aria-current="page">Infaq</li>
                    </ol>
                </nav>

                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest">
                    LAZISNU UPZIS
                </span>

                <h1 class="mt-4 max-w-3xl text-3xl sm:text-5xl font-extrabold leading-tight tracking-tight">
                    Infak &amp; Sedekah
                </h1>
                <p class="mt-3 max-w-2xl text-base sm:text-lg leading-relaxed text-white/80">
                    Titipkan infak Anda untuk menopang kegiatan keagamaan, sosial, dan kemaslahatan warga di Desa Banjaranyar.
                </p>

                <dl class="mt-7 flex flex-wrap gap-3">
                    <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-3 backdrop-blur-sm">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-white/60">Total Masuk</dt>
                        <dd class="text-2xl font-extrabold">Rp {{ number_format($totalMasuk ?? $totalInfaq, 0, ',', '.') }}</dd>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-3 backdrop-blur-sm">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-white/60">Total Disalurkan</dt>
                        <dd class="text-2xl font-extrabold">Rp {{ number_format($totalKeluar ?? 0, 0, ',', '.') }}</dd>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-3 backdrop-blur-sm">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-white/60">Saldo Kas</dt>
                        <dd class="text-2xl font-extrabold">Rp {{ number_format($saldo ?? $totalInfaq, 0, ',', '.') }}</dd>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-3 backdrop-blur-sm">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-white/60">Donasi Tercatat</dt>
                        <dd class="text-2xl font-extrabold">{{ $totalDonatur }} transaksi</dd>
                    </div>
                </dl>
            </div>
        </header>

        <section class="mx-auto grid max-w-6xl gap-8 px-5 py-12 lg:grid-cols-12">
            <!-- FORM INFAK -->
            <div class="lg:col-span-7">
                <div class="rounded-[28px] border border-[#DCE4DE] bg-white p-6 sm:p-8 shadow-[0_8px_24px_rgba(23,24,22,0.05)]">
                    <h2 class="text-xl font-bold text-[#17392D] sm:text-2xl">Formulir Infak</h2>
                    <p class="mt-1 text-sm leading-relaxed text-[#526158]">
                        Isi data di bawah ini, lalu lanjutkan ke halaman konfirmasi pembayaran.
                    </p>

                    @if ($errors->any())
                        <div role="alert" class="mt-5 rounded-2xl border border-[#F0C4C4] bg-[#FDECEC] p-4 text-sm text-[#B42318]">
                            <p class="font-bold">Terjadi kesalahan:</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('infaq.store') }}" method="POST" class="mt-6 space-y-5">
                        @csrf

                        <div>
                            <label for="nominal" class="mb-1.5 block text-sm font-semibold text-[#17392D]">Nominal Infak (Rp)</label>
                            <input
                                id="nominal"
                                name="nominal"
                                type="number"
                                min="1000"
                                step="1000"
                                value="{{ old('nominal') }}"
                                placeholder="Contoh: 50000"
                                required
                                class="min-h-[44px] w-full rounded-[14px] border border-[#DCE4DE] bg-white px-4 py-2.5 text-[#17392D] placeholder:text-[#7B8780] focus:border-[#1F5A3F] focus:outline-none focus:ring-2 focus:ring-[#1F5A3F]/20"
                            >
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ([10000, 50000, 100000] as $preset)
                                    <button
                                        type="button"
                                        data-nominal="{{ $preset }}"
                                        class="min-h-[36px] rounded-full border border-[#DCE4DE] bg-[#F5F7F4] px-4 text-xs font-bold text-[#526158] transition hover:border-[#1F5A3F] hover:bg-[#E8F2EA] hover:text-[#16452F]"
                                    >
                                        Rp {{ number_format($preset, 0, ',', '.') }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label for="nama_donatur" class="mb-1.5 block text-sm font-semibold text-[#17392D]">Nama Donatur</label>
                            <input
                                id="nama_donatur"
                                name="nama_donatur"
                                type="text"
                                value="{{ old('nama_donatur') }}"
                                placeholder="Kosongkan untuk “Hamba Allah”"
                                maxlength="100"
                                class="min-h-[44px] w-full rounded-[14px] border border-[#DCE4DE] bg-white px-4 py-2.5 text-[#17392D] placeholder:text-[#7B8780] focus:border-[#1F5A3F] focus:outline-none focus:ring-2 focus:ring-[#1F5A3F]/20"
                            >
                        </div>

                        <div>
                            <label for="metode_pembayaran" class="mb-1.5 block text-sm font-semibold text-[#17392D]">Metode Pembayaran</label>
                            <select
                                id="metode_pembayaran"
                                name="metode_pembayaran"
                                required
                                class="min-h-[44px] w-full rounded-[14px] border border-[#DCE4DE] bg-white px-4 py-2.5 text-[#17392D] focus:border-[#1F5A3F] focus:outline-none focus:ring-2 focus:ring-[#1F5A3F]/20"
                            >
                                <option value="qris" @selected(old('metode_pembayaran') === 'qris')>QRIS (Scan Barcode)</option>
                                <option value="transfer_bank" @selected(old('metode_pembayaran') === 'transfer_bank')>Transfer Bank (BSI)</option>
                                <option value="tunai" @selected(old('metode_pembayaran') === 'tunai')>Tunai / Bayar di Tempat</option>
                            </select>
                        </div>

                        <div>
                            <label for="catatan" class="mb-1.5 block text-sm font-semibold text-[#17392D]">Pesan / Doa (Opsional)</label>
                            <textarea
                                id="catatan"
                                name="catatan"
                                rows="3"
                                placeholder="Semoga berkah..."
                                class="w-full rounded-[14px] border border-[#DCE4DE] bg-white px-4 py-2.5 text-[#17392D] placeholder:text-[#7B8780] focus:border-[#1F5A3F] focus:outline-none focus:ring-2 focus:ring-[#1F5A3F]/20"
                            >{{ old('catatan') }}</textarea>
                        </div>

                        <button type="submit" class="min-h-[44px] w-full rounded-[14px] bg-[#1F5A3F] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#16452F] active:scale-[0.98]">
                            Lanjutkan Infak
                        </button>

                        <p class="text-center text-xs leading-relaxed text-[#7B8780]">
                            Setelah menekan tombol, Anda akan diarahkan ke halaman instruksi pembayaran.
                        </p>
                    </form>
                </div>
            </div>

            <!-- INFORMASI PENGGUNAAN & DONATUR TERBARU -->
            <aside class="space-y-6 lg:col-span-5">
                <div class="rounded-[28px] border border-[#DCE4DE] bg-white p-6">
                    <h2 class="text-lg font-bold text-[#17392D]">Untuk Apa Infak Ini?</h2>
                    <ul class="mt-4 space-y-3 text-sm leading-relaxed text-[#526158]">
                        <li class="flex gap-3">
                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-[#C99A2E]"></span>
                            Operasional majelis taklim dan pengajian rutin warga.
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-[#C99A2E]"></span>
                            Santunan anak yatim dan bantuan warga yang membutuhkan.
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-[#C99A2E]"></span>
                            Kegiatan dakwah, kaderisasi, dan penguatan badan otonom NU.
                        </li>
                    </ul>

                    <div class="mt-6 rounded-2xl border border-[#DCE4DE] bg-[#F5F7F4] p-4 text-sm text-[#526158]">
                        Butuh konfirmasi? Hubungi sekretariat melalui
                        <a class="font-semibold text-[#1F5A3F] underline underline-offset-4 hover:text-[#16452F]" href="https://wa.me/{{ config('kontak.whatsapp') }}" rel="noopener noreferrer" target="_blank">WhatsApp pengurus</a>.
                    </div>
                </div>

                <div class="rounded-[28px] border border-[#DCE4DE] bg-white p-6">
                    <div class="flex items-end justify-between gap-3 border-b border-[#DCE4DE] pb-3">
                        <h2 class="text-lg font-bold text-[#17392D]">Donatur Terbaru</h2>
                        <span class="shrink-0 rounded-full border border-[#DCE4DE] bg-white px-3 py-1 text-xs font-bold text-[#1F5A3F]">
                            Transparan
                        </span>
                    </div>

                    @if ($infaqTerbaru->isNotEmpty())
                        <ul class="divide-y divide-[#DCE4DE]">
                            @foreach ($infaqTerbaru as $donasi)
                                <li class="flex items-center justify-between gap-3 py-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-[#17392D]">{{ $donasi->nama_donatur }}</p>
                                        <p class="text-xs text-[#7B8780]">
                                            {{ $donasi->paid_at?->isoFormat('D MMM Y') ?? '-' }}
                                            · {{ ucwords(str_replace('_', ' ', $donasi->metode_pembayaran)) }}
                                        </p>
                                    </div>
                                    <span class="shrink-0 text-sm font-bold text-[#1F5A3F]">
                                        Rp {{ number_format($donasi->nominal, 0, ',', '.') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="py-6 text-center text-sm text-[#7B8780]">
                            Belum ada infak yang tercatat. Jadilah yang pertama.
                        </p>
                    @endif
                </div>
            </aside>
        </section>

        <!-- TRANSPARANSI KAS KELUAR -->
        <section class="mx-auto max-w-6xl px-5 pb-12">
            <div class="rounded-[28px] border border-[#DCE4DE] bg-white p-6 sm:p-8 shadow-[0_8px_24px_rgba(23,24,22,0.05)]">
                <div class="flex flex-wrap items-end justify-between gap-3 border-b border-[#DCE4DE] pb-4">
                    <div>
                        <h2 class="text-xl font-bold text-[#17392D] sm:text-2xl">Penyaluran & Kas Keluar</h2>
                        <p class="mt-1 text-sm leading-relaxed text-[#526158]">
                            Untuk apa saja dana infak dipakai. Diperbarui oleh pengurus setiap ada penyaluran.
                        </p>
                    </div>
                    <span class="shrink-0 rounded-full border border-[#DCE4DE] bg-[#F5F7F4] px-3 py-1 text-xs font-bold text-[#1F5A3F]">
                        Total disalurkan: Rp {{ number_format($totalKeluar ?? 0, 0, ',', '.') }}
                    </span>
                </div>

                @if (($kasKeluar ?? collect())->isNotEmpty())
                    <ul class="divide-y divide-[#DCE4DE]">
                        @foreach ($kasKeluar as $keluar)
                            <li class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex min-w-0 items-start gap-3">
                                    @if ($keluar->bukti_path)
                                        <a href="{{ $keluar->bukti_url }}" target="_blank" rel="noopener" class="shrink-0">
                                            <img src="{{ $keluar->bukti_url }}" alt="Bukti {{ $keluar->catatan }}" class="h-14 w-14 rounded-xl border border-[#DCE4DE] object-cover" />
                                        </a>
                                    @else
                                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border border-[#DCE4DE] bg-[#F5F7F4] text-lg font-bold text-[#C99A2E]">Rp</span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-[#17392D]">
                                            {{ $keluar->catatan ?: ucwords(str_replace('_', ' ', $keluar->kategori ?? 'Penyaluran')) }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-[#7B8780]">
                                            {{ $keluar->paid_at?->isoFormat('D MMMM Y') ?? $keluar->created_at->isoFormat('D MMMM Y') }}
                                            @if ($keluar->kategori)
                                                · {{ ucwords(str_replace('_', ' ', $keluar->kategori)) }}
                                            @endif
                                            @if ($keluar->penanggung_jawab)
                                                · PJ: {{ $keluar->penanggung_jawab }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex shrink-0 items-center gap-3 pl-[68px] sm:pl-0">
                                    <span class="text-sm font-bold text-[#B42318]">
                                        − Rp {{ number_format($keluar->nominal, 0, ',', '.') }}
                                    </span>
                                    @if ($keluar->bukti_path)
                                        <a href="{{ $keluar->bukti_url }}" target="_blank" rel="noopener" class="rounded-full border border-[#DCE4DE] px-3 py-1.5 text-xs font-bold text-[#1F5A3F] hover:bg-[#E8F2EA]">
                                            Bukti
                                        </a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="py-8 text-center text-sm text-[#7B8780]">
                        Belum ada penyaluran yang dicatat. Data penyaluran akan tampil di sini agar transparan.
                    </p>
                @endif
            </div>
        </section>
    </main>

    <x-footer />

    <script>
        document.addEventListener('click', function (event) {
            const button = event.target.closest('[data-nominal]');
            if (!button) return;

            const input = document.getElementById('nominal');
            if (!input) return;

            input.value = button.getAttribute('data-nominal');
            input.focus();
        });
    </script>
</body>
</html>

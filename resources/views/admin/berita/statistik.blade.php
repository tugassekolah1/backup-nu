<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('berita.index') }}"
                   class="p-2 bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 rounded-xl transition-colors"
                   title="Kembali ke Manajemen Berita">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="p-2.5 bg-teal-50 text-teal-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Statistik Pembaca Berita
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Grafik jumlah pembaca berita yang diterbitkan.</p>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $paletJenis = [
            'Pengumuman' => '#8b5cf6',
            'Kegiatan' => '#0d9488',
            'Artikel' => '#f59e0b',
            'Berita' => '#6366f1',
        ];
        $adaPembaca = array_sum($dataHarian) > 0;
    @endphp

    <div class="py-8 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ============ KARTU RINGKASAN ============ --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex items-center gap-3.5">
                    <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-black text-slate-900 leading-none">{{ number_format($stats['total']) }}</p>
                        <p class="text-xs font-semibold text-slate-500 mt-1.5">Total Berita</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex items-center gap-3.5">
                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-black text-slate-900 leading-none">{{ number_format($stats['publish']) }}</p>
                        <p class="text-xs font-semibold text-slate-500 mt-1.5">Terbit · <span class="text-slate-400">{{ $stats['draft'] }} draft</span></p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex items-center gap-3.5">
                    <div class="p-2.5 bg-amber-50 text-amber-600 rounded-xl shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-black text-slate-900 leading-none">{{ number_format($stats['views']) }}</p>
                        <p class="text-xs font-semibold text-slate-500 mt-1.5">Total Dibaca</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex items-center gap-3.5">
                    <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 01-.982-3.172m9.985 0a8.933 8.933 0 00-6.264-6.264M6 11.078a8.933 8.933 0 006.264-6.264M15.75 6.814a44.27 44.27 0 00-3.61 0"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-black text-slate-900 leading-none">{{ number_format($stats['rataViews']) }}</p>
                        <p class="text-xs font-semibold text-slate-500 mt-1.5">Rata-rata / Berita</p>
                    </div>
                </div>
            </div>

            {{-- ============ GRAFIK 1: TREN 30 HARI ============ --}}
            @php
                $totalPeriode = (int) array_sum($dataHarian);
                $jumlahHari = count($dataHarian);
                $rataHarian = $jumlahHari > 0 ? (int) round($totalPeriode / $jumlahHari) : 0;
                $hariAktif = count(array_filter($dataHarian, fn ($v) => (int) $v > 0));
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-teal-50 text-teal-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21V7a2 2 0 012-2h4a2 2 0 012 2v14m-8 0h8m-8 0H5a2 2 0 01-2-2v-4a2 2 0 012-2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Tren Pembaca 30 Hari Terakhir</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                @if ($adaPembaca)
                                    Jumlah pembaca yang tercatat tiap hari pada {{ $hariAktif }} hari aktif terakhir.
                                @else
                                    Data akan terisi otomatis setelah halaman berita dibaca.
                                @endif
                            </p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-teal-700 bg-teal-50 rounded-lg px-3 py-1.5">30 hari</span>
                </div>

                {{-- Ringkasan angka: dibaca sekilas tanpa harus menghitung dari garis. --}}
                @if ($adaPembaca)
                    <div class="grid grid-cols-2 sm:grid-cols-4 divide-slate-100 border-b border-slate-100 sm:divide-x">
                        <div class="px-5 py-4">
                            <p class="text-xl font-black text-slate-900 leading-none">{{ number_format($totalPeriode) }}</p>
                            <p class="text-[11px] font-semibold text-slate-500 mt-1.5 uppercase tracking-wide">Total Periode</p>
                        </div>
                        <div class="px-5 py-4">
                            <p class="text-xl font-black text-slate-900 leading-none">{{ number_format($rataHarian) }}</p>
                            <p class="text-[11px] font-semibold text-slate-500 mt-1.5 uppercase tracking-wide">Rata-rata / Hari</p>
                        </div>
                        <div class="px-5 py-4">
                            <p class="text-xl font-black text-slate-900 leading-none">{{ number_format($puncakHarian) }}</p>
                            <p class="text-[11px] font-semibold text-slate-500 mt-1.5 uppercase tracking-wide">Puncak Harian</p>
                        </div>
                        <div class="px-5 py-4">
                            <p class="text-xl font-black text-slate-900 leading-none">{{ $hariAktif }}<span class="text-sm font-bold text-slate-400">/{{ $jumlahHari }}</span></p>
                            <p class="text-[11px] font-semibold text-slate-500 mt-1.5 uppercase tracking-wide">Hari Terisi</p>
                        </div>
                    </div>
                @endif

                <div class="p-5">
                    @if ($adaPembaca)
                        <x-chart-area :labels="$labelHarian" :values="$dataHarian" color="#0d9488" :labelEvery="5"/>
                        <p class="mt-3 flex items-center gap-4 text-[11px] font-semibold text-slate-500">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="h-0.5 w-4 rounded-full bg-teal-600"></span>
                                Pembaca harian
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="h-0.5 w-4 rounded-full border-t-2 border-dashed border-teal-500"></span>
                                Rata-rata {{ number_format($rataHarian) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-teal-600"></span>
                                Puncak {{ number_format($puncakHarian) }}
                            </span>
                        </p>
                    @else
                        <div class="py-12 flex flex-col items-center text-center">
                            <div class="p-4 bg-slate-100 text-slate-400 rounded-full">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21V7a2 2 0 012-2h4a2 2 0 012 2v14m-8 0h8m-8 0H5a2 2 0 01-2-2v-4a2 2 0 012-2"/>
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-bold text-slate-700">Belum ada pembaca tercatat</p>
                            <p class="mt-1 text-xs text-slate-500 max-w-sm">
                                Grafik tren mulai terisi begitu pengunjung membuka halaman detail berita.
                                Buka salah satu berita terbit untuk mencobanya.
                            </p>
                            <button type="button" onclick="window.location.reload()"
                                    class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-teal-700 hover:text-teal-800 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Muat ulang grafik
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- ============ GRAFIK 2: 10 BERITA TERPOPULER ============ --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center gap-3">
                        <div class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">10 Berita Terpopuler</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Peringkat jumlah pembaca sepanjang waktu.</p>
                        </div>
                    </div>
                    <div class="p-5">
                        @if ($terpopuler->isEmpty())
                            <p class="py-8 text-center text-sm text-slate-500">Belum ada berita terbit.</p>
                        @else
                            <x-chart-bars color="#f59e0b" :items="$terpopuler
                                ->map(fn ($b) => [
                                    'label' => $b->judul,
                                    'value' => (int) $b->views,
                                    'meta' => $b->jenis . ' · terbit ' . $b->created_at->format('d M Y'),
                                ])
                                ->all()"/>
                        @endif
                    </div>
                </div>

                {{-- ============ GRAFIK 3: KOMPOSISI PER KANAL ============ --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center gap-3">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Pembaca per Kanal</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Sebaran jumlah pembaca menurut jenis berita.</p>
                        </div>
                    </div>
                    <div class="p-5">
                        @if ($perJenis->isEmpty() || $perJenis->sum('value') === 0)
                            <p class="py-8 text-center text-sm text-slate-500">Belum ada data pembaca.</p>
                        @else
                            <x-chart-donut :items="$perJenis
                                ->map(fn ($row) => [
                                    'label' => $row['label'],
                                    'value' => $row['value'],
                                    'color' => $paletJenis[$row['label']] ?? '#64748b',
                                ])
                                ->all()"/>

                            <ul role="list" class="mt-5 space-y-2">
                                @foreach ($perJenis as $row)
                                    <li class="flex items-center justify-between gap-3 text-sm">
                                        <span class="flex min-w-0 items-center gap-2">
                                            <span class="h-2.5 w-2.5 shrink-0 rounded-full"
                                                  style="background-color: {{ $paletJenis[$row['label']] ?? '#64748b' }}"></span>
                                            <span class="truncate font-semibold text-slate-700">{{ $row['label'] }}</span>
                                        </span>
                                        <span class="shrink-0 font-bold text-slate-900">
                                            {{ number_format($row['value']) }}
                                            <span class="text-xs font-semibold text-slate-400">
                                                · {{ $row['berita'] }} berita
                                            </span>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- ============ GRAFIK 4: PEMBACA PER BULAN ============ --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center gap-3">
                        <div class="p-2 bg-violet-50 text-violet-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 012-2h2a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Pembaca per Bulan Terbit</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Total pembaca dari berita yang terbit tiap bulan (6 bulan).</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <x-chart-columns color="#8b5cf6" :items="collect($labelBulan)
                            ->map(fn ($label, $i) => [
                                'label' => $label,
                                'value' => (int) ($dataViewsBulan[$i] ?? 0),
                            ])
                            ->all()"/>
                    </div>
                </div>

                {{-- ============ GRAFIK 5: BERITA TERBIT PER BULAN ============ --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center gap-3">
                        <div class="p-2 bg-sky-50 text-sky-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Berita Terbit per Bulan</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Jumlah berita yang diterbitkan tiap bulan (6 bulan).</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <x-chart-columns color="#0ea5e9" :items="collect($labelBulan)
                            ->map(fn ($label, $i) => [
                                'label' => $label,
                                'value' => (int) ($dataTerbit[$i] ?? 0),
                            ])
                            ->all()"/>
                    </div>
                </div>
            </div>

            {{-- Catatan kaki --}}
            <p class="text-xs text-slate-400 text-center">
                Data pembaca dicatat setiap kunjungan halaman detail berita.
                Halaman yang di-refresh berulang tetap terhitung sebagai pembaca baru.
            </p>
        </div>
    </div>
</x-app-layout>

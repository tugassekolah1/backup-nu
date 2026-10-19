@props([
    'labels' => [],
    'values' => [],
    'color' => '#0d9488',
    'labelEvery' => 5,
    'height' => 240,
])

@php
    // --- Geometri grafik ---
    $jumlah = max(count($values), 2);
    $lebar = 760;
    $padAtas = 22;
    $padBawah = 30;
    $padKiri = 52;
    $padKanan = 16;
    $tinggiGrafik = $height - $padAtas - $padBawah;
    $lebarGrafik = $lebar - $padKiri - $padKanan;

    // Bulatkan batas atas ke angka "rapi" agar garis bantu tidak aneh.
    $mentah = max($values ?: [0]) ?: 1;
    $eksponen = 10 ** floor(log10((float) $mentah));
    $batas = (int) $eksponen;
    foreach ([1, 1.5, 2, 2.5, 4, 5, 7.5, 10] as $kelipatan) {
        if ((float) $mentah <= $kelipatan * $eksponen) {
            $batas = (int) round($kelipatan * $eksponen);
            break;
        }
    }

    $stepX = $lebarGrafik / ($jumlah - 1);
    $titik = [];
    foreach ($values as $i => $nilai) {
        $titik[] = [
            'x' => round($padKiri + $stepX * $i, 2),
            'y' => round($padAtas + (1 - ((float) $nilai / $batas)) * $tinggiGrafik, 2),
            'nilai' => (int) $nilai,
            'label' => $labels[$i] ?? '',
        ];
    }

    $polyline = implode(' ', array_map(
        fn (array $t) => $t['x'].','.$t['y'],
        $titik
    ));
    $dasar = round($padAtas + $tinggiGrafik, 2);
    $bentuk = $titik[0]['x'].','.$dasar.' '.$polyline
        .' '.$titik[count($titik) - 1]['x'].','.$dasar;

    $garisBantu = [];
    for ($g = 0; $g <= 3; $g++) {
        $rasio = $g / 3;
        $garisBantu[] = [
            'y' => round($padAtas + (1 - $rasio) * $tinggiGrafik, 2),
            'nilai' => (int) round($batas * $rasio),
        ];
    }

    $uid = 'tren-'.substr(md5($lebar.$height.$color.(string) count($values)), 0, 8);
    $puncak = max($values ?: [0]);
    $rata = count($values) > 0 ? (int) round(array_sum($values) / count($values)) : 0;

    // Titik puncak ditandai supaya mata langsung tahu hari tersibuk.
    $iPuncak = 0;
    foreach ($values as $i => $nilai) {
        if ((int) $nilai === (int) $puncak) {
            $iPuncak = $i;
            break;
        }
    }
    $titikPuncak = $titik[$iPuncak] ?? null;

    // Nilai di sumbu Y diformat agar angka besar tetap enak dibaca.
    $formatSumbu = fn (int $n): string => number_format($n);
@endphp

<svg viewBox="0 0 {{ $lebar }} {{ $height }}" role="img"
     preserveAspectRatio="xMidYMid meet"
     {{ $attributes->merge(['class' => 'w-full h-auto overflow-visible']) }}>
    <title>Grafik pembaca harian selama {{ count($values) }} hari.
        Rata-rata {{ $formatSumbu($rata) }} pembaca per hari, puncak {{ $formatSumbu((int) $puncak) }} pembaca.</title>

    <defs>
        <linearGradient id="{{ $uid }}-isi" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="{{ $color }}" stop-opacity="0.28"/>
            <stop offset="100%" stop-color="{{ $color }}" stop-opacity="0.02"/>
        </linearGradient>

        <style>
            /* Garis utama "tergambar" sekali saat halaman dibuka, lalu berhenti.
               Dihormati prefers-reduced-motion: pengguna yang meminta gerak
               minimal langsung melihat garis utuh tanpa animasi. */
            #{{ $uid }}-garis {
                stroke-dasharray: 2400;
                stroke-dashoffset: 2400;
                animation: {{ $uid }}-gambar 1.1s ease-out .1s forwards;
            }
            @keyframes {{ $uid }}-gambar { to { stroke-dashoffset: 0; } }

            #{{ $uid }}-titik { transition: r .15s ease, opacity .15s ease; }
            #{{ $uid }}-titik:hover { r: 5.5; }

            @media (prefers-reduced-motion: reduce) {
                #{{ $uid }}-garis { animation: none; stroke-dashoffset: 0; }
                #{{ $uid }}-titik { transition: none; }
            }
        </style>
    </defs>

    @foreach ($garisBantu as $garis)
        <line x1="{{ $padKiri }}" x2="{{ $lebar - $padKanan }}"
              y1="{{ $garis['y'] }}" y2="{{ $garis['y'] }}"
              stroke="#eef2f6" stroke-width="1"/>
        <text x="{{ $padKiri - 10 }}" y="{{ $garis['y'] + 3.5 }}" text-anchor="end"
              class="fill-slate-400" style="font-size: 10px; font-weight: 600">
            {{ $formatSumbu($garis['nilai']) }}
        </text>
    @endforeach

    <polygon points="{{ $bentuk }}" fill="url(#{{ $uid }}-isi)"/>

    {{-- Garis rata-rata: patokan cepat apakah hari ini di atas atau di bawah biasa. --}}
    @if ($rata > 0 && $rata < $batas)
        @php($yRata = round($padAtas + (1 - ($rata / $batas)) * $tinggiGrafik, 2))
        <line x1="{{ $padKiri }}" x2="{{ $lebar - $padKanan }}"
              y1="{{ $yRata }}" y2="{{ $yRata }}"
              stroke="{{ $color }}" stroke-width="1.25"
              stroke-dasharray="5 5" opacity="0.55"/>
        <text x="{{ $lebar - $padKanan }}" y="{{ $yRata - 6 }}" text-anchor="end"
              style="font-size: 10px; font-weight: 700" fill="{{ $color }}" opacity="0.85">
            rata-rata {{ $formatSumbu($rata) }}
        </text>
    @endif

    <polyline id="{{ $uid }}-garis" points="{{ $polyline }}" fill="none" stroke="{{ $color }}"
              stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>

    {{-- Garis dasar (nilai 0) sedikit lebih tegas agar terlihat sebagai pijakan. --}}
    <line x1="{{ $padKiri }}" x2="{{ $lebar - $padKanan }}"
          y1="{{ $dasar }}" y2="{{ $dasar }}" stroke="#cbd5e1" stroke-width="1.5"/>

    @foreach ($titik as $t)
        <circle id="{{ $uid }}-titik" cx="{{ $t['x'] }}" cy="{{ $t['y'] }}" r="3"
                fill="#ffffff" stroke="{{ $color }}" stroke-width="1.75" opacity="0.9">
            <title>{{ $t['label'] }} — {{ $formatSumbu($t['nilai']) }} pembaca</title>
        </circle>
    @endforeach

    {{-- Puncak periode ditonjolkan, lengkap dengan angkanya. --}}
    @if ($titikPuncak !== null && (int) $puncak > 0)
        <circle cx="{{ $titikPuncak['x'] }}" cy="{{ $titikPuncak['y'] }}" r="6"
                fill="{{ $color }}"/>
        <circle cx="{{ $titikPuncak['x'] }}" cy="{{ $titikPuncak['y'] }}" r="10"
                fill="none" stroke="{{ $color }}" stroke-width="1.5" opacity="0.35">
            <title>Puncak: {{ $titikPuncak['label'] }} — {{ $formatSumbu((int) $puncak) }} pembaca</title>
        </circle>
        <text x="{{ $titikPuncak['x'] }}"
              y="{{ max(12, $titikPuncak['y'] - 16) }}"
              text-anchor="middle"
              style="font-size: 11px; font-weight: 800" fill="{{ $color }}">
            {{ $formatSumbu((int) $puncak) }}
        </text>
    @endif

    @foreach ($titik as $i => $t)
        @if ($i % $labelEvery === 0 || $i === count($titik) - 1)
            <text x="{{ $t['x'] }}" y="{{ $height - 9 }}" text-anchor="middle"
                  class="fill-slate-400" style="font-size: 10px; font-weight: 600">
                {{ $t['label'] }}
            </text>
        @endif
    @endforeach
</svg>

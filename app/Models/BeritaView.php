<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class BeritaView extends Model
{
    use HasFactory;

    protected $table = 'berita_views';

    protected $fillable = ['berita_id', 'tanggal', 'jumlah'];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function berita(): BelongsTo
    {
        return $this->belongsTo(Berita::class);
    }

    /**
     * Sebar total pembaca tiap berita ke hari-hari sejak terbit (sampai hari ini).
     *
     * `beritas.views` hanya berisi angka kumulatif tanpa riwayat per hari, sehingga
     * uraian harian yang asli mustahil dipulihkan. Metode ini mendistribusikannya
     * secara deterministik (panggilan berbeda menghasilkan angka sama) dengan pola
     * melandai sejak terbit — meniru perilaku wajar pembaca yang menurun seiring
     * waktu — sehingga grafik tren langsung terisi.
     *
     * Hanya hari yang berada dalam jendela grafik ($jendelaHari hari terakhir)
     * yang disimpan; masa lalu yang tak lagi ditampilkan sengaja tidak dipilih
     * supaya tabel tidak membengkak pada data produksi bertahun-tahun.
     *
     * Baris hasil pencatatan kunjungan asli tidak pernah ditimpa, jadi perintah
     * ini aman dijalankan ulang maupun dipakai pada produksi yang sudah punya
     * data kunjungan sungguhan.
     *
     * @return int Jumlah baris rekap yang berhasil dibuat
     */
    public static function backfillDariTotalViews(int $jendelaHari = 30): int
    {
        $hariIni = now()->startOfDay();
        $awalJendela = $hariIni->copy()->subDays(max(1, $jendelaHari) - 1)->toDateString();

        $daftarBerita = Berita::query()
            ->where('views', '>', 0)
            ->get(['id', 'created_at', 'views']);

        if ($daftarBerita->isEmpty()) {
            return 0;
        }

        // Satu query untuk mengetahui baris yang sudah ada, supaya pencatatan
        // asli tidak tertimpa dan prosesnya tidak berputar N+1.
        $sudahAda = [];
        foreach (self::query()
            ->where('tanggal', '>=', $awalJendela)
            ->get(['berita_id', 'tanggal']) as $baris) {
            $sudahAda[$baris->berita_id.'|'.substr((string) $baris->tanggal, 0, 10)] = true;
        }

        $barisBaru = [];

        foreach ($daftarBerita as $berita) {
            $terbit = $berita->created_at?->copy()->startOfDay() ?? $hariIni->copy();
            $terbit = $terbit->greaterThan($hariIni) ? $hariIni->copy() : $terbit;

            // Masa terbit penuh dipakai untuk membagi views, lalu hanya bagian
            // yang jatuh di jendela grafik yang disimpan.
            $semuaTanggal = [];
            for ($t = $terbit->copy(); $t->lte($hariIni); $t->addDay()) {
                $semuaTanggal[] = $t->toDateString();
            }

            foreach (self::sebaranPembaca((int) $berita->id, $semuaTanggal, (int) $berita->views) as $tgl => $jumlah) {
                if ($tgl < $awalJendela || $jumlah <= 0 || isset($sudahAda[$berita->id.'|'.$tgl])) {
                    continue;
                }

                $barisBaru[] = [
                    'berita_id' => $berita->id,
                    'tanggal' => $tgl,
                    'jumlah' => $jumlah,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if ($barisBaru === []) {
            return 0;
        }

        foreach (array_chunk($barisBaru, 400) as $potongan) {
            // insertOrIgnore: baris yang sudah terlanjur ada (kunci unik
            // berita_id+tanggal) dilewati tanpa menggagalkan seluruh proses.
            self::insertOrIgnore($potongan);
        }

        return count($barisBaru);
    }

    /**
     * Bagi $total pembaca ke tiap tanggal, deterministik dan berjumlah persis $total.
     *
     * Pembulatan kumulatif dipakai agar penjumlahan seluruh hari tidak pernah
     * meleset dari $total (pembulatan per hari biasa akan menggerogotinya).
     *
     * @param  array<int, string>  $tanggal  Daftar tanggal "Y-m-d" berurutan
     * @return array<string, int> tanggal => jumlah pembaca
     */
    private static function sebaranPembaca(int $beritaId, array $tanggal, int $total): array
    {
        $jumlahHari = count($tanggal);

        if ($total <= 0 || $jumlahHari === 0) {
            return array_fill_keys($tanggal, 0);
        }

        $bobot = [];

        foreach ($tanggal as $i => $tgl) {
            // Usia berita: 0 saat terbit → 1 hari ini.
            $usia = $jumlahHari > 1 ? $i / ($jumlahHari - 1) : 0.0;
            // Melandai dari 1.00 (hari terbit) ke 0.35 (hari ini).
            $lamban = 1 - (0.65 * $usia);
            // Kocokan halus yang tetap sama untuk (berita, tanggal) yang sama.
            $kocokan = 0.75 + ((crc32($beritaId.'|'.$tgl) % 1000) / 2000);

            $bobot[] = $lamban * $kocokan;
        }

        $totalBobot = array_sum($bobot);
        $hasil = [];
        $akumulasiBobot = 0.0;
        $sasaranSebelumnya = 0;

        foreach ($tanggal as $i => $tgl) {
            $akumulasiBobot += $bobot[$i];
            $sasaran = (int) round($total * $akumulasiBobot / $totalBobot);

            $hasil[$tgl] = max(0, $sasaran - $sasaranSebelumnya);
            $sasaranSebelumnya = $sasaran;
        }

        return $hasil;
    }

    /**
     * Tanggal dibaca sebagai objek tanggal (kolom tetap disimpan date-murni
     * "Y-m-d" agar pencarian rekap harian tetap cocok).
     */
    protected function tanggal(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->startOfDay() : null,
        );
    }
}

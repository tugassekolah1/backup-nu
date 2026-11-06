<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pengurus extends Model
{
    use HasFactory;

    // Arahkan Eloquent ke tabel penguruses
    protected $table = 'penguruses';

    /**
     * Daftar badan otonom / organisasi tingkat kecamatan.
     * Kode (nilai dropdown) => ['label' => label_banom, 'name' => teks tampilan].
     */
    public const BANOMS = [
        'mwcnu' => ['label' => 'MWCNU', 'name' => 'MWCNU (Majelis Wakil Cabang NU)'],
        'pac_ipnu' => ['label' => 'PAC IPNU', 'name' => 'PAC IPNU (Ikatan Pelajar NU)'],
        'pac_ippnu' => ['label' => 'PAC IPPNU', 'name' => 'PAC IPPNU (Ikatan Pelajar Putri NU)'],
        'pac_fatayat' => ['label' => 'PAC Fatayat NU', 'name' => 'PAC Fatayat NU'],
        'satkoryon_banser' => ['label' => 'Satkoryon Banser', 'name' => 'Satkoryon Banser'],
        'pac_ansor' => ['label' => 'PAC GP Ansor', 'name' => 'PAC GP Ansor'],
        'pac_muslimat' => ['label' => 'PAC Muslimat NU', 'name' => 'PAC Muslimat NU'],
        'pac_pargu' => ['label' => 'PAC Pergunu', 'name' => 'PAC Pergunu (Persatuan Guru NU)'],
        'pac_pencak_silat' => ['label' => 'PAC Pagar Nusa', 'name' => 'PAC Pagar Nusa'],
        'cbp_ipnu' => ['label' => 'CBP IPNU', 'name' => 'CBP (Corps Brigade Pembangunan)'],
        'kpp_ippnu' => ['label' => 'KPP IPPNU', 'name' => 'KPP (Korps Pelajar Putri)'],
        'pac_isnu' => ['label' => 'PAC ISNU', 'name' => 'PAC ISNU (Ikatan Sarjana NU)'],
        'pac_jqhnu' => ['label' => 'PAC JQHNU', 'name' => "PAC JQHNU (Jam'iyyatul Qurra wal Huffazh)"],
        'pac_jatman' => ['label' => 'PAC Jatman NU', 'name' => 'PAC Jatman (Jam\'iyyah Ahli Thariqah)'],
        'pac_ishari' => ['label' => 'PAC Ishari NU', 'name' => 'PAC Ishari NU (Ikatan Seni Hadhrah)'],
        'mwclazisnu' => ['label' => 'UPZIS LAZISNU', 'name' => 'UPZIS LAZISNU Kecamatan'],
    ];

    /**
     * Pilihan jabatan inti yang tersedia untuk semua banom.
     *
     * @var array<int, string>
     */
    public const JABATAN_INTI = [
        'Ketua',
        'Wakil Ketua',
        'Sekretaris',
        'Bendahara',
    ];

    /**
     * Pilihan jabatan bidang khas tiap banom.
     * Banom yang tidak tercantum memakai JABATAN_BIDANG_UMUM.
     *
     * @var array<string, array<int, string>>
     */
    public const JABATAN_BIDANG = [
        'mwcnu' => [
            'Wakil Ketua Bidang Dakwah',
            'Wakil Ketua Bidang Pendidikan',
            'Wakil Ketua Bidang Sosial',
            'Ketua Bidang Dakwah dan Ukhuwah',
            'Ketua Bidang Pendidikan dan Kaderisasi',
            'Ketua Bidang Sosial dan Kesejahteraan',
            'Ketua Bidang Ekonomi dan Usaha',
        ],
        'pac_ansor' => [
            'Ketua Bidang Perkaderanan',
            'Ketua Bidang Kepemudaan',
            'Ketua Bidang Sosial dan Kemanusiaan',
            'Ketua Bidang Komunikasi dan Informasi',
        ],
        'mwclazisnu' => [
            'Kepala Bidang Pengumpulan',
            'Kepala Bidang Penyaluran',
            'Kepala Bidang Pemberdayaan Umat',
        ],
    ];

    /**
     * Pilihan jabatan bidang generik untuk banom tanpa daftar khusus.
     *
     * @var array<int, string>
     */
    public const JABATAN_BIDANG_UMUM = [
        'Ketua Bidang Dakwah',
        'Ketua Bidang Pendidikan',
        'Ketua Bidang Sosial',
        'Ketua Bidang Ekonomi',
    ];

    /**
     * Pilihan jabatan penutup daftar dropdown form admin.
     *
     * @var array<int, string>
     */
    public const JABATAN_ANGGOTA = [
        'Anggota',
    ];

    protected $fillable = [
        'nama',
        'jabatan',
        'banom',
        'label_banom',
        'foto',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * URL foto pengurus di disk Supabase, siap dipakai di view.
     *
     * Nilai kosong dikembalikan sebagai string kosong agar view cukup
     * memakai `$pengurus->foto_url` tanpa pemeriksaan null terpisah.
     */
    protected function fotoUrl(): Attribute
    {
        return Attribute::get(fn (): string => blank($this->foto)
            ? ''
            : Storage::disk('supabase')->url($this->foto));
    }

    /**
     * Tentukan level piramida dari teks jabatan.
     * 1 = Ketua, 2 = pengurus harian (wakil/sekretaris/bendahara), 3 = bidang.
     */
    public static function levelJabatan(?string $jabatan): int
    {
        $j = mb_strtolower(trim((string) $jabatan));

        if (str_contains($j, 'wakil')) {
            return 2;
        }

        if (str_contains($j, 'ketua')
            && ! str_contains($j, 'bidang')
            && ! str_contains($j, 'departemen')
            && ! str_contains($j, 'seksi')
            && ! str_contains($j, 'komisi')
        ) {
            return 1;
        }

        if (str_contains($j, 'sekret') || str_contains($j, 'bendahara') || str_contains($j, 'bendaraha')) {
            return 2;
        }

        return 3;
    }

    /**
     * Bobot urut pengurus harian: wakil → sekretaris → bendahara.
     */
    public static function bobotInti(?string $jabatan): int
    {
        $j = mb_strtolower(trim((string) $jabatan));

        return match (true) {
            str_contains($j, 'wakil') => 1,
            str_contains($j, 'sekret') => 2,
            str_contains($j, 'bendahara'), str_contains($j, 'bendaraha') => 3,
            default => 4,
        };
    }

    /**
     * Daftar pilihan jabatan untuk satu banom: inti → bidang → anggota.
     *
     * @return array<int, string>
     */
    public static function daftarJabatan(?string $banom): array
    {
        $bidang = $banom !== null && isset(self::JABATAN_BIDANG[$banom])
            ? self::JABATAN_BIDANG[$banom]
            : self::JABATAN_BIDANG_UMUM;

        return array_values(array_unique([
            ...self::JABATAN_INTI,
            ...$bidang,
            ...self::JABATAN_ANGGOTA,
        ]));
    }

    /**
     * Peta kode banom => daftar jabatan untuk dropdown dinamis di form admin.
     * Kunci "__default__" dipakai saat banom belum dipilih atau bukan kode resmi.
     *
     * @return array<string, array<int, string>>
     */
    public static function jabatanPerBanom(): array
    {
        $peta = [];

        foreach (array_keys(self::BANOMS) as $kode) {
            $peta[$kode] = self::daftarJabatan($kode);
        }

        $peta['__default__'] = self::daftarJabatan(null);

        return $peta;
    }
}

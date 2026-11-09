<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'organisasi',
        'tanggal',
        'deskripsi',
        'foto',
        'urutan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'urutan' => 'integer',
    ];

    /**
     * URL foto yang siap dipakai di view.
     *
     * Kolom `foto` boleh berisi path relatif di disk Supabase (mis. `galleries/x.jpg`)
     * maupun URL absolut (dipakai data seeder). Tanpa accessor ini, view harus
     * mengulang logika ini sendiri dan bisa salah menggabungkan path absolut
     * dengan base URL.
     */
    /**
     * Tanggal tampil untuk foto: tanggal bila diisi admin,
     * jatuh ke created_at untuk data lama yang belum punya tanggal.
     */
    public function getTanggalTampilAttribute(): ?CarbonInterface
    {
        return $this->tanggal ?? $this->created_at;
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn (): string => match (true) {
            blank($this->foto) => '',
            preg_match('#^https?://#i', $this->foto) === 1 => $this->foto,
            default => Storage::disk('supabase')->url($this->foto),
        });
    }

    /**
     * Data organisasi dari config sesuai slug tersimpan, atau null bila
     * foto tidak terikat satu organisasi.
     *
     * @return array<string, mixed>|null
     */
    public function organisasiInfo(): ?array
    {
        if (blank($this->organisasi)) {
            return null;
        }

        return collect(config('organisasi.organisasi', []))
            ->firstWhere('slug', $this->organisasi);
    }

    /**
     * Nama pendek organisasi untuk label/badge.
     *
     * Bila slug sudah tidak ada di config (data lama), slug itu sendiri
     * ditampilkan supaya tidak ada label kosong.
     */
    public function organisasiLabel(): string
    {
        $info = $this->organisasiInfo();

        if ($info !== null) {
            return trim((string) $info['singkatan']);
        }

        return (string) $this->organisasi;
    }

    /**
     * Daftar organisasi yang boleh dipilih sebagai sumber foto.
     *
     * Sumbernya config('organisasi.organisasi') sehingga daftarnya selalu
     * sinkron dengan halaman publik /organisasi. Hanya organisasi
     * terverifikasi yang ditawarkan; yang berstatus contoh data disembunyikan.
     *
     * @return array<int, array{slug: string, singkatan: string, nama: string, kategori: string}>
     */
    public static function daftarOrganisasi(): array
    {
        return collect(config('organisasi.organisasi', []))
            ->filter(fn (array $org): bool => ($org['terverifikasi'] ?? false) === true)
            ->map(fn (array $org): array => [
                'slug' => (string) $org['slug'],
                'singkatan' => trim((string) $org['singkatan']),
                'nama' => (string) $org['nama'],
                'kategori' => (string) $org['kategori'],
            ])
            ->sortBy(fn (array $org): array => [self::urutKategori($org['kategori']), $org['singkatan']])
            ->values()
            ->all();
    }

    /**
     * Opsi dropdown dikelompokkan per kategori untuk dipakai <optgroup>.
     *
     * @return array<int, array{kode: string, label: string, opsi: array<int, array{slug: string, singkatan: string, nama: string}>}>
     */
    public static function opsiOrganisasiPerKategori(): array
    {
        $daftarKategori = (array) config('organisasi.kategori', []);

        return collect(self::daftarOrganisasi())
            ->groupBy('kategori')
            ->map(fn ($opsi, string $kode) => [
                'kode' => $kode,
                'label' => $daftarKategori[$kode] ?? ucfirst($kode),
                'opsi' => $opsi->map(fn (array $org): array => [
                    'slug' => $org['slug'],
                    'singkatan' => $org['singkatan'],
                    'nama' => $org['nama'],
                ])->values()->all(),
            ])
            ->sortBy(fn (array $grup): int => self::urutKategori($grup['kode']))
            ->values()
            ->all();
    }

    /**
     * Urutan tampil kategori pada dropdown admin.
     */
    private static function urutKategori(string $kategori): int
    {
        return match ($kategori) {
            'pengurus' => 1,
            'banom' => 2,
            'lembaga' => 3,
            default => 4,
        };
    }
}

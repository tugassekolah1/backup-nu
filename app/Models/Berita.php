<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Berita extends Model
{
    use HasFactory;

    /**
     * Pilihan jenis berita yang tersedia di form admin.
     */
    public const JENIS = ['Pengumuman', 'Kegiatan', 'Artikel', 'Berita'];

    /**
     * Pilihan pengurutan daftar berita (publik & admin).
     */
    public const SORTS = [
        'terbaru' => 'Terbaru',
        'terpopuler' => 'Terpopuler',
        'terlama' => 'Terlama',
    ];

    /**
     * `views` sengaja tidak masuk $fillable: penghitung pembaca hanya boleh
     * berubah lewat increment() di halaman publik, bukan lewat input massal.
     */
    protected $fillable = ['judul', 'slug', 'jenis', 'tanggal_terbit', 'isi', 'gambar', 'gambar_url', 'user_id', 'status'];

    protected $attributes = [
        'jenis' => 'Berita',
        'views' => 0,
    ];

    protected $casts = [
        'status' => 'boolean',
        'views' => 'integer',
        'tanggal_terbit' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tanggal tampil untuk berita: tanggal_terbit bila diisi admin,
     * jatuh ke created_at untuk data lama yang belum punya tanggal_terbit.
     */
    public function getTanggalTampilAttribute(): ?CarbonInterface
    {
        return $this->tanggal_terbit ?? $this->created_at;
    }

    /**
     * Ambil gambar dari URL atau file upload.
     * Prioritas: gambar_url > gambar (file upload)
     *
     * File upload diseimpan sebagai path relatif di disk Supabase, sedangkan
     * data lama bisa berisi URL absolut — keduanya dihasilkan dengan benar.
     */
    public function getGambarUtamaAttribute(): ?string
    {
        if ($this->gambar_url) {
            return $this->gambar_url;
        }

        if ($this->gambar) {
            return preg_match('#^https?://#i', $this->gambar) === 1
                ? $this->gambar
                : Storage::disk('supabase')->url($this->gambar);
        }

        return null;
    }

    /**
     * Hanya berita yang sudah diterbitkan (aman untuk halaman publik).
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Urutkan berita sesuai pilihan pengguna: terbaru (bawaan), terpopuler,
     * atau terlama. Pilihan di luar daftar SORTS diabaikan.
     */
    public function scopeSorted(Builder $query, ?string $sort = null): Builder
    {
        // Urutkan berdasarkan tanggal tampil (tanggal_terbit, fallback created_at).
        // COALESCE menjaga data lama (tanggal_terbit NULL) tetap terurut seperti semula.
        return match ($sort) {
            'terlama' => $query->orderByRaw('COALESCE(tanggal_terbit, created_at) ASC')->orderBy('created_at')->orderBy('id'),
            'terpopuler' => $query->orderByDesc('views')->orderByRaw('COALESCE(tanggal_terbit, created_at) DESC')->orderByDesc('created_at')->orderByDesc('id'),
            default => $query->orderByRaw('COALESCE(tanggal_terbit, created_at) DESC')->orderByDesc('created_at')->orderByDesc('id'),
        };
    }

    /**
     * Filter berita berdasarkan kata kunci (judul/slug/isi) dan jenis.
     * Jenis di luar daftar JENIS diabaikan.
     */
    public function scopeFilter(Builder $query, ?string $q = null, ?string $jenis = null): Builder
    {
        $q = trim((string) $q);

        return $query
            ->when($q !== '', function (Builder $query) use ($q) {
                $like = "%{$q}%";

                $query->where(function (Builder $query) use ($like) {
                    $query->where('judul', 'like', $like)
                        ->orWhere('slug', 'like', $like)
                        ->orWhere('isi', 'like', $like);
                });
            })
            ->when(in_array($jenis, self::JENIS, true), fn (Builder $query) => $query->where('jenis', $jenis));
    }

    /**
     * Estimasi waktu baca berdasarkan jumlah kata (±200 kata/menit).
     */
    public function getEstimasiBacaAttribute(): string
    {
        $teks = trim(strip_tags((string) $this->isi));
        $jumlahKata = $teks === '' ? 0 : str_word_count($teks);
        $menit = max(1, (int) ceil($jumlahKata / 200));

        return $menit.' menit baca';
    }
}

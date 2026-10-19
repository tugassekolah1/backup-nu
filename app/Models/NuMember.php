<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NuMember extends Model
{
    protected $fillable = [
        'nik', 'full_name', 'phone', 'gender', 'organisasi', 'label_organisasi',
        'address', 'photo',
        'member_card_no', 'status',
        'registration_status', 'rejection_reason',
    ];

    /**
     * Pilihan organisasi / banom untuk pendaftaran anggota,
     * dikelompokkan sesuai struktur kaderisasi NU.
     * Kode => kode pada Pengurus::BANOMS (label diambil dari sana).
     */
    public const ORGANISASI_GROUPS = [
        'Banom Kaderisasi (Usia & Gender)' => [
            'pac_muslimat' => 'Perempuan / tokoh wanita NU',
            'pac_fatayat' => 'Perempuan muda, maks. 40 tahun',
            'pac_ansor' => 'Pemuda NU, maks. 40 tahun',
            'satkoryon_banser' => 'Unit Barisan Ansor Serbaguna',
            'pac_ipnu' => 'Pelajar, santri & mahasiswa laki-laki, maks. 27 tahun',
            'cbp_ipnu' => 'Unit Corps Brigade Pembangunan (di bawah IPNU)',
            'pac_ippnu' => 'Pelajar, santri & mahasiswi, maks. 27 tahun',
            'kpp_ippnu' => 'Unit Korps Pelajar Putri (di bawah IPPNU)',
        ],
        'Banom Profesi, Kekhususan & Hobi' => [
            'pac_pencak_silat' => 'Seni bela diri pencak silat',
            'pac_pargu' => 'Guru & pendidik NU',
            'pac_isnu' => 'Sarjana, intelektual & profesional',
            'pac_jqhnu' => "Qari, qariah & penghafal Al-Qur'an",
            'pac_jatman' => 'Pengamal ajaran thariqah',
            'pac_ishari' => 'Seni hadrah & selawat tradisional',
        ],
    ];

    /**
     * Daftar kode organisasi yang boleh dipilih (untuk validasi).
     *
     * @return string[]
     */
    public static function organisasiCodes(): array
    {
        return collect(static::ORGANISASI_GROUPS)
            ->map(fn ($group) => array_keys($group))
            ->flatten()
            ->all();
    }

    /**
     * Label tampilan organisasi anggota.
     * Prioritas: label tersimpan > label baku > kode > null.
     */
    public function getOrganisasiLabelAttribute(): ?string
    {
        if (! empty($this->label_organisasi)) {
            return $this->label_organisasi;
        }

        if (! empty($this->organisasi) && isset(Pengurus::BANOMS[$this->organisasi])) {
            return Pengurus::BANOMS[$this->organisasi]['label'];
        }

        return $this->organisasi;
    }

    /**
     * Status permintaan pendaftaran anggota.
     *
     * Nilai kolom `registration_status` (pending|accepted|rejected) dipakai bila sudah
     * diisi. Data lama (sebelum kolom ini ada) diturunkan dari status keanggotaan:
     * anggota aktif dianggap diterima, selain itu masih menunggu.
     */
    public function registrationState(): string
    {
        if ($this->registration_status !== null) {
            return $this->registration_status;
        }

        return $this->status === 'active' ? 'accepted' : 'pending';
    }

    /**
     * Filter query berdasarkan status pendaftaran: pending|accepted|rejected.
     * Baris lama (registration_status NULL) diturunkan dari kolom status
     * dengan logika yang sama seperti registrationState().
     */
    public function scopeWithRegistrationState($query, string $state)
    {
        return $query->where(function ($q) use ($state) {
            match ($state) {
                'pending' => $q->where('registration_status', 'pending')
                    ->orWhere(fn ($qq) => $qq->whereNull('registration_status')->where('status', '!=', 'active')),
                'accepted' => $q->where('registration_status', 'accepted')
                    ->orWhere(fn ($qq) => $qq->whereNull('registration_status')->where('status', 'active')),
                'rejected' => $q->where('registration_status', 'rejected'),
                default => $q->whereRaw('1 = 1'),
            };
        });
    }

    /**
     * Anggota yang masih relevan dihitung (bukan yang pendaftarannya ditolak).
     */
    public function scopeNotRejected($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('registration_status')
                ->orWhere('registration_status', '!=', 'rejected');
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $fillable = [
        'title', 'category', 'event_date', 'event_time', 'location',
        'latitude', 'longitude', 'maps_url', 'description',
    ];

    protected $casts = [
        'event_date' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * Apakah agenda sudah lewat (tanggal acara di bawah hari ini)?
     *
     * Agenda hari ini masih dihitung mendatang, sesuai pemisahan
     * daftar halaman publik (mendatang >= hari ini, lewat < hari ini).
     */
    public function isPast(): bool
    {
        return $this->event_date !== null
            && $this->event_date->lt(today());
    }

    /**
     * Link Google Maps universal untuk lokasi agenda.
     * Prioritas: maps_url manual > koordinat > nama lokasi.
     */
    public function getMapsLinkAttribute(): ?string
    {
        if (! empty($this->maps_url)) {
            return $this->maps_url;
        }

        if (! empty($this->latitude) && ! empty($this->longitude)) {
            return 'https://www.google.com/maps/search/?api=1&query='
                .$this->latitude.','.$this->longitude;
        }

        if (! empty($this->location)) {
            return 'https://www.google.com/maps/search/?api=1&query='
                .urlencode($this->location);
        }

        return null;
    }

    /**
     * URL embed tanpa API key (cukup untuk iframe publik).
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if (! empty($this->latitude) && ! empty($this->longitude)) {
            return 'https://maps.google.com/maps?q='
                .$this->latitude.','.$this->longitude.'&z=16&output=embed';
        }

        if (! empty($this->location)) {
            return 'https://maps.google.com/maps?q='
                .urlencode($this->location).'&z=16&output=embed';
        }

        return null;
    }
}

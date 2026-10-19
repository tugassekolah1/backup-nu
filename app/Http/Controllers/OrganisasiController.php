<?php

namespace App\Http\Controllers;

use App\Models\Pengurus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganisasiController extends Controller
{
    /**
     * Direktori organisasi NU: pencarian + filter kategori.
     */
    public function index(Request $request): View
    {
        $kategori = $request->query('kategori', 'semua');
        if (! isset(config('organisasi.kategori')[$kategori])) {
            $kategori = 'semua';
        }

        $q = trim((string) $request->query('q', ''));

        $semua = collect(config('organisasi.organisasi'));

        // Hanya tampilkan organisasi yang terverifikasi keberadaannya
        $terverifikasi = $semua->where('terverifikasi', true);

        $terfilter = $terverifikasi
            ->when($kategori !== 'semua', fn ($items) => $items->where('kategori', $kategori))
            ->when($q !== '', fn ($items) => $items->filter(fn ($org) => str_contains(
                mb_strtolower($org['nama'].' '.$org['singkatan'].' '.$org['deskripsi']),
                mb_strtolower($q)
            )))
            ->values();

        // Navigasi logo: hanya banom terverifikasi yang dipakai sebagai tautan cepat
        $navigasi = $terverifikasi
            ->filter(fn ($org) => $org['banom'] !== null && $org['kategori'] === 'banom')
            ->values();

        // Data pengurus untuk modal detail tiap organisasi (dikelompokkan per banom)
        $semuaPengurus = Pengurus::orderBy('urutan', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('banom');

        return view('organisasi.index', [
            'kategoriAktif' => $kategori,
            'q' => $q,
            'daftar' => $terfilter,
            'navigasi' => $navigasi,
            'totalSemua' => $terverifikasi->count(),
            'semuaPengurus' => $semuaPengurus,
        ]);
    }

    /**
     * Halaman detail profil organisasi dengan tab Profil / Struktur / Program.
     */
    public function show(Request $request, string $slug): View
    {
        $organisasi = collect(config('organisasi.organisasi'))
            ->firstWhere('slug', $slug);

        abort_if($organisasi === null, 404);

        $tab = $request->query('tab', 'profil');
        if (! in_array($tab, ['profil', 'struktur', 'program'], true)) {
            $tab = 'profil';
        }

        // Struktur pengurus dari tabel penguruses bila organisasi punya banom
        $ketua = collect();
        $inti = collect();
        $bidang = collect();
        $jabatanList = collect();
        $jabatanFilter = 'all';
        $hasilFilter = collect();

        if ($organisasi['banom'] !== null) {
            $pengurus = Pengurus::query()
                ->whereIn('banom', $this->kodeBanom($organisasi))
                ->orderBy('urutan')
                ->orderBy('id')
                ->get();

            $ketua = $pengurus->filter(fn ($p) => Pengurus::levelJabatan($p->jabatan) === 1)->values();

            $inti = $pengurus->filter(fn ($p) => Pengurus::levelJabatan($p->jabatan) === 2)
                ->sortBy(fn ($p) => [Pengurus::bobotInti($p->jabatan), $p->urutan ?? 0, $p->id])
                ->values();

            $bidang = $pengurus->filter(fn ($p) => Pengurus::levelJabatan($p->jabatan) === 3)->values();

            // Filter jabatan (warisan halaman /struktur lama): daftar opsi + hasil terpilih.
            $jabatanList = $pengurus
                ->groupBy('jabatan')
                ->map(fn ($items, $jabatan) => [
                    'jabatan' => $jabatan,
                    'jumlah' => $items->count(),
                ])
                ->sortBy(fn ($row) => [Pengurus::levelJabatan($row['jabatan']), $row['jabatan']])
                ->values();

            $jabatanFilter = (string) $request->query('jabatan', 'all');
            if (! $jabatanList->contains(fn ($row) => $row['jabatan'] === $jabatanFilter)) {
                $jabatanFilter = 'all';
            }

            $hasilFilter = $jabatanFilter === 'all'
                ? collect()
                : $pengurus->filter(fn ($p) => $p->jabatan === $jabatanFilter)->values();
        }

        $lainnya = collect(config('organisasi.organisasi'))
            ->where('kategori', $organisasi['kategori'])
            ->where('slug', '!=', $slug)
            ->take(4)
            ->values();

        return view('organisasi.show', [
            'org' => $organisasi,
            'tabAktif' => $tab,
            'ketua' => $ketua,
            'inti' => $inti,
            'bidang' => $bidang,
            'punyaStruktur' => $ketua->isNotEmpty() || $inti->isNotEmpty() || $bidang->isNotEmpty(),
            'jabatanList' => $jabatanList,
            'jabatanFilter' => $jabatanFilter,
            'hasilFilter' => $hasilFilter,
            'lainnya' => $lainnya,
        ]);
    }

    /**
     * Kode banom utama + kode tambahan sebuah organisasi.
     *
     * Beberapa banom sengaja digabung ke satu halaman organisasi, misalnya
     * Satkoryon Banser tampil pada halaman GP Ansor dan CBP/KPP tampil pada
     * halaman IPNU/IPPNU.
     *
     * @param  array<string, mixed>  $organisasi
     * @return array<int, string>
     */
    private function kodeBanom(array $organisasi): array
    {
        return array_values(array_unique(array_filter(array_merge(
            [$organisasi['banom']],
            $organisasi['banom_tambahan'] ?? [],
        ))));
    }
}

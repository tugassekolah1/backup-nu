<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Halaman galeri publik, dengan filter organisasi.
     */
    public function publicIndex(Request $request): View
    {
        $daftarOrganisasi = Gallery::daftarOrganisasi();

        // Slug yang tidak dikenal dianggap "semua" agar tidak memunculkan
        // 404 ketika pengunjung mengutak-atik URL.
        $organisasiAktif = (string) $request->query('organisasi', '');
        if (! in_array($organisasiAktif, array_column($daftarOrganisasi, 'slug'), true)) {
            $organisasiAktif = '';
        }

        $galleries = Gallery::query()
            ->when($organisasiAktif !== '', fn (Builder $query) => $query->where('organisasi', $organisasiAktif))
            ->orderBy('urutan', 'asc')
            ->orderByRaw('COALESCE(tanggal, created_at) DESC')
            ->get();

        // Satu agregat untuk jumlah foto tiap organisasi (tanpa N+1).
        $jumlahPerOrganisasi = Gallery::query()
            ->selectRaw('organisasi, count(*) as jumlah')
            ->groupBy('organisasi')
            ->pluck('jumlah', 'organisasi');

        $organisasiTerpilih = collect($daftarOrganisasi)->firstWhere('slug', $organisasiAktif);

        return view('galeri', [
            'galleries' => $galleries,
            'daftarOrganisasi' => $daftarOrganisasi,
            'organisasiAktif' => $organisasiAktif,
            'namaOrganisasiAktif' => $organisasiTerpilih['singkatan'] ?? '',
            'jumlahPerOrganisasi' => $jumlahPerOrganisasi,
            'jumlahSemua' => (int) $jumlahPerOrganisasi->sum(),
        ]);
    }

    /**
     * Daftar foto galeri di panel admin, dengan pencarian dan filter organisasi.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $daftarOrganisasi = Gallery::daftarOrganisasi();

        $organisasiAktif = (string) $request->query('organisasi', '');
        if (! in_array($organisasiAktif, array_column($daftarOrganisasi, 'slug'), true)) {
            $organisasiAktif = '';
        }

        $galleries = Gallery::query()
            ->when($organisasiAktif !== '', fn (Builder $query) => $query->where('organisasi', $organisasiAktif))
            ->when($q !== '', function (Builder $query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function (Builder $query) use ($like) {
                    $query->where('judul', 'like', $like)
                        ->orWhere('deskripsi', 'like', $like);
                });
            })
            ->orderBy('urutan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('gallery.index', compact('galleries', 'q', 'daftarOrganisasi', 'organisasiAktif'));
    }

    public function create(): View
    {
        return view('gallery.create', [
            'opsiOrganisasi' => Gallery::opsiOrganisasiPerKategori(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'organisasi' => ['nullable', 'string', Rule::in(array_column(Gallery::daftarOrganisasi(), 'slug'))],
            'tanggal' => ['nullable', 'date', 'before_or_equal:'.today()->toDateString()],
            'deskripsi' => 'nullable|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'urutan' => 'nullable|integer',
        ], [
            'tanggal.date' => 'Format tanggal foto tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal foto tidak boleh setelah hari ini.',
        ]);

        $fotoPath = $request->file('foto')->store('galleries', 'supabase');

        Gallery::create([
            'judul' => $validated['judul'],
            'organisasi' => ! empty($validated['organisasi'] ?? null) ? $validated['organisasi'] : null,
            'tanggal' => ! empty($validated['tanggal'] ?? null) ? $validated['tanggal'] : null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'foto' => $fotoPath,
            'urutan' => $validated['urutan'] ?? 0,
        ]);

        return redirect()->route('gallery.index')->with('success', 'Foto galeri berhasil ditambahkan.');
    }

    public function edit(Gallery $gallery): View
    {
        return view('gallery.edit', [
            'gallery' => $gallery,
            'opsiOrganisasi' => Gallery::opsiOrganisasiPerKategori(),
        ]);
    }

    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'organisasi' => ['nullable', 'string', Rule::in(array_column(Gallery::daftarOrganisasi(), 'slug'))],
            'tanggal' => ['nullable', 'date', 'before_or_equal:'.today()->toDateString()],
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'urutan' => 'nullable|integer',
        ], [
            'tanggal.date' => 'Format tanggal foto tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal foto tidak boleh setelah hari ini.',
        ]);

        $fotoPath = $gallery->foto;

        if ($request->hasFile('foto')) {
            if ($gallery->foto && Storage::disk('supabase')->exists($gallery->foto)) {
                Storage::disk('supabase')->delete($gallery->foto);
            }
            $fotoPath = $request->file('foto')->store('galleries', 'supabase');
        }

        $gallery->update([
            'judul' => $validated['judul'],
            'organisasi' => ! empty($validated['organisasi'] ?? null) ? $validated['organisasi'] : null,
            'tanggal' => ! empty($validated['tanggal'] ?? null) ? $validated['tanggal'] : null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'foto' => $fotoPath,
            'urutan' => $validated['urutan'] ?? 0,
        ]);

        return redirect()->route('gallery.index')->with('success', 'Data galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->foto && Storage::disk('supabase')->exists($gallery->foto)) {
            Storage::disk('supabase')->delete($gallery->foto);
        }

        $gallery->delete();

        return redirect()->route('gallery.index')->with('success', 'Foto galeri berhasil dihapus.');
    }
}

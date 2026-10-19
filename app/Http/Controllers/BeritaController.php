<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\BeritaView;
use App\Models\Pengurus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $jenis = $request->query('jenis');
        $sort = $this->resolveSort($request->query('sort'));

        $beritas = Berita::with('user')
            ->filter($q, $jenis)
            ->sorted($sort)
            ->paginate(10)
            ->withQueryString();

        // Statistik ringkas untuk kartu di atas tabel.
        $totalBerita = Berita::count();
        $totalViews = (int) Berita::sum('views');
        $totalPublish = Berita::published()->count();

        $stats = [
            'total' => $totalBerita,
            'publish' => $totalPublish,
            'draft' => $totalBerita - $totalPublish,
            'views' => $totalViews,
            'rataViews' => $totalBerita > 0 ? (int) round($totalViews / $totalBerita) : 0,
        ];

        // Peringkat berita paling banyak dibaca.
        $terpopuler = Berita::published()
            ->orderByDesc('views')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('admin.berita.index', compact('beritas', 'q', 'jenis', 'sort', 'stats', 'terpopuler'));
    }

    public function create()
    {
        return view('admin.berita.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|max:255|unique:beritas,judul',
            'slug' => $this->slugRules(),
            'jenis' => ['required', Rule::in(Berita::JENIS)],
            'isi' => 'required',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gambar_url' => 'nullable|url|max:500',
            'status' => 'boolean',
        ], [
            'judul.unique' => 'Judul berita ini sudah pernah digunakan. Silakan gunakan judul lain.',
            'slug.unique' => 'Slug ini sudah dipakai berita lain. Silakan gunakan slug lain.',
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda strip (-).',
            'jenis.in' => 'Jenis berita tidak dikenali.',
            'gambar_url.url' => 'URL gambar harus format URL yang valid.',
        ]);

        $validated['slug'] = $validated['slug'] ?? $this->generateUniqueSlug($validated['judul']);
        $validated['user_id'] = auth()->id();

        // Prioritas: jika ada file upload, gunakan itu. Jika ada URL, gunakan URL.
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
            $validated['gambar_url'] = null; // kosongkan URL jika ada file
        }

        Berita::create($validated);

        return redirect()->route('berita.index')->with('success', 'Berita ditambahkan');
    }

    public function show(Berita $berita)
    {
        abort_if(! $berita->status, 404); // draft gak boleh diakses publik

        // Hitung pembaca untuk statistik & sidebar "Terpopuler"
        $berita->increment('views');
        $this->catatPembaca($berita);

        $newsList = Berita::published()
            ->where('id', '!=', $berita->id)
            ->latest()
            ->take(5)
            ->get();

        return view('berita-detail', compact('berita', 'newsList'));
    }

    public function edit(Berita $berita)
    {
        return view('admin.berita.edit', compact('berita'));
    }

    public function update(Request $request, Berita $berita)
    {
        $validated = $request->validate([
            'judul' => 'required|max:255',
            'slug' => $this->slugRules($berita->id),
            'jenis' => ['required', Rule::in(Berita::JENIS)],
            'isi' => 'required',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gambar_url' => 'nullable|url|max:500',
            'status' => 'boolean',
        ], [
            'slug.unique' => 'Slug ini sudah dipakai berita lain. Silakan gunakan slug lain.',
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda strip (-).',
            'jenis.in' => 'Jenis berita tidak dikenali.',
            'gambar_url.url' => 'URL gambar harus format URL yang valid.',
        ]);

        $validated['slug'] = $validated['slug'] ?? $this->generateUniqueSlug($validated['judul'], $berita->id);

        $gambarLama = $berita->gambar;

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
            $validated['gambar_url'] = null; // kosongkan URL jika ada file baru
        } elseif (isset($validated['gambar_url']) && $validated['gambar_url']) {
            $validated['gambar'] = null; // kosongkan file jika ada URL
        }

        $berita->update($validated);

        // Bersihkan file lama setelah baris berhasil diperbarui agar tidak menumpuk.
        if ($berita->wasChanged('gambar') && $gambarLama) {
            Storage::disk('public')->delete($gambarLama);
        }

        return redirect()->route('berita.index')->with('success', 'Berita diperbarui');
    }

    public function destroy(Berita $berita)
    {
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $berita->delete();

        return redirect()->route('berita.index')->with('success', 'Berita dihapus');
    }

    public function landing()
    {
        $newsList = Berita::with('user')
            ->published()
            ->latest()
            ->paginate(6);
        $upcomingAgenda = Agenda::where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->take(3)
            ->get();
        $pengurus = Pengurus::orderBy('urutan', 'asc')->get();

        return view('welcome', compact('newsList', 'upcomingAgenda', 'pengurus'));
    }

    public function index_publik(Request $request)
    {
        $q = (string) $request->query('q', '');
        $jenis = $request->query('jenis');
        $sort = $this->resolveSort($request->query('sort'));

        $newsList = Berita::filter($q, $jenis)
            ->published()
            ->sorted($sort)
            ->paginate(5)
            ->withQueryString();

        $isFiltering = trim($q) !== '' || in_array($jenis, Berita::JENIS, true);

        // Sidebar: berita paling banyak dibaca
        $popular = Berita::published()
            ->orderByDesc('views')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // Sidebar: jumlah berita per kanal/jenis
        $kanalCounts = Berita::published()
            ->groupBy('jenis')
            ->selectRaw('jenis, COUNT(*) as total')
            ->pluck('total', 'jenis');

        $totalBerita = Berita::published()->count();

        return view('berita', compact('newsList', 'q', 'jenis', 'sort', 'isFiltering', 'popular', 'kanalCounts', 'totalBerita'));
    }

    /**
     * Pastikan parameter sort hanya menerima nilai yang dikenal,
     * jika tidak jatuh ke "terbaru" (urutan bawaan).
     */
    private function resolveSort(mixed $sort): string
    {
        return is_string($sort) && array_key_exists($sort, Berita::SORTS)
            ? $sort
            : 'terbaru';
    }

    /**
     * Halaman statistik pembaca berita berbentuk grafik (khusus admin).
     */
    public function statistik(): View
    {
        // Ringkasan angka utama.
        $totalBerita = Berita::count();
        $totalViews = (int) Berita::sum('views');
        $totalPublish = Berita::published()->count();

        $stats = [
            'total' => $totalBerita,
            'publish' => $totalPublish,
            'draft' => $totalBerita - $totalPublish,
            'views' => $totalViews,
            'rataViews' => $totalBerita > 0 ? (int) round($totalViews / $totalBerita) : 0,
        ];

        // --- Grafik 1: tren pembaca 30 hari terakhir ---
        $rekapHarian = $this->pembacaHarian(30);
        $labelHarian = array_keys($rekapHarian);
        $dataHarian = array_values($rekapHarian);
        $puncakHarian = max($dataHarian ?: [0]);

        // --- Grafik 2: 10 berita terpopuler ---
        $terpopuler = Berita::published()
            ->orderByDesc('views')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(10)
            ->get();

        // --- Grafik 3: berita terbit & totalnya tiap bulan (6 bulan) ---
        $perBulan = $this->rekapBulan(6);
        $labelBulan = array_keys($perBulan);
        $dataTerbit = array_column($perBulan, 'terbit');
        $dataViewsBulan = array_column($perBulan, 'views');

        // --- Grafik 4: komposisi pembaca per jenis/kanal ---
        $perJenis = Berita::published()
            ->selectRaw('jenis, COUNT(*) as total_berita, COALESCE(SUM(views), 0) as total_views')
            ->groupBy('jenis')
            ->orderByDesc('total_views')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->jenis,
                'value' => (int) $row->total_views,
                'berita' => (int) $row->total_berita,
            ]);

        return view('admin.berita.statistik', compact(
            'stats',
            'labelHarian',
            'dataHarian',
            'puncakHarian',
            'terpopuler',
            'labelBulan',
            'dataTerbit',
            'dataViewsBulan',
            'perJenis',
        ));
    }

    /**
     * Catat satu pembaca untuk hari ini pada rekap harian berita.
     */
    private function catatPembaca(Berita $berita): void
    {
        $today = now()->toDateString();

        $rekap = BeritaView::firstOrNew([
            'berita_id' => $berita->id,
            'tanggal' => $today,
        ]);

        $rekap->jumlah = ((int) $rekap->jumlah) + 1;
        $rekap->save();
    }

    /**
     * Total pembaca per hari selama $hari hari terakhir (hari tanpa
     * pembaca tetap muncul dengan nilai 0 agar garis grafik utuh).
     *
     * @return array<string, int>
     */
    private function pembacaHarian(int $hari): array
    {
        $mulai = now()->subDays($hari - 1)->startOfDay();

        $tercatat = BeritaView::query()
            ->where('tanggal', '>=', $mulai->toDateString())
            ->groupBy('tanggal')
            ->selectRaw('tanggal, SUM(jumlah) as total')
            ->orderBy('tanggal')
            ->get()
            ->mapWithKeys(fn ($row) => [substr((string) $row->tanggal, 0, 10) => (int) $row->total]);

        $hasil = [];

        foreach (range($hari - 1, 0) as $mundur) {
            $tanggal = now()->subDays($mundur);
            $hasil[$tanggal->translatedFormat('d M')] = (int) ($tercatat[$tanggal->toDateString()] ?? 0);
        }

        return $hasil;
    }

    /**
     * Jumlah berita terbit dan total pembacanya per bulan.
     *
     * @return array<string, array{terbit: int, views: int}>
     */
    private function rekapBulan(int $bulan): array
    {
        $mulai = now()->startOfMonth()->subMonths($bulan - 1);

        $dikelompokkan = Berita::query()
            ->where('created_at', '>=', $mulai)
            ->get(['created_at', 'views'])
            ->groupBy(fn (Berita $berita) => $berita->created_at->format('Y-m'));

        $hasil = [];

        foreach (range($bulan - 1, 0) as $mundur) {
            $tanggal = now()->startOfMonth()->subMonths($mundur);
            $grup = $dikelompokkan->get($tanggal->format('Y-m'), collect());

            $hasil[$tanggal->locale('id')->translatedFormat('M Y')] = [
                'terbit' => $grup->count(),
                'views' => (int) $grup->sum('views'),
            ];
        }

        return $hasil;
    }

    /**
     * Aturan validasi slug: wajib format slug dan unik (abaikan ID saat update).
     * Kolom bersifat nullable karena bisa dikosongkan agar dibuat otomatis dari judul.
     */
    private function slugRules($ignoreId = null): array
    {
        $unique = Rule::unique('beritas', 'slug');

        if ($ignoreId) {
            $unique->ignore($ignoreId);
        }

        return ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $unique];
    }

    /**
     * Method untuk membuat slug unik dari judul berita
     */
    private function generateUniqueSlug($judul, $ignoreId = null)
    {
        $slug = Str::slug($judul);

        // Cek apakah slug sudah ada di database (abaikan ID berita jika sedang update)
        $query = Berita::where('slug', 'LIKE', "{$slug}%");
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $count = $query->count();

        return $count ? "{$slug}-".($count + 1) : $slug;
    }
}

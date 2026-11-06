<?php

namespace App\Http\Controllers;

use App\Models\Pengurus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengurusController extends Controller
{
    // Halaman detail profil satu pengurus (publik)
    public function show(Pengurus $pengurus)
    {
        $lainnya = Pengurus::query()
            ->where('banom', $pengurus->banom)
            ->where('id', '!=', $pengurus->id)
            ->orderBy('urutan')
            ->orderBy('id')
            ->take(4)
            ->get();

        return view('struktur-show', compact('pengurus', 'lainnya') + [
            // Halaman organisasi induk, dipakai untuk tautan kembali yang relevan.
            'org' => $this->organisasiInduk($pengurus),
        ]);
    }

    /**
     * Cari entri direktori organisasi yang menaungi kode banom pengurus.
     *
     * @return array<string, mixed>|null
     */
    private function organisasiInduk(Pengurus $pengurus): ?array
    {
        return collect(config('organisasi.organisasi'))
            ->filter(fn (array $org) => $org['banom'] !== null
                && in_array($pengurus->banom, array_merge(
                    [$org['banom']],
                    $org['banom_tambahan'] ?? [],
                ), true))
            ->firstWhere('terverifikasi', true);
    }

    // Dashboard Admin - List Data
    public function adminIndex()
    {
        $pengurus = Pengurus::orderBy('urutan', 'asc')->orderBy('id', 'asc')->get();

        return view('pengurus.index', compact('pengurus'));
    }

    // Form Tambah
    public function create()
    {
        return view('pengurus.create', [
            'banomOptions' => Pengurus::BANOMS,
            'jabatanMap' => Pengurus::jabatanPerBanom(),
        ]);
    }

    // Store Data
    public function store(Request $request)
    {
        $banom = $request->input('banom');

        $validated = $request->validate($this->rules($banom), $this->messages($banom));

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('pengurus', 'supabase');
        }

        $validated['jabatan'] = trim($validated['jabatan']);
        $validated['label_banom'] = Pengurus::BANOMS[$validated['banom']]['label'];

        if (! $request->filled('urutan')) {
            $validated['urutan'] = $this->urutanOtomatis();
        }

        Pengurus::create($validated);

        return redirect()->route('pengurus.index')->with('success', 'Data pengurus berhasil ditambahkan.');
    }

    // Form Edit
    public function edit(Pengurus $pengurus)
    {
        $banomOptions = Pengurus::BANOMS;

        // Kode lama yang sudah tidak ada di daftar dropdown tetap bisa dipilih saat edit
        if (! isset($banomOptions[$pengurus->banom])) {
            $banomOptions[$pengurus->banom] = [
                'label' => $pengurus->label_banom,
                'name' => "{$pengurus->label_banom} ({$pengurus->banom})",
            ];
        }

        return view('pengurus.edit', [
            'pengurus' => $pengurus,
            'banomOptions' => $banomOptions,
            'jabatanMap' => Pengurus::jabatanPerBanom(),
        ]);
    }

    // Update Data
    public function update(Request $request, Pengurus $pengurus)
    {
        $banom = $request->input('banom');

        $validated = $request->validate(
            $this->rules($banom, $pengurus->banom, $pengurus->id),
            $this->messages($banom)
        );

        if ($request->hasFile('foto')) {
            if ($pengurus->foto) {
                Storage::disk('supabase')->delete($pengurus->foto);
            }
            $validated['foto'] = $request->file('foto')->store('pengurus', 'supabase');
        }

        $validated['jabatan'] = trim($validated['jabatan']);
        $validated['label_banom'] = Pengurus::BANOMS[$validated['banom']]['label']
            ?? $pengurus->label_banom;

        if (! $request->filled('urutan')) {
            $validated['urutan'] = $this->urutanOtomatis();
        }

        $pengurus->update($validated);

        return redirect()->route('pengurus.index')->with('success', 'Data pengurus berhasil diperbarui.');
    }

    // Hapus Data
    public function destroy(Pengurus $pengurus)
    {
        if ($pengurus->foto) {
            Storage::disk('supabase')->delete($pengurus->foto);
        }
        $pengurus->delete();

        return redirect()->route('pengurus.index')->with('success', 'Data pengurus berhasil dihapus.');
    }

    /**
     * Aturan validasi form tambah / edit pengurus.
     *
     * @return array<string, array<int, mixed>>
     */
    private function rules(?string $banomDipilih, ?string $banomTersimpan = null, ?int $kecualiId = null): array
    {
        $banomDiizinkan = array_keys(Pengurus::BANOMS);

        if ($banomTersimpan !== null && ! in_array($banomTersimpan, $banomDiizinkan, true)) {
            $banomDiizinkan[] = $banomTersimpan;
        }

        return [
            'nama' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255', $this->jabatanUnikDalamBanom($banomDipilih, $kecualiId)],
            'banom' => ['required', 'string', Rule::in($banomDiizinkan)],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'urutan' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Pesan error validasi berbahasa Indonesia.
     *
     * @return array<string, string>
     */
    private function messages(?string $banomDipilih): array
    {
        $labelBanom = Pengurus::BANOMS[$banomDipilih]['label'] ?? ($banomDipilih ?: 'organisasi ini');

        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 255 karakter.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'jabatan.max' => 'Jabatan maksimal 255 karakter.',
            'banom.required' => 'Pilih organisasi / banom terlebih dahulu.',
            'banom.in' => 'Organisasi / banom tidak dikenali. Silakan pilih dari daftar yang tersedia.',
            'foto.image' => 'Foto harus berupa gambar (JPG, PNG, atau WebP).',
            'foto.mimes' => 'Foto harus berformat JPG, JPEG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
            'urutan.integer' => 'Nomor urut harus berupa angka bulat.',
            'urutan.min' => 'Nomor urut minimal 1.',
        ];
    }

    /**
     * Cegah satu organisasi (banom) punya dua orang dengan jabatan sama,
     * mis. dua Ketua dalam satu PAC. Berlaku tanpa memperhatikan huruf besar/kecil.
     */
    private function jabatanUnikDalamBanom(?string $banom, ?int $kecualiId = null): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($banom, $kecualiId) {
            if (blank($banom)) {
                return;
            }

            $nilai = trim((string) $value);

            $duplikat = Pengurus::query()
                ->where('banom', $banom)
                ->when($kecualiId, fn ($query) => $query->where('id', '!=', $kecualiId))
                ->whereRaw('lower(trim(jabatan)) = ?', [mb_strtolower($nilai)])
                ->exists();

            if ($duplikat) {
                $label = Pengurus::BANOMS[$banom]['label'] ?? $banom;

                $fail("Jabatan \"{$nilai}\" sudah dipakai di {$label}. Satu organisasi hanya boleh punya satu orang dengan jabatan yang sama.");
            }
        };
    }

    /**
     * Nomor urut berikutnya untuk pengisian otomatis (field urutan dikosongkan).
     */
    private function urutanOtomatis(): int
    {
        return (int) Pengurus::max('urutan') + 1;
    }
}

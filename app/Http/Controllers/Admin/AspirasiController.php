<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aspirasi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AspirasiController extends Controller
{
    /**
     * Daftar aspirasi masuk + rekap, dengan pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $kategori = $request->query('kategori');

        $aspirasis = Aspirasi::query()
            ->when($q !== '', function (Builder $query) use ($q) {
                $like = '%'.$q.'%';

                $query->where(function (Builder $query) use ($like) {
                    $query->where('nama', 'like', $like)
                        ->orWhere('isi', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('kode', 'like', $like);
                });
            })
            ->when(in_array($status, Aspirasi::STATUSES, true), fn (Builder $query) => $query->where('status', $status))
            ->when(in_array($kategori, Aspirasi::KATEGORI, true), fn (Builder $query) => $query->where('kategori', $kategori))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Rekap status dalam 1 query conditional aggregate.
        $rekapRow = Aspirasi::query()
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'baru' THEN 1 ELSE 0 END) as baru,
                SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
                SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai
            ")
            ->first();

        $rekap = [
            'total' => (int) $rekapRow->total,
            'baru' => (int) $rekapRow->baru,
            'diproses' => (int) $rekapRow->diproses,
            'selesai' => (int) $rekapRow->selesai,
        ];

        return view('admin.aspirasi.index', [
            'aspirasis' => $aspirasis,
            'rekap' => $rekap,
            'q' => $q,
            'status' => in_array($status, Aspirasi::STATUSES, true) ? $status : null,
            'kategori' => in_array($kategori, Aspirasi::KATEGORI, true) ? $kategori : null,
        ]);
    }

    /**
     * Formulir detail: ubah status dan beri tanggapan.
     */
    public function edit(Aspirasi $aspirasi): View
    {
        return view('admin.aspirasi.edit', compact('aspirasi'));
    }

    /**
     * Simpan status penanganan dan tanggapan pengurus.
     */
    public function update(Request $request, Aspirasi $aspirasi): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Aspirasi::STATUSES)],
            'tanggapan' => 'nullable|string|max:2000',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status aspirasi tidak dikenali.',
            'tanggapan.max' => 'Tanggapan maksimal 2000 karakter.',
        ]);

        $tanggapan = trim($validated['tanggapan'] ?? '');

        $aspirasi->update([
            'status' => $validated['status'],
            'tanggapan' => $tanggapan !== '' ? $tanggapan : null,
            'tanggapan_at' => $tanggapan !== '' ? now() : null,
        ]);

        return redirect()
            ->route('admin.aspirasi.index')
            ->with('success', 'Aspirasi berhasil diperbarui.');
    }

    /**
     * Hapus aspirasi.
     */
    public function destroy(Aspirasi $aspirasi): RedirectResponse
    {
        $aspirasi->delete();

        return redirect()
            ->route('admin.aspirasi.index')
            ->with('success', 'Aspirasi berhasil dihapus.');
    }
}

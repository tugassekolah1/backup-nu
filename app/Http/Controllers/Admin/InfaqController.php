<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infaq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InfaqController extends Controller
{
    /**
     * Daftar transaksi infaq + rekap total, dengan filter status dan pencarian.
     */
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $status = $request->query('status');
        $arah = $request->query('arah');

        $infaqs = Infaq::query()
            ->when(trim($q) !== '', function (Builder $query) use ($q) {
                $like = '%'.trim($q).'%';

                $query->where(function (Builder $query) use ($like) {
                    $query->where('kode_transaksi', 'like', $like)
                        ->orWhere('nama_donatur', 'like', $like)
                        ->orWhere('penanggung_jawab', 'like', $like)
                        ->orWhere('kategori', 'like', $like)
                        ->orWhere('no_hp', 'like', $like);
                });
            })
            ->when(in_array($status, Infaq::STATUSES, true), fn (Builder $query) => $query->where('status', $status))
            ->when(in_array($arah, Infaq::ARAH, true), fn (Builder $query) => $query->where('arah', $arah))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Rekap saldo dalam 1 query conditional aggregate (bukan 6 query terpisah).
        $rekapRow = Infaq::query()
            ->selectRaw("
                SUM(CASE WHEN arah = 'masuk' AND status = 'lunas' THEN nominal ELSE 0 END) as total_masuk,
                SUM(CASE WHEN arah = 'keluar' AND status = 'lunas' THEN nominal ELSE 0 END) as total_keluar,
                SUM(CASE WHEN arah = 'masuk' AND status = 'lunas' AND paid_at >= ? AND paid_at <= ? THEN nominal ELSE 0 END) as hari_ini,
                SUM(CASE WHEN arah = 'masuk' AND status = 'lunas' AND paid_at >= ? AND paid_at <= ? THEN nominal ELSE 0 END) as bulan_ini,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
            ", [
                today()->startOfDay(), today()->endOfDay(),
                now()->startOfMonth(), now()->endOfMonth(),
            ])
            ->first();

        $totalMasuk = (int) $rekapRow->total_masuk;
        $totalKeluar = (int) $rekapRow->total_keluar;

        $rekap = [
            'total_lunas' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'saldo' => $totalMasuk - $totalKeluar,
            'hari_ini' => (int) $rekapRow->hari_ini,
            'bulan_ini' => (int) $rekapRow->bulan_ini,
            'pending' => (int) $rekapRow->pending,
        ];

        return view('admin.infaq.index', [
            'infaqs' => $infaqs,
            'rekap' => $rekap,
            'q' => $q,
            'status' => in_array($status, Infaq::STATUSES, true) ? $status : null,
            'arah' => in_array($arah, Infaq::ARAH, true) ? $arah : null,
        ]);
    }

    public function create(Request $request)
    {
        $arah = $request->query('arah', 'masuk');

        if (! in_array($arah, Infaq::ARAH, true)) {
            $arah = 'masuk';
        }

        $totalMasuk = (float) Infaq::where('arah', 'masuk')->where('status', 'lunas')->sum('nominal');
        $totalKeluar = (float) Infaq::where('arah', 'keluar')->where('status', 'lunas')->sum('nominal');

        return view('admin.infaq.create', [
            'arah' => $arah,
            'saldoKas' => $totalMasuk - $totalKeluar,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateTransaksi($request);

        $validated['kode_transaksi'] = $this->generateKode();
        $validated['status'] = $validated['status'] ?? 'pending';
        $validated['arah'] = in_array($request->input('arah'), Infaq::ARAH, true) ? $request->input('arah') : ($validated['arah'] ?? 'masuk');
        $validated['nama_donatur'] = ($validated['nama_donatur'] ?? '') ?: ($validated['arah'] === 'keluar' ? 'Kas Keluar' : 'Hamba Allah');
        $validated['kategori'] = $validated['arah'] === 'keluar' ? ($validated['kategori'] ?? null) : null;
        $validated['penanggung_jawab'] = $validated['arah'] === 'keluar' ? ($validated['penanggung_jawab'] ?? null) : null;
        $validated['no_hp'] = $validated['arah'] === 'masuk' ? ($validated['no_hp'] ?? null) : null;
        $validated['paid_at'] = $this->resolvePaidAt($validated['status'], $validated['paid_at'] ?? null);

        $this->ensureSaldoCukup($validated['arah'], $validated['status'], (float) $validated['nominal']);

        if ($request->hasFile('bukti')) {
            $validated['bukti_path'] = $request->file('bukti')->store('infaq-bukti', 'supabase');
        }
        unset($validated['bukti']);

        Infaq::create($validated);

        return redirect()->route('admin.infaq.index')->with('success', 'Transaksi infaq berhasil dicatat');
    }

    public function edit(Infaq $infaq)
    {
        $totalMasuk = (float) Infaq::where('arah', 'masuk')->where('status', 'lunas')->sum('nominal');
        $totalKeluar = (float) Infaq::where('arah', 'keluar')->where('status', 'lunas')->sum('nominal');
        $saldoKas = $totalMasuk - $totalKeluar;

        // Saat edit, hitung saldo tersedia seolah transaksi ini belum berlaku (agar peringatan lebih akurat).
        if (($infaq->status ?? null) === 'lunas') {
            if (($infaq->arah ?? 'masuk') === 'keluar') {
                $saldoKas += (float) $infaq->nominal;
            } else {
                $saldoKas -= (float) $infaq->nominal;
            }
        }

        return view('admin.infaq.edit', [
            'infaq' => $infaq,
            'arah' => $infaq->arah ?: 'masuk',
            'saldoKas' => $saldoKas,
        ]);
    }

    public function update(Request $request, Infaq $infaq)
    {
        $validated = $this->validateTransaksi($request, $infaq);

        $validated['status'] = $validated['status'] ?? 'pending';
        $validated['arah'] = in_array($request->input('arah'), Infaq::ARAH, true) ? $request->input('arah') : ($infaq->arah ?: 'masuk');
        $validated['nama_donatur'] = ($validated['nama_donatur'] ?? '') ?: ($validated['arah'] === 'keluar' ? 'Kas Keluar' : 'Hamba Allah');
        $validated['kategori'] = $validated['arah'] === 'keluar' ? ($validated['kategori'] ?? null) : null;
        $validated['penanggung_jawab'] = $validated['arah'] === 'keluar' ? ($validated['penanggung_jawab'] ?? null) : null;
        $validated['no_hp'] = $validated['arah'] === 'masuk' ? ($validated['no_hp'] ?? null) : null;
        $validated['paid_at'] = $this->resolvePaidAt($validated['status'], $validated['paid_at'] ?? null);

        $this->ensureSaldoCukup($validated['arah'], $validated['status'], (float) $validated['nominal'], $infaq);

        if ($request->hasFile('bukti')) {
            if ($infaq->bukti_path) {
                Storage::disk('supabase')->delete($infaq->bukti_path);
            }
            $validated['bukti_path'] = $request->file('bukti')->store('infaq-bukti', 'supabase');
        } else {
            unset($validated['bukti_path']);
        }
        unset($validated['bukti']);

        $infaq->update($validated);

        return redirect()->route('admin.infaq.index')->with('success', 'Transaksi infaq diperbarui');
    }

    public function destroy(Infaq $infaq)
    {
        if ($infaq->bukti_path) {
            Storage::disk('supabase')->delete($infaq->bukti_path);
        }

        $infaq->delete();

        return redirect()->route('admin.infaq.index')->with('success', 'Transaksi infaq dihapus');
    }

    /**
     * Tandai transaksi pending menjadi lunas.
     */
    public function markLunas(Infaq $infaq)
    {
        if ($infaq->status === 'lunas') {
            return redirect()->back()->with('success', 'Transaksi ini sudah lunas.');
        }

        if ($infaq->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya transaksi berstatus pending yang bisa ditandai lunas.');
        }

        if (($infaq->arah ?? 'masuk') === 'keluar') {
            $totalMasuk = (float) Infaq::where('arah', 'masuk')->where('status', 'lunas')->sum('nominal');
            $totalKeluar = (float) Infaq::where('arah', 'keluar')->where('status', 'lunas')->sum('nominal');
            $saldo = $totalMasuk - $totalKeluar;

            if ((float) $infaq->nominal > $saldo) {
                return redirect()->back()->with('error', 'Tidak bisa ditandai lunas: nominal pengeluaran melebihi saldo kas tersedia (Rp '.number_format($saldo, 0, ',', '.').'). Saldo tidak boleh minus.');
            }
        }

        $infaq->update([
            'status' => 'lunas',
            'paid_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Transaksi ditandai lunas');
    }

    /**
     * Batalkan transaksi yang masih berstatus pending.
     */
    public function markBatal(Infaq $infaq)
    {
        if ($infaq->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya transaksi berstatus pending yang bisa dibatalkan.');
        }

        $infaq->update([
            'status' => 'dibatalkan',
            'paid_at' => null,
        ]);

        return redirect()->back()->with('success', 'Transaksi dibatalkan');
    }

    /**
     * Aturan validasi untuk store dan update.
     */
    private function validateTransaksi(Request $request, ?Infaq $infaq = null): array
    {
        // Normalisasi format ribuan (mis. "50.000") agar lolos validasi numeric.
        if ($request->filled('nominal')) {
            $request->merge([
                'nominal' => preg_replace('/[^\d]/', '', (string) $request->input('nominal')),
            ]);
        }

        $isKeluar = $request->input('arah') === 'keluar';

        // Khusus pengeluaran: semua input wajib diisi. Bukti wajib ada (baru atau sudah tersimpan saat edit).
        $buktiRule = ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        if ($isKeluar) {
            array_unshift($buktiRule, ($infaq && $infaq->bukti_path) ? 'nullable' : 'required');
        } else {
            array_unshift($buktiRule, 'nullable');
        }

        return $request->validate([
            'nominal' => 'required|numeric|min:1',
            'metode_pembayaran' => ['required', Rule::in(Infaq::METODE)],
            'nama_donatur' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'status' => ['required', Rule::in(Infaq::STATUSES)],
            'arah' => ['nullable', Rule::in(Infaq::ARAH)],
            'kategori' => [Rule::requiredIf($isKeluar), 'nullable', Rule::in(Infaq::KATEGORI_KELUAR)],
            'penanggung_jawab' => [Rule::requiredIf($isKeluar), 'nullable', 'string', 'max:100'],
            'bukti' => $buktiRule,
            'paid_at' => [Rule::requiredIf($isKeluar), 'nullable', 'date', 'before_or_equal:today'],
            'catatan' => [Rule::requiredIf($isKeluar), 'nullable', 'string'],
        ], [
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal minimal Rp 1.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran tidak dikenali.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status transaksi tidak dikenali.',
            'kategori.required' => 'Kategori penggunaan wajib dipilih.',
            'kategori.in' => 'Kategori penggunaan tidak dikenali.',
            'penanggung_jawab.required' => 'Penanggung jawab wajib diisi.',
            'catatan.required' => 'Keperluan/keterangan wajib diisi.',
            'bukti.required' => 'Bukti/kwitansi wajib diunggah.',
            'bukti.image' => 'Bukti harus berupa gambar (JPG/PNG/WEBP).',
            'bukti.max' => 'Ukuran bukti maksimal 2 MB.',
            'paid_at.required' => 'Tanggal wajib diisi.',
            'paid_at.date' => 'Tanggal tidak valid.',
            'paid_at.before_or_equal' => 'Tanggal tidak boleh setelah hari ini.',
        ]);
    }

    /**
     * paid_at hanya berlaku untuk status lunas; jika kosong, pakai waktu sekarang.
     */
    private function resolvePaidAt(string $status, $paidAt = null)
    {
        if ($status !== 'lunas') {
            return null;
        }

        return $paidAt ?: now();
    }

    /**
     * Pengeluaran lunas tidak boleh membuat saldo kas menjadi minus.
     */
    private function ensureSaldoCukup(string $arah, string $status, float $nominal, ?Infaq $ignore = null): void
    {
        if ($arah !== 'keluar' || $status !== 'lunas') {
            return;
        }

        $totalMasuk = (float) Infaq::where('arah', 'masuk')->where('status', 'lunas')->sum('nominal');
        $totalKeluar = (float) Infaq::where('arah', 'keluar')->where('status', 'lunas')->sum('nominal');
        $saldo = $totalMasuk - $totalKeluar;

        if ($ignore && $ignore->status === 'lunas') {
            if ($ignore->arah === 'keluar') {
                $saldo += (float) $ignore->nominal;
            } elseif ($ignore->arah === 'masuk') {
                $saldo -= (float) $ignore->nominal;
            }
        }

        if ($nominal > $saldo) {
            throw ValidationException::withMessages([
                'nominal' => 'Nominal pengeluaran melebihi saldo kas tersedia (Rp '.number_format($saldo, 0, ',', '.').'). Saldo tidak boleh minus.',
            ]);
        }
    }

    private function generateKode(): string
    {
        do {
            $kode = 'INF-'.strtoupper(Str::random(8));
        } while (Infaq::where('kode_transaksi', $kode)->exists());

        return $kode;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Infaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InfaqController extends Controller
{
    // Halaman Form Infak
    public function index()
    {
        $totalMasuk = Infaq::where('arah', 'masuk')->where('status', 'lunas')->sum('nominal');
        $totalKeluar = Infaq::where('arah', 'keluar')->where('status', 'lunas')->sum('nominal');
        $totalInfaq = $totalMasuk;
        $totalDonatur = Infaq::where('arah', 'masuk')->where('status', 'lunas')->count();
        $infaqTerbaru = Infaq::where('arah', 'masuk')->where('status', 'lunas')->latest()->take(5)->get();
        $kasKeluar = Infaq::where('arah', 'keluar')->where('status', 'lunas')->latest('paid_at')->take(10)->get();
        $saldo = $totalMasuk - $totalKeluar;

        return view('infaq.index', compact('totalInfaq', 'totalMasuk', 'totalKeluar', 'saldo', 'totalDonatur', 'infaqTerbaru', 'kasKeluar'));
    }

    // Proses Simpan Infak (Pending)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nominal' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:transfer_bank,qris,tunai',
            'nama_donatur' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'catatan' => 'nullable|string|max:500',
        ]);

        $infaq = Infaq::create([
            'kode_transaksi' => 'INF-'.strtoupper(Str::random(8)),
            'nama_donatur' => $validated['nama_donatur'] ?? 'Hamba Allah',
            'no_hp' => $validated['no_hp'] ?? null,
            'nominal' => $validated['nominal'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'catatan' => $validated['catatan'] ?? null,
            'arah' => 'masuk',
            'status' => 'pending',
        ]);

        return redirect()->route('infaq.checkout', $infaq->kode_transaksi);
    }

    // Halaman Instruksi Pembayaran & Simulasi
    public function checkout($kode)
    {
        $infaq = Infaq::where('kode_transaksi', $kode)->firstOrFail();

        // QR dummy untuk metode QRIS (payload contoh, bukan QRIS asli).
        $qrCode = null;
        if ($infaq->metode_pembayaran === 'qris' && $infaq->status === 'pending' && ! $infaq->bukti_path) {
            $payload = implode('|', [
                'DUMMY-QRIS',
                'NU-BANJARANYAR',
                $infaq->kode_transaksi,
                (int) $infaq->nominal,
            ]);
            $qrCode = QrCode::size(220)->generate($payload);
        }

        return view('infaq.checkout', compact('infaq', 'qrCode'));
    }

    /**
     * Donatur menekan "Saya Sudah Bayar" sekaligus mengunggah bukti transfer.
     * Status tetap pending sampai admin menyetujui (tandai lunas).
     */
    public function confirmProof(Request $request, $kode)
    {
        $infaq = Infaq::where('kode_transaksi', $kode)->firstOrFail();

        if ($infaq->status !== 'pending') {
            return redirect()->route('infaq.checkout', $kode)
                ->with('error', 'Transaksi ini sudah tidak membutuhkan konfirmasi.');
        }

        $validated = $request->validate([
            'bukti' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'bukti.required' => 'Lampirkan foto bukti pembayaran terlebih dahulu.',
            'bukti.image' => 'Bukti harus berupa gambar (JPG/PNG).',
            'bukti.max' => 'Ukuran bukti maksimal 2 MB.',
        ]);

        if ($infaq->bukti_path) {
            Storage::disk('public')->delete($infaq->bukti_path);
        }

        $infaq->update([
            'bukti_path' => $request->file('bukti')->store('infaq-bukti', 'public'),
        ]);

        return redirect()->route('infaq.checkout', $kode)
            ->with('success', 'Bukti pembayaran terkirim. Admin akan memverifikasi, lalu status berubah lunas.');
    }

    // SIMULASI: Ubah Status ke Lunas
    public function simulatePayment($kode)
    {
        // Pengaman berlapis: rute ini sudah hanya didaftarkan di environment
        // local/testing, tapi method tetap menolak dijalankan di lingkungan lain.
        abort_unless(app()->environment(['local', 'testing']), 404);

        $infaq = Infaq::where('kode_transaksi', $kode)->firstOrFail();

        $infaq->update([
            'status' => 'lunas',
            'paid_at' => now(),
        ]);

        return redirect()->route('infaq.success', $infaq->kode_transaksi)
            ->with('success', 'Simulasi Pembayaran Berhasil!');
    }

    // Halaman Pembayaran Berhasil
    public function success($kode)
    {
        $infaq = Infaq::where('kode_transaksi', $kode)->firstOrFail();

        if ($infaq->status !== 'lunas') {
            return redirect()->route('infaq.checkout', $kode);
        }

        return view('infaq.success', compact('infaq'));
    }
}

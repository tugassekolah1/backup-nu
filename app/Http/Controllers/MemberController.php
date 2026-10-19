<?php

namespace App\Http\Controllers;

use App\Models\NuMember;
use App\Models\Pengurus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        if (! in_array($statusFilter, ['all', 'pending', 'approved'], true)) {
            $statusFilter = 'all';
        }

        $banomFilter = $request->query('banom', 'all');
        $banomCodes = NuMember::organisasiCodes();
        if ($banomFilter !== 'all' && $banomFilter !== 'none' && ! in_array($banomFilter, $banomCodes, true)) {
            $banomFilter = 'all';
        }

        // Search query berdasarkan NIK atau Nama
        $search = trim($request->query('search', ''));

        $members = NuMember::query()
            ->when($statusFilter !== 'all', fn ($q) => $q->withRegistrationState($statusFilter === 'approved' ? 'accepted' : 'pending'))
            ->when($banomFilter === 'none', fn ($q) => $q->whereNull('organisasi'))
            ->when($banomFilter !== 'all' && $banomFilter !== 'none', fn ($q) => $q->where('organisasi', $banomFilter))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('nik', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Hitung jumlah anggota per banom dalam 1 query agregat.
        $jumlahPerOrganisasi = NuMember::query()
            ->selectRaw('organisasi, COUNT(*) as total')
            ->groupBy('organisasi')
            ->get();

        $banomCounts = array_fill_keys(NuMember::organisasiCodes(), 0);
        $banomCounts['none'] = 0;

        foreach ($jumlahPerOrganisasi as $row) {
            if ($row->organisasi === null) {
                $banomCounts['none'] = (int) $row->total;
            } else {
                $banomCounts[$row->organisasi] = (int) $row->total;
            }
        }

        return view('admin.members.index', compact('members', 'statusFilter', 'banomFilter', 'banomCounts', 'search'));
    }

    public function create()
    {
        return view('admin.members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|digits:16|unique:nu_members,nik',
            'full_name' => 'required|max:255',
            'phone' => 'required|max:20',
            'gender' => 'required|in:L,P',
            'organisasi' => ['nullable', 'string', Rule::in(NuMember::organisasiCodes())],
            'address' => 'required',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('members-photo', 'public');
        }

        // Anggota yang ditambahkan langsung oleh admin dianggap sudah terverifikasi.
        NuMember::create([
            'nik' => $validated['nik'],
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'organisasi' => $validated['organisasi'] ?? null,
            'label_organisasi' => isset($validated['organisasi']) ? Pengurus::BANOMS[$validated['organisasi']]['label'] : null,
            'address' => $validated['address'],
            'photo' => $photoPath,
            'registration_status' => 'accepted',
            'status' => 'active',
            'member_card_no' => $this->generateCardNumber(),
        ]);

        return redirect()->route('members.index')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(NuMember $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, NuMember $member)
    {
        $validated = $request->validate([
            'nik' => 'required|digits:16|unique:nu_members,nik,'.$member->id,
            'full_name' => 'required|max:255',
            'phone' => 'required|max:20',
            'gender' => 'required|in:L,P',
            'organisasi' => ['nullable', 'string', Rule::in(NuMember::organisasiCodes())],
            'address' => 'required',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }
            $validated['photo'] = $request->file('photo')->store('members-photo', 'public');
        }

        $validated['label_organisasi'] = ! empty($validated['organisasi'])
            ? Pengurus::BANOMS[$validated['organisasi']]['label']
            : null;

        $member->update($validated);

        return redirect()->route('members.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(NuMember $member)
    {
        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }
        $member->delete();

        return redirect()->route('members.index')->with('success', 'Anggota berhasil dihapus.');
    }

    /**
     * Daftar permintaan pendaftaran anggota (admin).
     *
     * Mendukung filter status (menunggu/diterima/ditolak) dan pencarian
     * berdasarkan nama, NIK, atau nomor telepon.
     */
    public function registrationRequests(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        if (! in_array($statusFilter, ['all', 'pending', 'accepted', 'rejected'], true)) {
            $statusFilter = 'all';
        }

        $search = trim((string) $request->query('q', ''));

        $members = NuMember::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($statusFilter !== 'all', function ($query) use ($statusFilter) {
                $query->where(function ($q) use ($statusFilter) {
                    match ($statusFilter) {
                        'pending' => $q->where('registration_status', 'pending')
                            ->orWhere(fn ($qq) => $qq->whereNull('registration_status')->where('status', '!=', 'active')),
                        'accepted' => $q->where('registration_status', 'accepted')
                            ->orWhere(fn ($qq) => $qq->whereNull('registration_status')->where('status', 'active')),
                        'rejected' => $q->where('registration_status', 'rejected'),
                    };
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.members.requests', compact('members', 'statusFilter', 'search'));
    }

    /**
     * Terima permintaan pendaftaran -> anggota aktif (AJAX/JSON).
     */
    public function acceptRegistration(NuMember $member)
    {
        if ($member->registrationState() !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya permintaan berstatus Menunggu yang dapat diterima.',
            ], 422);
        }

        $member->update([
            'registration_status' => 'accepted',
            'rejection_reason' => null,
            'status' => 'active',
            'member_card_no' => $member->member_card_no ?? $this->generateCardNumber(),
        ]);

        return $this->registrationResponse(
            $member,
            'Pendaftaran diterima. '.$member->full_name.' kini berstatus anggota aktif.'
        );
    }

    /**
     * Tolak permintaan pendaftaran beserta alasan penolakan (AJAX/JSON).
     */
    public function rejectRegistration(Request $request, NuMember $member)
    {
        if ($member->registrationState() !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya permintaan berstatus Menunggu yang dapat ditolak.',
            ], 422);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $member->update([
            'registration_status' => 'rejected',
            'rejection_reason' => $validated['reason'],
        ]);

        return $this->registrationResponse(
            $member,
            'Pendaftaran '.$member->full_name.' ditolak.'
        );
    }

    /**
     * Respons JSON standar aksi permintaan pendaftaran,
     * berisi HTML sel status & aksi agar UI bisa diperbarui tanpa refresh.
     */
    private function registrationResponse(NuMember $member, string $message)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'html' => [
                'status' => view('admin.members.requests.status-cell', ['member' => $member])->render(),
                'actions' => view('admin.members.requests.actions-cell', ['member' => $member])->render(),
            ],
        ]);
    }

    /**
     * Halaman "Kartu Anggota" (pusat status & cetak kartu dalam satu halaman).
     *
     * Menampilkan status pengajuan (belum ajukan / menunggu / ditolak /
     * disetujui) dan, bila disetujui, langsung preview kartu 3D beserta
     * tombol cetak & download — tanpa perlu membuka halaman terpisah.
     */
    public function memberCard(Request $request)
    {
        $member = null;
        $qrCode = null;
        $notVerified = false;

        if ($request->filled('nik')) {
            $member = NuMember::where('nik', $request->nik)->first();

            // Verifikasi faktor kedua: nomor HP yang dipakai saat mendaftar.
            // Tanpa ini, siapa pun yang mengetahui NIK orang lain dapat melihat
            // nama, nomor HP, dan nomor kartu anggota yang bersangkutan.
            if ($member && ! $this->nomorHpCocok($member->phone, $request->input('phone'))) {
                $member = null;
                $notVerified = true;
            }

            if ($member && $member->status === 'active' && $member->member_card_no) {
                $qrData = 'NIK: '.$member->nik.' | CARD: '.$member->member_card_no;
                $qrCode = QrCode::size(80)->generate($qrData);
            }
        }

        return view('members-card', compact('member', 'qrCode', 'notVerified'));
    }

    /**
     * Membandingkan nomor HP dengan format yang dimaklumi: hanya digit,
     * awalan "62" disamakan dengan "0", sehingga 0812… = 62812… .
     */
    private function nomorHpCocok(?string $tersimpan, ?string $diisi): bool
    {
        $normalisasi = static function (?string $nomor): string {
            $digit = preg_replace('/\D+/', '', (string) $nomor) ?? '';

            if (str_starts_with($digit, '62')) {
                $digit = '0'.substr($digit, 2);
            }

            return $digit;
        };

        $nomorTersimpan = $normalisasi($tersimpan);
        $nomorDiisi = $normalisasi($diisi);

        if ($nomorTersimpan === '' || $nomorDiisi === '') {
            return false;
        }

        return $nomorTersimpan === $nomorDiisi;
    }

    /**
     * RUTE LAMA /cek-kartu — diarahkan ke halaman Kartu Anggota
     * agar tautan lama tetap kompatibel.
     */
    public function searchCard(Request $request)
    {
        return redirect()->route('members.card', $request->query());
    }

    /**
     * RUTE LAMA /cek-status — diarahkan ke halaman Kartu Anggota
     * agar tautan lama tetap kompatibel.
     */
    public function statusCheck(Request $request)
    {
        return redirect()->route('members.card', $request->query());
    }

    /**
     * Cetak kartu anggota
     */
    public function printCard($id)
    {
        $member = NuMember::findOrFail($id);

        if ($member->status !== 'active') {
            return back()->with('error', 'Kartu belum dapat dicetak karena pendaftaran belum disetujui admin.');
        }

        // Generate QR Code berisi NIK & Nomor Kartu
        $qrData = 'NIK: '.$member->nik.' | CARD: '.$member->member_card_no;
        $qrCode = QrCode::size(90)->generate($qrData);

        return view('admin.members.print-card', compact('member', 'qrCode'));
    }

    /**
     * Cetak KTA masal (Kartu Tanda Anggota).
     *
     * Menerima salah satu dari:
     * - ids[]  : array ID anggota terpilih (bulk action dari tabel)
     * - banom : kode organisasi (cetak berjamaah satu banom) atau "none"
     * - tanpa keduanya: seluruh anggota aktif
     */
    public function printKtaBulk(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []), fn ($id) => is_numeric($id));
        $banom = $request->query('banom');

        $query = NuMember::where('status', 'active');

        if ($ids !== []) {
            $query->whereIn('id', $ids);
        } elseif ($banom !== null && $banom !== '') {
            $validCodes = NuMember::organisasiCodes();

            if ($banom === 'none') {
                $query->whereNull('organisasi');
            } elseif (in_array($banom, $validCodes, true)) {
                $query->where('organisasi', $banom);
            } else {
                return back()->with('error', 'Banom tidak valid.');
            }
        }

        $members = $query->get();

        if ($members->isEmpty()) {
            return back()->with('error', 'Tidak ada anggota aktif untuk dicetak.');
        }

        // Generate QR untuk setiap member
        $membersWithQr = $members->map(function ($member) {
            $qrData = 'NIK: '.$member->nik.' | CARD: '.$member->member_card_no;

            return [
                'member' => $member,
                'qrCode' => QrCode::size(70)->generate($qrData),
            ];
        });

        return view('admin.members.cetak-kta-bulk', compact('membersWithQr'));
    }

    /**
     * Download KTA individual dalam format PDF/printable
     */
    public function downloadKta($id)
    {
        $member = NuMember::findOrFail($id);

        if ($member->status !== 'active') {
            return back()->with('error', 'Kartu belum dapat diunduh karena pendaftaran belum disetujui admin.');
        }

        $qrData = 'NIK: '.$member->nik.' | CARD: '.$member->member_card_no;
        $qrCode = QrCode::size(90)->generate($qrData);

        return view('admin.members.download-kta', compact('member', 'qrCode'));
    }

    private function generateCardNumber(): string
    {
        $year = now()->format('Y');
        $lastNumber = NuMember::where('member_card_no', 'like', "NU-{$year}-%")
            ->orderByDesc('member_card_no')
            ->value('member_card_no');

        $nextSequence = 1;
        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber, -3);
            $nextSequence = $lastSequence + 1;
        }

        return sprintf('NU-%s-%03d', $year, $nextSequence);
    }

    public function registerForm()
    {
        return view('members-register');
    }

    /**
     * Proses pendaftaran anggota baru.
     *
     * Tidak ada pembayaran online: pengajuan langsung berstatus "menunggu"
     * dan diaktifkan oleh admin melalui halaman Permintaan Pendaftaran.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|digits:16|unique:nu_members,nik',
            'full_name' => 'required|max:255',
            'phone' => 'required|max:20',
            'gender' => 'required|in:L,P',
            'organisasi' => ['nullable', 'string', Rule::in(NuMember::organisasiCodes())],
            'address' => 'required',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('members-photo', 'public');
        }

        NuMember::create([
            'nik' => $validated['nik'],
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'organisasi' => $validated['organisasi'] ?? null,
            'label_organisasi' => isset($validated['organisasi']) ? Pengurus::BANOMS[$validated['organisasi']]['label'] : null,
            'address' => $validated['address'],
            'photo' => $photoPath,
            'registration_status' => 'pending',
            'status' => 'pending_payment',
        ]);

        return redirect()
            ->route('members.card', ['nik' => $validated['nik'], 'phone' => $validated['phone']])
            ->with('success', 'Pendaftaran berhasil! Pengajuan Anda sedang menunggu verifikasi admin. Pantau statusnya di halaman Kartu Anggota.');
    }
}

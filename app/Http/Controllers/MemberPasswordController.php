<?php

namespace App\Http\Controllers;

use App\Models\NuMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MemberPasswordController extends Controller
{
    /**
     * Tampilkan formulir "lupa password".
     *
     * Anggota memverifikasi identitasnya dengan NIK + nomor HP yang
     * terdaftar (pola yang sama dengan halaman Kartu Anggota), lalu
     * langsung menentukan kata sandi baru — tanpa perlu email.
     */
    public function create(): View
    {
        return view('auth.member-forgot-password');
    }

    /**
     * Setel ulang kata sandi anggota setelah NIK + nomor HP cocok.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'digits:16'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $member = NuMember::where('nik', $validated['nik'])->first();

        if (! $member || ! $member->nomorHpCocok($validated['phone'])) {
            throw ValidationException::withMessages([
                'nik' => 'NIK dan nomor HP tidak cocok dengan data anggota kami.',
            ]);
        }

        if ($member->status !== 'active') {
            throw ValidationException::withMessages([
                'nik' => $member->registrationState() === 'rejected'
                    ? 'Pendaftaran Anda ditolak. Hubungi admin untuk informasi lebih lanjut.'
                    : 'Akun Anda belum aktif. Pendaftaran masih menunggu persetujuan admin.',
            ]);
        }

        $member->update(['password' => $validated['password']]);

        return redirect()
            ->route('member.login')
            ->with('status', 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru Anda.');
    }
}

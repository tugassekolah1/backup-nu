<?php

namespace App\Http\Controllers;

use App\Models\NuMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MemberLoginController extends Controller
{
    /**
     * Tampilkan formulir masuk anggota (NIK + kata sandi).
     */
    public function create(): View
    {
        return view('auth.member-login');
    }

    /**
     * Proses masuk anggota.
     *
     * Hanya anggota aktif yang boleh masuk. Pesan kegagalan dibuat
     * spesifik agar pendaftar baru tahu status pengajuannya.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'nik' => ['required', 'string', 'digits:16'],
            'password' => ['required', 'string'],
        ]);

        $member = NuMember::where('nik', $credentials['nik'])->first();

        if (! $member || blank($member->password) || ! Hash::check($credentials['password'], $member->password)) {
            throw ValidationException::withMessages([
                'nik' => 'NIK atau kata sandi salah.',
            ]);
        }

        if ($member->status !== 'active') {
            throw ValidationException::withMessages([
                'nik' => $member->registrationState() === 'rejected'
                    ? 'Pendaftaran Anda ditolak. Hubungi admin untuk informasi lebih lanjut.'
                    : 'Akun Anda belum aktif. Pendaftaran masih menunggu persetujuan admin.',
            ]);
        }

        Auth::guard('member')->login($member);

        $request->session()->regenerate();

        return redirect()->intended(route('member.profile.edit'));
    }

    /**
     * Keluar dari sesi anggota tanpa mengganggu sesi admin (guard "web").
     * Hanya kunci sesi milik guard member yang dibersihkan.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('member')->logout();

        $request->session()->regenerateToken();

        return redirect()
            ->route('member.login')
            ->with('status', 'Anda telah keluar. Silakan masuk kembali bila diperlukan.');
    }
}

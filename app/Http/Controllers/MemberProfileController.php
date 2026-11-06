<?php

namespace App\Http\Controllers;

use App\Models\NuMember;
use App\Models\Pengurus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberProfileController extends Controller
{
    /**
     * Halaman "Data Diri Anggota": anggota mengedit sendiri profilnya.
     */
    public function edit(): View
    {
        return view('members-profile', [
            'member' => Auth::guard('member')->user(),
        ]);
    }

    /**
     * Perbarui data diri anggota yang sedang masuk.
     *
     * NIK sengaja tidak dapat diubah karena dipakai sebagai identitas
     * login dan isi QR pada kartu anggota. Nomor kartu tetap urusan admin.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var NuMember $member */
        $member = Auth::guard('member')->user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'in:L,P'],
            'organisasi' => ['nullable', 'string', Rule::in(NuMember::organisasiCodes())],
            'address' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                Storage::disk('supabase')->delete($member->photo);
            }

            $validated['photo'] = $request->file('photo')->store('members-photo', 'supabase');
        }

        $validated['label_organisasi'] = ! empty($validated['organisasi'])
            ? Pengurus::BANOMS[$validated['organisasi']]['label']
            : null;

        $member->update($validated);

        return redirect()
            ->route('member.profile.edit')
            ->with('success', 'Data diri berhasil diperbarui. Perubahan langsung tampil pada kartu anggota Anda.');
    }
}

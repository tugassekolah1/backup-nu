<?php

use App\Models\NuMember;
use App\Models\Pengurus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

function memberForAuthTests(array $overrides = []): NuMember
{
    return NuMember::create(array_merge([
        'nik' => '3300000000000401',
        'full_name' => 'Anggota Aktif',
        'phone' => '081234567891',
        'gender' => 'L',
        'address' => 'Banjaranyar',
        'status' => 'active',
        'registration_status' => 'accepted',
        'member_card_no' => 'NU-2026-401',
        'organisasi' => 'pac_ansor',
        'label_organisasi' => Pengurus::BANOMS['pac_ansor']['label'],
        'password' => 'sandi-rahasia',
    ], $overrides));
}

test('member login page shows the sign in form', function () {
    $this->get(route('member.login'))
        ->assertOk()
        ->assertSee('Masuk Anggota')
        ->assertSee('name="nik"', false)
        ->assertSee('name="password"', false)
        ->assertSee(route('member.password.request'), false);
});

test('an active member can log in with nik and password', function () {
    $member = memberForAuthTests();

    $this->post(route('member.login'), [
        'nik' => $member->nik,
        'password' => 'sandi-rahasia',
    ])->assertRedirect(route('member.profile.edit'))
        ->assertSessionHasNoErrors();

    expect(Auth::guard('member')->check())->toBeTrue()
        ->and(Auth::guard('member')->id())->toBe($member->id);
});

test('a member with a wrong password cannot log in', function () {
    $member = memberForAuthTests();

    $this->from(route('member.login'))
        ->post(route('member.login'), [
            'nik' => $member->nik,
            'password' => 'salah-terus',
        ])->assertRedirect(route('member.login'))
        ->assertSessionHasErrors('nik');

    expect(Auth::guard('member')->check())->toBeFalse();
});

test('a member with a pending registration cannot log in', function () {
    $member = memberForAuthTests([
        'status' => 'pending_payment',
        'registration_status' => 'pending',
        'member_card_no' => null,
    ]);

    $this->from(route('member.login'))
        ->post(route('member.login'), [
            'nik' => $member->nik,
            'password' => 'sandi-rahasia',
        ])->assertRedirect(route('member.login'))
        ->assertSessionHasErrors('nik');

    expect(Auth::guard('member')->check())->toBeFalse();
    expect(session('errors')->get('nik')[0])->toContain('belum aktif');
});

test('a rejected member cannot log in and sees the rejection reason', function () {
    $member = memberForAuthTests([
        'status' => 'pending_payment',
        'registration_status' => 'rejected',
        'rejection_reason' => 'NIK tidak sesuai.',
        'member_card_no' => null,
    ]);

    $this->from(route('member.login'))
        ->post(route('member.login'), [
            'nik' => $member->nik,
            'password' => 'sandi-rahasia',
        ])->assertSessionHasErrors('nik');

    expect(Auth::guard('member')->check())->toBeFalse();
    expect(session('errors')->get('nik')[0])->toContain('ditolak');
});

test('guest is redirected to the member login page when opening the member profile', function () {
    $this->get(route('member.profile.edit'))->assertRedirect(route('member.login'));
});

test('the member profile page renders with a visible logout button', function () {
    $member = memberForAuthTests();

    $this->actingAs($member, 'member')
        ->get(route('member.profile.edit'))
        ->assertOk()
        ->assertSee('Data Diri Saya')
        // Tombol keluar terlihat langsung di header, ala sosmed.
        ->assertSee('title="Keluar dari akun anggota"', false)
        ->assertSee('name="_token"', false);
});

test('a member can update their own profile data', function () {
    $member = memberForAuthTests();

    $this->actingAs($member, 'member')
        ->patch(route('member.profile.update'), [
            'full_name' => 'Anggota Diperbarui',
            'phone' => '628123456789',
            'gender' => 'P',
            'organisasi' => 'pac_fatayat',
            'address' => 'Banjaranyar RT 01 RW 02',
        ])->assertRedirect(route('member.profile.edit'))
        ->assertSessionHasNoErrors();

    $member->refresh();

    expect($member->full_name)->toBe('Anggota Diperbarui')
        ->and($member->phone)->toBe('628123456789')
        ->and($member->gender)->toBe('P')
        ->and($member->organisasi)->toBe('pac_fatayat')
        ->and($member->label_organisasi)->toBe(Pengurus::BANOMS['pac_fatayat']['label'])
        ->and($member->address)->toBe('Banjaranyar RT 01 RW 02');
});

test('a member cannot change their own nik', function () {
    $member = memberForAuthTests();

    $this->actingAs($member, 'member')
        ->patch(route('member.profile.update'), [
            'nik' => '9999999999999999',
            'full_name' => 'Nama Tetap',
            'phone' => '081234567891',
            'gender' => 'L',
            'organisasi' => 'pac_ansor',
            'address' => 'Banjaranyar',
        ])->assertSessionHasNoErrors();

    expect($member->refresh()->nik)->toBe('3300000000000401');
});

test('profile update stores a new photo on the supabase disk and deletes the old one', function () {
    fakeSupabaseDisk();

    Storage::disk('supabase')->put('members-photo/foto-lama.png', 'lama');

    $member = memberForAuthTests(['photo' => 'members-photo/foto-lama.png']);

    $this->actingAs($member, 'member')
        ->patch(route('member.profile.update'), [
            'full_name' => $member->full_name,
            'phone' => $member->phone,
            'gender' => $member->gender,
            'organisasi' => $member->organisasi,
            'address' => $member->address,
            'photo' => fakePngUpload('foto-baru.png'),
        ])->assertSessionHasNoErrors();

    $member->refresh();

    expect($member->photo)->not->toBe('members-photo/foto-lama.png');

    Storage::disk('supabase')->assertExists($member->photo);
    Storage::disk('supabase')->assertMissing('members-photo/foto-lama.png');
});

test('the updated data is visible on the public card page immediately', function () {
    $member = memberForAuthTests();

    $this->actingAs($member, 'member')
        ->patch(route('member.profile.update'), [
            'full_name' => 'Nama Di Kartu Baru',
            'phone' => '081234567891',
            'gender' => 'L',
            'organisasi' => 'pac_fatayat',
            'address' => 'Banjaranyar',
        ])->assertSessionHasNoErrors();

    $this->get(route('members.card', ['nik' => $member->nik, 'phone' => $member->phone]))
        ->assertOk()
        ->assertSee('Nama Di Kartu Baru')
        ->assertSee(Pengurus::BANOMS['pac_fatayat']['label']);
});

test('member logout ends only the member session and keeps the admin session', function () {
    $admin = User::factory()->admin()->create();
    $member = memberForAuthTests();

    $this->actingAs($admin);
    $this->actingAs($member, 'member');

    $this->post(route('member.logout'))->assertRedirect(route('member.login'));

    expect(Auth::guard('member')->check())->toBeFalse()
        ->and(Auth::guard('web')->check())->toBeTrue();
});

test('forgot password form is shown', function () {
    $this->get(route('member.password.request'))
        ->assertOk()
        ->assertSee('Lupa Kata Sandi?')
        ->assertSee('name="phone"', false);
});

test('a forgotten password can be set again with nik and phone', function () {
    $member = memberForAuthTests(['phone' => '081234567891']);

    // Nomor tersimpan boleh berbeda format saat diisi (628… vs 08…).
    $this->post(route('member.password.update'), [
        'nik' => $member->nik,
        'phone' => '6281234567891',
        'password' => 'sandi-baru-kamu',
        'password_confirmation' => 'sandi-baru-kamu',
    ])->assertRedirect(route('member.login'))
        ->assertSessionHasNoErrors();

    $this->post(route('member.login'), [
        'nik' => $member->nik,
        'password' => 'sandi-baru-kamu',
    ])->assertRedirect(route('member.profile.edit'));

    expect(Auth::guard('member')->check())->toBeTrue();
});

test('forgot password rejects a nik that does not match the phone', function () {
    $member = memberForAuthTests();

    $this->from(route('member.password.request'))
        ->post(route('member.password.update'), [
            'nik' => $member->nik,
            'phone' => '089999999999',
            'password' => 'sandi-baru-kamu',
            'password_confirmation' => 'sandi-baru-kamu',
        ])->assertRedirect(route('member.password.request'))
        ->assertSessionHasErrors('nik');

    // Kata sandi lama tidak berubah karena verifikasi gagal.
    $this->post(route('member.login'), [
        'nik' => $member->nik,
        'password' => 'sandi-rahasia',
    ])->assertRedirect(route('member.profile.edit'));
});

test('forgot password is blocked for a member whose registration is pending', function () {
    $member = memberForAuthTests([
        'status' => 'pending_payment',
        'registration_status' => 'pending',
        'member_card_no' => null,
    ]);

    $this->from(route('member.password.request'))
        ->post(route('member.password.update'), [
            'nik' => $member->nik,
            'phone' => $member->phone,
            'password' => 'sandi-baru-kamu',
            'password_confirmation' => 'sandi-baru-kamu',
        ])->assertRedirect(route('member.password.request'))
        ->assertSessionHasErrors('nik');
});

test('an already signed in member is redirected away from the login page to the profile', function () {
    $member = memberForAuthTests();

    $this->actingAs($member, 'member')
        ->get(route('member.login'))
        ->assertRedirect(route('member.profile.edit'));
});

test('the member session does not unlock the admin area', function () {
    $member = memberForAuthTests();

    $this->actingAs($member, 'member')
        ->get(route('members.index'))
        ->assertForbidden();
});

test('public registration requires a password', function () {
    $this->post('/daftar', [
        'nik' => '3300000000000499',
        'full_name' => 'Tanpa Sandi',
        'phone' => '081234567899',
        'gender' => 'L',
        'address' => 'Banjaranyar',
    ])->assertSessionHasErrors('password');

    expect(NuMember::where('nik', '3300000000000499')->exists())->toBeFalse();
});

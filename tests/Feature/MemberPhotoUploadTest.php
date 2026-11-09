<?php

use App\Models\NuMember;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * PNG 1x1 valid agar rule `image` lolos tanpa perlu ekstensi GD.
 */
function fakeMemberPhoto(): UploadedFile
{
    return fakePngUpload('foto-anggota.png');
}

test('admin member create form has a photo upload field', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.create'))
        ->assertOk()
        ->assertSee('name="photo"', false)
        ->assertSee('Foto Anggota', false)
        ->assertSee('enctype="multipart/form-data"', false);
});

test('admin member edit form shows the current photo and an upload field', function () {
    $member = NuMember::create([
        'nik' => '3300000000000101',
        'full_name' => 'Siti Rahayu',
        'phone' => '081234567890',
        'gender' => 'P',
        'address' => 'Kecamatan Cilongok',
        'status' => 'active',
        'member_card_no' => 'NU-2026-101',
        'photo' => 'members-photo/siti.png',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.edit', $member))
        ->assertOk()
        ->assertSee('name="photo"', false)
        ->assertSee(Storage::disk('supabase')->url('members-photo/siti.png'), false)
        ->assertSee('Foto saat ini', false);
});

test('admin can create a member with an uploaded photo', function () {
    fakeSupabaseDisk();

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('members.store'), [
            'nik' => '3300000000000102',
            'full_name' => 'Ahmad Hidayat',
            'phone' => '081234567891',
            'gender' => 'L',
            'address' => 'Kecamatan Cilongok',
            'photo' => fakeMemberPhoto(),
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('members.index'));

    $member = NuMember::where('nik', '3300000000000102')->sole();

    expect($member->photo)->not->toBeNull();

    Storage::disk('supabase')->assertExists($member->photo);
});

test('admin member edit can replace the photo', function () {
    fakeSupabaseDisk();

    $member = NuMember::create([
        'nik' => '3300000000000103',
        'full_name' => 'Budi Wijaya',
        'phone' => '081234567892',
        'gender' => 'L',
        'address' => 'Kecamatan Cilongok',
        'status' => 'active',
        'member_card_no' => 'NU-2026-103',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('members.update', $member), [
            'nik' => $member->nik,
            'full_name' => $member->full_name,
            'phone' => $member->phone,
            'gender' => $member->gender,
            'address' => $member->address,
            'photo' => fakeMemberPhoto(),
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('members.index'));

    $photo = $member->fresh()->photo;

    expect($photo)->not->toBeNull();

    Storage::disk('supabase')->assertExists($photo);
});

test('public registration form has a photo upload field', function () {
    $this->get('/daftar')
        ->assertOk()
        ->assertSee('name="photo"', false)
        ->assertSee('enctype="multipart/form-data"', false)
        ->assertSee('Foto Diri', false);
});

test('public registration stores the uploaded photo', function () {
    fakeSupabaseDisk();

    $this->post('/daftar', [
        'nik' => '3300000000000104',
        'full_name' => 'Nur Hasanah',
        'phone' => '081234567893',
        'gender' => 'P',
        'address' => 'Kecamatan Cilongok',
        'photo' => fakeMemberPhoto(),
        'password' => 'sandi-rahasia',
        'password_confirmation' => 'sandi-rahasia',
    ])->assertSessionHasNoErrors();

    $member = NuMember::where('nik', '3300000000000104')->sole();

    expect($member->photo)->not->toBeNull();

    Storage::disk('supabase')->assertExists($member->photo);
});

test('public registration rejects a file that is not a photo', function () {
    fakeSupabaseDisk();

    $this->from('/daftar')
        ->post('/daftar', [
            'nik' => '3300000000000105',
            'full_name' => 'Uji Coba',
            'phone' => '081234567894',
            'gender' => 'L',
            'address' => 'Kecamatan Cilongok',
            'photo' => UploadedFile::fake()->create('dokumen.txt', 10, 'text/plain'),
            'password' => 'sandi-rahasia',
            'password_confirmation' => 'sandi-rahasia',
        ])
        ->assertSessionHasErrors('photo');

    expect(NuMember::count())->toBe(0);
});

test('print card page renders the member photo', function () {
    $member = NuMember::create([
        'nik' => '3300000000000106',
        'full_name' => 'Fulan Fulanah',
        'phone' => '081234567895',
        'gender' => 'L',
        'address' => 'Kecamatan Cilongok',
        'status' => 'active',
        'member_card_no' => 'NU-2026-106',
        'photo' => 'members-photo/fulan.png',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.print-card', $member))
        ->assertOk()
        ->assertSee(Storage::disk('supabase')->url('members-photo/fulan.png'), false)
        ->assertSee('KARTU TANDA ANGGOTA', false);
});

test('admin member list shows the photo and a print card action', function () {
    $member = NuMember::create([
        'nik' => '3300000000000108',
        'full_name' => 'Hasan Basri',
        'phone' => '081234567897',
        'gender' => 'L',
        'address' => 'Kecamatan Cilongok',
        'status' => 'active',
        'member_card_no' => 'NU-2026-108',
        'photo' => 'members-photo/hasan.png',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index'))
        ->assertOk()
        ->assertSee(Storage::disk('supabase')->url('members-photo/hasan.png'), false)
        ->assertSee(route('members.print-card', $member), false);
});

test('guest cannot print a member card', function () {
    $member = NuMember::create([
        'nik' => '3300000000000107',
        'full_name' => 'Tamu',
        'phone' => '081234567896',
        'gender' => 'L',
        'address' => 'Kecamatan Cilongok',
        'status' => 'active',
        'member_card_no' => 'NU-2026-107',
    ]);

    $this->get(route('members.print-card', $member))
        ->assertRedirect('/login/admin');
});

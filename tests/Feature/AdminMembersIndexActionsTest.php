<?php

use App\Models\NuMember;
use App\Models\User;

function memberRow(array $overrides = []): NuMember
{
    static $counter = 0;
    $counter++;

    return NuMember::create(array_merge([
        'nik' => '33000000000003'.str_pad((string) $counter, 2, '0', STR_PAD_LEFT),
        'full_name' => 'Anggota Uji '.$counter,
        'phone' => '0812345600'.$counter,
        'gender' => 'L',
        'address' => 'Banjaranyar',
        'status' => 'pending_payment',
        'registration_status' => 'pending',
    ], $overrides));
}

test('members index shows accept and reject buttons for pending registrations', function () {
    $pending = memberRow(['full_name' => 'Calon Anggota']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index'))
        ->assertOk()
        ->assertSee('Calon Anggota')
        ->assertSee(route('admin.members.requests.accept', $pending), false)
        ->assertSee('data-reject-url="'.route('admin.members.requests.reject', $pending).'"', false)
        ->assertSee('Terima')
        ->assertSee('Tolak');
});

test('processed members do not show accept or reject buttons', function () {
    $accepted = memberRow([
        'full_name' => 'Anggota Aktif',
        'registration_status' => 'accepted',
        'status' => 'active',
        'member_card_no' => 'NU-2026-901',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index'))
        ->assertOk()
        ->assertSee('Anggota Aktif')
        ->assertDontSee(route('admin.members.requests.accept', $accepted), false)
        ->assertDontSee('data-reject-url="'.route('admin.members.requests.reject', $accepted).'"', false);
});

test('admin can accept a pending registration straight from the members list', function () {
    $pending = memberRow(['full_name' => 'Pendaftar Diterima']);

    $this->actingAs(User::factory()->admin()->create())
        ->from(route('members.index'))
        ->patch(route('admin.members.requests.accept', $pending))
        ->assertRedirect(route('members.index'))
        ->assertSessionHas('success');

    $pending->refresh();

    expect($pending->registration_status)->toBe('accepted')
        ->and($pending->status)->toBe('active')
        ->and($pending->member_card_no)->not->toBeNull();
});

test('admin can reject a pending registration straight from the members list', function () {
    $pending = memberRow(['full_name' => 'Pendaftar Ditolak']);

    $this->actingAs(User::factory()->admin()->create())
        ->from(route('members.index'))
        ->patch(route('admin.members.requests.reject', $pending), [
            'reason' => 'Alamat di luar wilayah ranting.',
        ])
        ->assertRedirect(route('members.index'))
        ->assertSessionHas('success');

    $pending->refresh();

    expect($pending->registration_status)->toBe('rejected')
        ->and($pending->rejection_reason)->toBe('Alamat di luar wilayah ranting.');
});

test('rejecting from the members list without a reason fails validation', function () {
    $pending = memberRow();

    $this->actingAs(User::factory()->admin()->create())
        ->from(route('members.index'))
        ->patch(route('admin.members.requests.reject', $pending), [])
        ->assertRedirect(route('members.index'))
        ->assertSessionHasErrors('reason');

    expect($pending->refresh()->registration_status)->toBe('pending');
});

test('accepting an already processed registration redirects back with an error message', function () {
    $accepted = memberRow([
        'registration_status' => 'accepted',
        'status' => 'active',
        'member_card_no' => 'NU-2026-902',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->from(route('members.index'))
        ->patch(route('admin.members.requests.accept', $accepted))
        ->assertRedirect(route('members.index'))
        ->assertSessionHas('error');
});

test('the members table is compact and does not need horizontal scrolling', function () {
    memberRow(['full_name' => 'Anggota Tanpa Scroll']);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index'))
        ->assertOk()
        ->assertSee('Anggota Tanpa Scroll')
        ->assertSee('<table', false)
        // Kolom sekunder disembunyikan di layar kecil supaya tabel tetap muat.
        ->assertSee('hidden md:table-cell', false)
        ->assertSee('hidden xl:table-cell', false)
        ->assertDontSee('overflow-x-auto', false)
        ->assertSee('divide-y divide-slate-100', false);

    expect($response->getContent())->not->toContain('overflow-x-auto');
});

test('the reject dialog explains itself in plain language', function () {
    $pending = memberRow(['full_name' => 'Perlu Alasan']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index'))
        ->assertOk()
        ->assertSee('id="reject-modal"', false)
        ->assertSee('Tolak Pendaftaran?')
        ->assertSee('Alasan Penolakan')
        ->assertSee('Ya, Tolak Pendaftaran')
        ->assertSee('data-reject-name="Perlu Alasan"', false);
});

test('row action forms are not nested inside the bulk print form', function () {
    $pending = memberRow(['full_name' => 'Anggota Form']);

    $html = $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index'))
        ->assertOk()
        ->getContent();

    $bulkStart = strpos($html, 'id="bulk-action-form"');
    $bulkEnd = strpos($html, '</form>', $bulkStart);
    $acceptAction = strpos($html, route('admin.members.requests.accept', $pending));

    expect($bulkStart)->not->toBeFalse()
        ->and($bulkEnd)->not->toBeFalse()
        ->and($acceptAction)->not->toBeFalse()
        // Form cetak massal harus sudah ditutup sebelum form Terima per baris muncul,
        // supaya tombol Terima tidak mengirim PATCH ke URL halaman.
        ->and($bulkEnd)->toBeLessThan($acceptAction);
});

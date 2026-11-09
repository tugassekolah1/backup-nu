<?php

use App\Models\NuMember;
use App\Models\User;

function bulkPrintMember(array $overrides = []): NuMember
{
    static $seq = 400;

    return NuMember::create(array_merge([
        'nik' => (string) random_int(1000000000000000, 9999999999999999),
        'full_name' => 'Fulan '.++$seq,
        'phone' => '081234567803',
        'gender' => 'L',
        'address' => 'Kecamatan Cilongok',
        'status' => 'active',
        'registration_status' => 'accepted',
        'member_card_no' => 'NU-2026-'.$seq,
    ], $overrides));
}

test('admin can bulk print kta via post with selected ids', function () {
    $member = bulkPrintMember();

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('members.cetak-kta-bulk'), ['ids' => [$member->id]])
        ->assertOk()
        ->assertSee($member->full_name, false);
});

test('guest hitting cetak-kta-bulk is redirected to login', function () {
    $this->post(route('members.cetak-kta-bulk'))
        ->assertRedirect(route('login'));
});

test('get without ids prints every active member', function () {
    $member = bulkPrintMember();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.cetak-kta-bulk'))
        ->assertOk()
        ->assertSee($member->full_name, false);
});

test('printing by banom only includes active members of that banom', function () {
    $ansorMember = bulkPrintMember(['organisasi' => 'pac_ansor']);
    bulkPrintMember(['organisasi' => 'pac_ipnu']);
    bulkPrintMember(['organisasi' => 'pac_ansor', 'status' => 'pending_payment']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.cetak-kta-bulk', ['banom' => 'pac_ansor']))
        ->assertOk()
        ->assertSee($ansorMember->full_name, false)
        ->assertDontSee('PAC IPNU', false);
});

test('printing banom none includes only members without organisation', function () {
    $member = bulkPrintMember(['organisasi' => null]);
    bulkPrintMember(['organisasi' => 'pac_ansor']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.cetak-kta-bulk', ['banom' => 'none']))
        ->assertOk()
        ->assertSee($member->full_name, false);
});

test('printing with an unknown banom code bounces back with an error', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->from(route('members.index'))
        ->get(route('members.cetak-kta-bulk', ['banom' => 'pa_hantam']))
        ->assertRedirect(route('members.index'))
        ->assertSessionHas('error', 'Banom tidak valid.');
});

test('members index exposes a berjamaah print link on every banom card', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index'))
        ->assertOk()
        ->assertSee(route('members.cetak-kta-bulk', ['banom' => 'pac_ansor']), false)
        ->assertSee(route('members.cetak-kta-bulk', ['banom' => 'none']), false)
        ->assertSee(route('members.cetak-kta-bulk'), false);
});

test('members index shows the print banom button when a banom filter is active', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index', ['banom' => 'pac_ansor']))
        ->assertOk()
        ->assertSee('Cetak KTA Banom Ini', false);
});

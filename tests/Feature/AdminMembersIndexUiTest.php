<?php

use App\Models\NuMember;
use App\Models\Pengurus;
use App\Models\User;

test('member management summarises organisations as compact chips instead of boxed cards', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index'))
        ->assertOk()
        // Bentuk lama: 12 kartu kotak ber-border tebal dengan tombol cetak melayang.
        ->assertDontSee('border-2 transition-all hover:shadow-md', false)
        // Bentuk baru: satu baris chip pill.
        ->assertSee('inline-flex items-stretch rounded-full border', false);
});

test('every organisation chip still exposes its own bulk print link', function () {
    $response = $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index'))
        ->assertOk();

    $response->assertSee(route('members.cetak-kta-bulk'), false)
        ->assertSee(route('members.cetak-kta-bulk', ['banom' => 'none']), false);

    foreach (NuMember::organisasiCodes() as $kode) {
        $response->assertSee(route('members.cetak-kta-bulk', ['banom' => $kode]), false);
    }
});

test('chips list one entry per organisation plus the all and unassigned entries', function () {
    $response = $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index'))
        ->assertOk();

    $response->assertSee('>Semua<', false)
        ->assertSee('Tanpa Organisasi', false);

    foreach (NuMember::organisasiCodes() as $kode) {
        $response->assertSee(Pengurus::BANOMS[$kode]['label'] ?? $kode, false);
    }
});

test('the active chip is marked as current and highlighted', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index', ['banom' => 'pac_ansor']))
        ->assertOk()
        ->assertSee('aria-current="true"', false)
        // Kutipnya tidak boleh ikut ter-escape jadi &quot; — kalau iya,
        // atributnya rusak dan penanda chip aktif hilang dari browser.
        ->assertDontSee('aria-current=&quot;', false)
        ->assertSee('bg-emerald-600 border-emerald-600', false);
});

test('the unassigned chip uses its own amber accent', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index', ['banom' => 'none']))
        ->assertOk()
        ->assertSee('bg-amber-500 border-amber-500', false);
});

test('the members page does not rely on the tailwind browser cdn', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index'))
        ->assertOk()
        ->assertDontSee('tailwindcss/browser', false);
});

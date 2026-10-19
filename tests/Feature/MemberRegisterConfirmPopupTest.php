<?php

test('registration form opens a confirmation popup above the form', function () {
    $this->get('/daftar')
        ->assertOk()
        ->assertSee('id="confirmRegister"', false)
        ->assertSee('id="confirmSend"', false)
        ->assertSee('id="confirmBack"', false)
        ->assertSee('Periksa Data Anda Dulu')
        ->assertSee('Kirim Pendaftaran')
        ->assertSee('Kembali Perbaiki');
});

test('confirmation popup lists every field the registrant must verify', function () {
    $this->get('/daftar')
        ->assertOk()
        ->assertSee('id="cfNik"', false)
        ->assertSee('id="cfNama"', false)
        ->assertSee('id="cfGender"', false)
        ->assertSee('id="cfPhone"', false)
        ->assertSee('id="cfOrganisasi"', false)
        ->assertSee('id="cfAddress"', false)
        ->assertSee('id="cfPhoto"', false);
});

test('confirmation popup sits above the form layer and targets the register endpoint', function () {
    $html = $this->get('/daftar')->assertOk()->getContent();

    // Overlay fixed + z-index di atas form-section (z-index: 3).
    expect($html)->toContain('class="confirm-overlay"')
        ->and($html)->toMatch('/\.confirm-overlay\s*\{[^}]*position: fixed/s')
        ->and($html)->toContain('z-index: 1000')
        ->and($html)->toContain('action="'.route('members.register').'"')
        ->and($html)->toContain('id="registerForm"');
});

test('required fields are checked before the popup opens', function () {
    $html = $this->get('/daftar')->assertOk()->getContent();

    foreach (['nik', 'full_name', 'gender', 'phone', 'address'] as $field) {
        expect($html)->toContain("id: '".$field."'");
    }

    expect($html)->toContain('Mohon lengkapi dulu:');
});

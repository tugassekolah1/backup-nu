<?php

use App\Models\NuMember;

test('kartu anggota page renders the public search form', function () {
    $this->get('/kartu-anggota')
        ->assertOk()
        ->assertSee('<title>Kartu Anggota', false)
        ->assertSee('name="nik"', false)
        ->assertSee(route('members.card'), false);
});

test('legacy cek-status and cek-kartu urls redirect to the kartu anggota page', function () {
    $this->get('/cek-status?nik=3300000000000001')
        ->assertRedirect(route('members.card', ['nik' => '3300000000000001']));

    $this->get('/cek-kartu?nik=3300000000000001')
        ->assertRedirect(route('members.card', ['nik' => '3300000000000001']));

    $this->get('/cek-status')
        ->assertRedirect(route('members.card'));
});

test('unknown nik shows the not submitted state with a registration cta', function () {
    $this->get('/kartu-anggota?nik=3300000000000000')
        ->assertOk()
        ->assertSee('Belum Ada Pengajuan')
        ->assertSee('3300000000000000')
        ->assertSee(route('members.register-form'), false)
        ->assertDontSee('id="mcStage"', false);
});

test('the card page requires the registered phone as a second factor', function () {
    $member = NuMember::create([
        'nik' => '3300000000000009',
        'full_name' => 'Siti Maemunah',
        'phone' => '081234567899',
        'gender' => 'P',
        'address' => 'Kecamatan Cilongok',
        'status' => 'active',
        'member_card_no' => 'NU-2026-999',
    ]);

    // NIK saja: identitas tidak boleh bocor.
    $this->get('/kartu-anggota?nik='.$member->nik)
        ->assertOk()
        ->assertDontSee('Siti Maemunah')
        ->assertDontSee('081234567899')
        ->assertDontSee('NU-2026-999')
        ->assertDontSee('id="mcStage"', false);

    // NIK + nomor HP yang salah: tetap tidak boleh bocor.
    $this->get('/kartu-anggota?nik='.$member->nik.'&phone=081299999999')
        ->assertOk()
        ->assertDontSee('Siti Maemunah')
        ->assertDontSee('NU-2026-999');

    // NIK + nomor HP yang benar: data tampil.
    $this->get('/kartu-anggota?nik='.$member->nik.'&phone='.$member->phone)
        ->assertOk()
        ->assertSee('Siti Maemunah')
        ->assertSee('NU-2026-999');
});

test('the phone second factor tolerates the 62 country code prefix', function () {
    $member = NuMember::create([
        'nik' => '3300000000000008',
        'full_name' => 'Abdul Karim',
        'phone' => '081234567898',
        'gender' => 'L',
        'address' => 'Kecamatan Cilongok',
        'status' => 'active',
        'member_card_no' => 'NU-2026-998',
    ]);

    // 6281234567898 == 081234567898
    $this->get('/kartu-anggota?nik='.$member->nik.'&phone=6281234567898')
        ->assertOk()
        ->assertSee('Abdul Karim')
        ->assertSee('NU-2026-998');
});

test('a pending registration shows the waiting state without print buttons', function () {
    $member = NuMember::create([
        'nik' => '3300000000000001',
        'full_name' => 'Budi Santoso',
        'phone' => '081234567890',
        'gender' => 'L',
        'address' => 'Kecamatan Cilongok',
        'status' => 'pending_payment',
        'registration_status' => 'pending',
    ]);

    $this->get('/kartu-anggota?nik=3300000000000001&phone=081234567890')
        ->assertOk()
        ->assertSee('Budi Santoso')
        ->assertSee('Menunggu Persetujuan')
        ->assertSee('Tanggal pengajuan')
        ->assertSee('Menunggu Verifikasi')
        ->assertDontSee('id="mcStage"', false)
        ->assertDontSee('printMemberCard(', false);
});

test('a rejected application shows the reason and a reapply cta without print', function () {
    $member = NuMember::create([
        'nik' => '3300000000000005',
        'full_name' => 'Zainul Arifin',
        'phone' => '081234567893',
        'gender' => 'L',
        'address' => 'Kecamatan Cilongok',
        'status' => 'pending_payment',
        'registration_status' => 'rejected',
        'rejection_reason' => 'Berkas identitas belum lengkap.',
    ]);

    $this->get('/kartu-anggota?nik='.$member->nik.'&phone='.$member->phone)
        ->assertOk()
        ->assertSee('Pengajuan Ditolak')
        ->assertSee('Berkas identitas belum lengkap.')
        ->assertSee('Ajukan Kembali')
        ->assertSee(route('members.register-form'), false)
        ->assertDontSee('id="mcStage"', false)
        ->assertDontSee('printMemberCard(', false);
});

test('an approved member sees the card right on the page with print and download', function () {
    $member = NuMember::create([
        'nik' => '3300000000000003',
        'full_name' => 'Ahmad Fauzi',
        'phone' => '081234567892',
        'gender' => 'L',
        'address' => 'Kecamatan Cilongok',
        'status' => 'active',
        'registration_status' => 'accepted',
        'member_card_no' => 'NU-2026-001',
    ]);

    $this->get('/kartu-anggota?nik=3300000000000003&phone=081234567892')
        ->assertOk()
        ->assertSee('Disetujui / Aktif')
        ->assertSee('Pengajuan Disetujui — Kartu Siap Digunakan')
        ->assertSee('Aktif')
        ->assertSee('NU-2026-001')
        ->assertSee('id="mcStage"', false)
        ->assertSee('Cetak Kartu')
        ->assertSee("printMemberCard('front')", false)
        ->assertSee("printMemberCard('back')", false)
        ->assertSee("printMemberCard('both')", false);
});

<?php

use App\Models\Aspirasi;
use App\Models\User;

function buatAspirasi(array $overrides = []): Aspirasi
{
    return Aspirasi::factory()->create($overrides);
}

function payloadAspirasi(array $overrides = []): array
{
    return array_merge([
        'nama' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'no_hp' => '081234567890',
        'kategori' => 'kegiatan',
        'isi' => 'Usulan agar pengajian rutin ditambah setiap malam Jumat.',
    ], $overrides);
}

test('guest can open the public kotak aspirasi page', function () {
    $this->get('/aspirasi')
        ->assertOk()
        ->assertSee('Kotak Aspirasi')
        ->assertSee('name="nama"', false)
        ->assertSee('name="kategori"', false)
        ->assertSee('name="isi"', false)
        ->assertSee(route('aspirasi.store'), false);
});

test('visitor can send an aspiration', function () {
    $response = $this->from('/aspirasi')
        ->post('/aspirasi', payloadAspirasi());
    $response->assertSessionHasNoErrors();

    $aspirasi = Aspirasi::sole();
    $response->assertRedirect(route('aspirasi.lacak', ['kode' => $aspirasi->kode]));

    expect($aspirasi->nama)->toBe('Budi Santoso')
        ->and($aspirasi->email)->toBe('budi@example.com')
        ->and($aspirasi->no_hp)->toBe('081234567890')
        ->and($aspirasi->kategori)->toBe('kegiatan')
        ->and($aspirasi->isi)->toBe('Usulan agar pengajian rutin ditambah setiap malam Jumat.')
        ->and($aspirasi->status)->toBe('baru')
        ->and($aspirasi->tanggapan)->toBeNull()
        ->and($aspirasi->kode)->toMatch('/^ASP-[A-Z0-9]{8}$/');

    $this->get(route('aspirasi.lacak', ['kode' => $aspirasi->kode]))
        ->assertOk()
        ->assertSee($aspirasi->kode, false);
});

test('contact fields are optional when sending an aspiration', function () {
    $response = $this->post('/aspirasi', payloadAspirasi([
        'email' => '',
        'no_hp' => '',
    ]));
    $response->assertSessionHasNoErrors();

    $aspirasi = Aspirasi::sole();
    $response->assertRedirect(route('aspirasi.lacak', ['kode' => $aspirasi->kode]));

    expect($aspirasi->email)->toBeNull()
        ->and($aspirasi->no_hp)->toBeNull();
});

test('aspiration input is validated', function (array $payload, string $field) {
    $this->from('/aspirasi')
        ->post('/aspirasi', $payload)
        ->assertRedirect(route('aspirasi.index'))
        ->assertSessionHasErrors($field);
})->with([
    'nama kosong' => [['nama' => ''], 'nama'],
    'kategori kosong' => [['kategori' => ''], 'kategori'],
    'kategori tidak dikenal' => [['kategori' => 'terlengkap'], 'kategori'],
    'isi kosong' => [['isi' => ''], 'isi'],
    'isi terlalu pendek' => [['isi' => 'pendek'], 'isi'],
    'email tidak valid' => [['email' => 'bukan-email'], 'email'],
]);

test('sending aspirations is rate limited', function () {
    foreach (range(1, 10) as $i) {
        $this->post('/aspirasi', payloadAspirasi(['nama' => 'Warga '.$i]))
            ->assertSessionHasNoErrors();
    }

    $this->post('/aspirasi', payloadAspirasi(['nama' => 'Warga Batas']))
        ->assertStatus(429);

    expect(Aspirasi::count())->toBe(10);
});

test('responded aspirations are shown on the public page', function () {
    buatAspirasi([
        'nama' => 'Siti Aminah',
        'isi' => 'Mohon toilet wudu ditambah di aula.',
        'status' => 'selesai',
        'tanggapan' => 'Insyaallah toilet wudu ditambah tahun ini.',
        'tanggapan_at' => now(),
    ]);

    buatAspirasi([
        'status' => 'baru',
        'tanggapan' => null,
        'tanggapan_at' => null,
    ]);

    $this->get('/aspirasi')
        ->assertOk()
        ->assertSee('Tanggapan Pengurus')
        ->assertSee('Mohon toilet wudu ditambah di aula.')
        ->assertSee('Insyaallah toilet wudu ditambah tahun ini.');
});

test('aspirations without a response are not published', function () {
    buatAspirasi([
        'isi' => 'Aspirasi rahasia yang belum ditanggapi.',
        'status' => 'diproses',
        'tanggapan' => null,
        'tanggapan_at' => null,
    ]);

    $this->get('/aspirasi')
        ->assertOk()
        ->assertDontSee('Aspirasi rahasia yang belum ditanggapi.')
        ->assertSee('Belum ada tanggapan yang dipublikasikan');
});

test('guest cannot access admin aspiration pages', function () {
    $this->get('/admin/aspirasi')->assertRedirect('/login/admin');
});

test('non admin gets 403 on admin aspiration pages', function () {
    $aspirasi = buatAspirasi();

    $this->actingAs(User::factory()->create())
        ->get('/admin/aspirasi')
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->put("/admin/aspirasi/{$aspirasi->id}", ['status' => 'selesai'])
        ->assertForbidden();
});

test('admin sees the aspiration list with recap', function () {
    buatAspirasi(['nama' => 'Ahmad Fauzi', 'status' => 'baru']);
    buatAspirasi(['nama' => 'Zahra Aulia', 'status' => 'selesai']);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/aspirasi')
        ->assertOk()
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Zahra Aulia')
        ->assertSee('Total Aspirasi')
        ->assertSee('Selesai Ditangani');
});

test('admin can search aspirations by name and content', function () {
    buatAspirasi(['nama' => 'Cari Satu', 'isi' => 'Isi tentang lampu jalan desa.']);
    buatAspirasi(['nama' => 'Cari Dua', 'isi' => 'Isi tentang sampah organik.']);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/aspirasi?q=lampu')
        ->assertOk()
        ->assertSee('Cari Satu')
        ->assertDontSee('Cari Dua');

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/aspirasi?q=Cari Dua')
        ->assertOk()
        ->assertSee('Cari Dua')
        ->assertDontSee('Cari Satu');
});

test('admin can filter aspirations by status and category', function () {
    buatAspirasi(['nama' => 'Filter Baru', 'status' => 'baru', 'kategori' => 'kegiatan']);
    buatAspirasi(['nama' => 'Filter Selesai', 'status' => 'selesai', 'kategori' => 'fasilitas']);

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin/aspirasi?status=baru')
        ->assertOk()
        ->assertSee('Filter Baru')
        ->assertDontSee('Filter Selesai');

    $this->actingAs($admin)
        ->get('/admin/aspirasi?kategori=fasilitas')
        ->assertOk()
        ->assertSee('Filter Selesai')
        ->assertDontSee('Filter Baru');
});

test('unknown filters are ignored', function () {
    buatAspirasi(['nama' => 'Tetap Muncul', 'status' => 'baru']);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/aspirasi?status=ngawur&kategori=ngawur')
        ->assertOk()
        ->assertSee('Tetap Muncul');
});

test('admin can update status and give a response', function () {
    $aspirasi = buatAspirasi(['status' => 'baru', 'tanggapan' => null, 'tanggapan_at' => null]);

    $this->actingAs(User::factory()->admin()->create())
        ->from(route('admin.aspirasi.edit', $aspirasi))
        ->put("/admin/aspirasi/{$aspirasi->id}", [
            'status' => 'selesai',
            'tanggapan' => '  Terima kasih, usulan sedang kami bahas.  ',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.aspirasi.index'));

    $aspirasi->refresh();

    expect($aspirasi->status)->toBe('selesai')
        ->and($aspirasi->tanggapan)->toBe('Terima kasih, usulan sedang kami bahas.')
        ->and($aspirasi->tanggapan_at)->not->toBeNull();
});

test('clearing the response removes it and its timestamp', function () {
    $aspirasi = buatAspirasi([
        'status' => 'selesai',
        'tanggapan' => 'Tanggapan lama.',
        'tanggapan_at' => now(),
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->put("/admin/aspirasi/{$aspirasi->id}", [
            'status' => 'diproses',
            'tanggapan' => '',
        ])
        ->assertSessionHasNoErrors();

    $aspirasi->refresh();

    expect($aspirasi->status)->toBe('diproses')
        ->and($aspirasi->tanggapan)->toBeNull()
        ->and($aspirasi->tanggapan_at)->toBeNull();
});

test('aspiration status update is validated', function (array $payload, string $field) {
    $aspirasi = buatAspirasi();

    $this->actingAs(User::factory()->admin()->create())
        ->put("/admin/aspirasi/{$aspirasi->id}", $payload)
        ->assertSessionHasErrors($field);
})->with([
    'status kosong' => [['status' => '', 'tanggapan' => ''], 'status'],
    'status tidak dikenal' => [['status' => 'ngawur', 'tanggapan' => ''], 'status'],
]);

test('admin can delete an aspiration permanently', function () {
    $aspirasi = buatAspirasi();

    $this->actingAs(User::factory()->admin()->create())
        ->delete("/admin/aspirasi/{$aspirasi->id}")
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.aspirasi.index'));

    $this->assertDatabaseMissing('aspirasis', ['id' => $aspirasi->id]);
});

test('admin can open the response form', function () {
    $aspirasi = buatAspirasi(['nama' => 'Pengirim Contoh', 'isi' => 'Isi lengkap aspirasi contoh.']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.aspirasi.edit', $aspirasi))
        ->assertOk()
        ->assertSee('Pengirim Contoh')
        ->assertSee('Isi lengkap aspirasi contoh.')
        ->assertSee('name="status"', false)
        ->assertSee('name="tanggapan"', false);
});

test('sidebar links admin to the aspiration manager', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Kotak Aspirasi')
        ->assertSee(route('admin.aspirasi.index'), false);
});

test('sidebar sends non admin users to the public aspiration page', function () {
    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee(route('aspirasi.index'), false)
        ->assertDontSee(route('admin.aspirasi.index'), false);
});

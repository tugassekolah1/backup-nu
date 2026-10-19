<?php

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\Gallery;
use App\Models\Infaq;
use App\Models\NuMember;
use App\Models\Pengurus;
use App\Models\User;

function infaqAdmin(): User
{
    return User::factory()->admin()->create();
}

function buatInfaq(array $overrides = []): Infaq
{
    return Infaq::create(array_merge([
        'kode_transaksi' => 'INF-'.strtoupper(uniqid()),
        'nama_donatur' => 'Hamba Allah',
        'no_hp' => null,
        'nominal' => 10000,
        'metode_pembayaran' => 'tunai',
        'status' => 'pending',
        'catatan' => null,
        'paid_at' => null,
    ], $overrides));
}

test('guest cannot access admin infaq pages', function () {
    $this->get('/admin/infaq')->assertRedirect('/login');
});

test('non admin gets 403 on admin infaq pages', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin/infaq')
        ->assertForbidden();
});

test('admin sees the infaq transaction list', function () {
    buatInfaq(['kode_transaksi' => 'INF-LIST01', 'nominal' => 15000]);

    $this->actingAs(infaqAdmin())
        ->get('/admin/infaq')
        ->assertOk()
        ->assertSee('INF-LIST01')
        ->assertSee('Rp 15.000');
});

test('admin can record a new infaq transaction', function () {
    $this->actingAs(infaqAdmin())
        ->post('/admin/infaq', [
            'nominal' => 75000,
            'metode_pembayaran' => 'qris',
            'nama_donatur' => 'Bu Siti',
            'no_hp' => '08123456789',
            'status' => 'pending',
            'catatan' => 'infak jumat bersih',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.infaq.index'));

    $infaq = Infaq::sole();

    expect($infaq->kode_transaksi)->toStartWith('INF-')
        ->and($infaq->nominal)->toBe(75000)
        ->and($infaq->metode_pembayaran)->toBe('qris')
        ->and($infaq->nama_donatur)->toBe('Bu Siti')
        ->and($infaq->status)->toBe('pending')
        ->and($infaq->paid_at)->toBeNull();
});

test('recording a lunas transaction without date sets paid_at automatically', function () {
    $this->actingAs(infaqAdmin())
        ->post('/admin/infaq', [
            'nominal' => 25000,
            'metode_pembayaran' => 'transfer_bank',
            'status' => 'lunas',
        ])
        ->assertSessionHasNoErrors();

    $infaq = Infaq::sole();

    expect($infaq->status)->toBe('lunas')
        ->and($infaq->paid_at)->not->toBeNull();
});

test('empty donatur name defaults to Hamba Allah', function () {
    $this->actingAs(infaqAdmin())
        ->post('/admin/infaq', [
            'nominal' => 5000,
            'metode_pembayaran' => 'tunai',
            'nama_donatur' => '',
            'status' => 'pending',
        ])
        ->assertSessionHasNoErrors();

    expect(Infaq::sole()->nama_donatur)->toBe('Hamba Allah');
});

test('infaq transaction input is validated', function (array $payload, string $field) {
    $this->actingAs(infaqAdmin())
        ->from(route('admin.infaq.create'))
        ->post('/admin/infaq', $payload)
        ->assertSessionHasErrors($field);
})->with([
    'nominal kosong' => [[
        'nominal' => '', 'metode_pembayaran' => 'tunai', 'status' => 'pending',
    ], 'nominal'],
    'nominal bukan angka' => [[
        'nominal' => 'seratus', 'metode_pembayaran' => 'tunai', 'status' => 'pending',
    ], 'nominal'],
    'metode tidak dikenal' => [[
        'nominal' => 10000, 'metode_pembayaran' => 'crypto', 'status' => 'pending',
    ], 'metode_pembayaran'],
    'status tidak dikenal' => [[
        'nominal' => 10000, 'metode_pembayaran' => 'tunai', 'status' => 'ngawur',
    ], 'status'],
]);

test('admin can update a transaction without changing its kode', function () {
    $infaq = buatInfaq(['kode_transaksi' => 'INF-KEEP01', 'nominal' => 10000, 'status' => 'pending']);

    $this->actingAs(infaqAdmin())
        ->put("/admin/infaq/{$infaq->id}", [
            'nominal' => 99000,
            'metode_pembayaran' => 'qris',
            'nama_donatur' => 'Pak Ahmad',
            'status' => 'lunas',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.infaq.index'));

    $infaq->refresh();

    expect($infaq->kode_transaksi)->toBe('INF-KEEP01')
        ->and($infaq->nominal)->toBe(99000)
        ->and($infaq->nama_donatur)->toBe('Pak Ahmad')
        ->and($infaq->status)->toBe('lunas')
        ->and($infaq->paid_at)->not->toBeNull();
});

test('admin can delete a transaction permanently', function () {
    $infaq = buatInfaq();

    $this->actingAs(infaqAdmin())
        ->delete("/admin/infaq/{$infaq->id}")
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.infaq.index'));

    $this->assertDatabaseMissing('infaqs', ['id' => $infaq->id]);
});

test('pending transaction can be marked as lunas', function () {
    $infaq = buatInfaq(['status' => 'pending']);

    $this->actingAs(infaqAdmin())
        ->post("/admin/infaq/{$infaq->id}/lunas")
        ->assertRedirect();

    $infaq->refresh();

    expect($infaq->status)->toBe('lunas')
        ->and($infaq->paid_at)->not->toBeNull();
});

test('cancelled transaction cannot be marked as lunas', function () {
    $infaq = buatInfaq(['status' => 'dibatalkan']);

    $this->actingAs(infaqAdmin())
        ->post("/admin/infaq/{$infaq->id}/lunas")
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($infaq->refresh()->status)->toBe('dibatalkan');
});

test('admin can search transactions by kode and donatur', function () {
    buatInfaq(['kode_transaksi' => 'INF-CARIA1', 'nama_donatur' => 'Budi Santoso']);
    buatInfaq(['kode_transaksi' => 'INF-CARIB2', 'nama_donatur' => 'Joko Susilo']);

    $this->actingAs(infaqAdmin())
        ->get('/admin/infaq?q=CARIA1')
        ->assertOk()
        ->assertSee('INF-CARIA1')
        ->assertDontSee('INF-CARIB2');

    $this->actingAs(infaqAdmin())
        ->get('/admin/infaq?q=Joko')
        ->assertOk()
        ->assertSee('INF-CARIB2')
        ->assertDontSee('INF-CARIA1');
});

test('admin can filter transactions by status', function () {
    buatInfaq(['kode_transaksi' => 'INF-STATP1', 'status' => 'pending']);
    buatInfaq(['kode_transaksi' => 'INF-STATL1', 'status' => 'lunas', 'paid_at' => now()]);

    $this->actingAs(infaqAdmin())
        ->get('/admin/infaq?status=pending')
        ->assertOk()
        ->assertSee('INF-STATP1')
        ->assertDontSee('INF-STATL1');

    $this->actingAs(infaqAdmin())
        ->get('/admin/infaq?status=lunas')
        ->assertOk()
        ->assertSee('INF-STATL1')
        ->assertDontSee('INF-STATP1');
});

test('unknown status filter is ignored', function () {
    buatInfaq(['kode_transaksi' => 'INF-ANYST01', 'status' => 'pending']);

    $this->actingAs(infaqAdmin())
        ->get('/admin/infaq?status=ngawur')
        ->assertOk()
        ->assertSee('INF-ANYST01');
});

test('infaq recap shows correct totals', function () {
    buatInfaq(['nominal' => 100000, 'status' => 'lunas', 'paid_at' => now()]);
    buatInfaq(['nominal' => 50000, 'status' => 'pending']);

    $this->actingAs(infaqAdmin())
        ->get('/admin/infaq')
        ->assertOk()
        ->assertSee('Rp 100.000')
        ->assertSee('1 transaksi');
});

test('dashboard shows infaq widget and quick action for admin', function () {
    buatInfaq(['kode_transaksi' => 'INF-DASHW1', 'nama_donatur' => 'Hj. Aminah', 'status' => 'lunas', 'paid_at' => now()]);

    $this->actingAs(infaqAdmin())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee(route('admin.infaq.index'), false)
        ->assertSee(route('admin.infaq.create'), false)
        ->assertSee('Transaksi Infaq Terbaru')
        ->assertSee('INF-DASHW1')
        ->assertSee('Hj. Aminah');
});

test('dashboard hides infaq admin links from non admin users', function () {
    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertDontSee(route('admin.infaq.index'), false)
        ->assertDontSee('Transaksi Infaq Terbaru');
});

test('public infaq total includes admin recorded lunas transactions', function () {
    buatInfaq(['nominal' => 123000, 'status' => 'lunas', 'paid_at' => now()]);

    $this->get('/infaq')
        ->assertOk()
        ->assertSee('Rp 123.000');
});

test('admin can open the record and edit forms', function () {
    $admin = infaqAdmin();
    $infaq = buatInfaq(['kode_transaksi' => 'INF-FORM01']);

    $this->actingAs($admin)
        ->get(route('admin.infaq.create'))
        ->assertOk()
        ->assertSee('name="nominal"', false)
        ->assertSee('name="metode_pembayaran"', false);

    $this->actingAs($admin)
        ->get(route('admin.infaq.edit', $infaq))
        ->assertOk()
        ->assertSee('INF-FORM01')
        ->assertSee('name="status"', false);
});

test('non admin cannot open infaq create form', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.infaq.create'))
        ->assertForbidden();
});

test('every dashboard card shows its real count', function () {
    $owner = User::factory()->create();

    foreach (range(1, 6) as $i) {
        NuMember::create([
            'nik' => str_pad((string) $i, 16, '0', STR_PAD_LEFT),
            'full_name' => 'Anggota '.$i,
            'phone' => '08120000000'.$i,
            'gender' => 'L',
            'address' => 'Jl. Contoh No.'.$i,
        ]);
    }

    foreach (range(1, 7) as $i) {
        Pengurus::create([
            'nama' => 'Pengurus '.$i,
            'jabatan' => 'Ketua',
            'banom' => 'PR IPNU',
            'label_banom' => 'PR IPNU',
        ]);
    }

    foreach (range(1, 8) as $i) {
        Gallery::create([
            'judul' => 'Foto '.$i,
            'foto' => 'galeri/'.$i.'.jpg',
        ]);
    }

    foreach (range(1, 9) as $i) {
        Agenda::create([
            'title' => 'Agenda '.$i,
            'event_date' => now()->addDays($i)->toDateString(),
        ]);
    }

    foreach (range(1, 4) as $i) {
        Berita::create([
            'judul' => 'Berita '.$i,
            'slug' => 'dashboard-berita-'.$i,
            'isi' => 'Isi berita '.$i,
            'user_id' => $owner->id,
            'status' => true,
        ]);
    }

    $html = $this->actingAs(infaqAdmin())
        ->get('/dashboard')
        ->assertOk()
        ->getContent();

    $cards = [
        'Total Anggota' => 6,
        'Pengurus & Banom' => 7,
        'Total Publikasi' => 4,
        'Galeri Kegiatan' => 8,
        'Agenda Kegiatan' => 9,
    ];

    foreach ($cards as $title => $count) {
        expect($html)->toMatch(
            '/<h2[^>]*>\s*'.preg_quote($title, '/').'\s*<\/h2>.*?<h3[^>]*>\s*'.$count.'\s*<\/h3>/s'
        );
    }
});

test('admin infaq list shows the confirm button for pending transactions', function () {
    $pending = buatInfaq(['kode_transaksi' => 'INF-KONFIRM1', 'status' => 'pending']);
    $lunas = buatInfaq(['kode_transaksi' => 'INF-KONFIRM2', 'status' => 'lunas', 'paid_at' => now()]);

    $this->actingAs(infaqAdmin())
        ->get('/admin/infaq')
        ->assertOk()
        ->assertSee(route('admin.infaq.mark-lunas', $pending), false)
        ->assertDontSee(route('admin.infaq.mark-lunas', $lunas), false);
});

test('pending transaction can be cancelled by admin', function () {
    $infaq = buatInfaq(['status' => 'pending']);

    $this->actingAs(infaqAdmin())
        ->post("/admin/infaq/{$infaq->id}/batal")
        ->assertRedirect();

    $infaq->refresh();

    expect($infaq->status)->toBe('dibatalkan')
        ->and($infaq->paid_at)->toBeNull();
});

test('only pending transaction can be cancelled', function (string $status) {
    $infaq = buatInfaq(['status' => $status, 'paid_at' => $status === 'lunas' ? now() : null]);

    $this->actingAs(infaqAdmin())
        ->post("/admin/infaq/{$infaq->id}/batal")
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($infaq->refresh()->status)->toBe($status);
})->with([
    'lunas' => 'lunas',
    'sudah dibatalkan' => 'dibatalkan',
]);

test('guest cannot cancel a transaction', function () {
    $infaq = buatInfaq(['status' => 'pending']);

    $this->post("/admin/infaq/{$infaq->id}/batal")
        ->assertRedirect('/login');

    expect($infaq->refresh()->status)->toBe('pending');
});

test('non admin cannot cancel a transaction', function () {
    $infaq = buatInfaq(['status' => 'pending']);

    $this->actingAs(User::factory()->create())
        ->post("/admin/infaq/{$infaq->id}/batal")
        ->assertForbidden();

    expect($infaq->refresh()->status)->toBe('pending');
});

test('pending rows show labelled confirm and cancel buttons', function () {
    $pendingBukti = buatInfaq([
        'kode_transaksi' => 'INF-TOMBOL1',
        'status' => 'pending',
        'bukti_path' => 'infaq-bukti/contoh.jpg',
    ]);
    $lunas = buatInfaq(['kode_transaksi' => 'INF-TOMBOL2', 'status' => 'lunas', 'paid_at' => now()]);

    $this->actingAs(infaqAdmin())
        ->get('/admin/infaq')
        ->assertOk()
        ->assertSee('Setujui & Lunas')
        ->assertSee('Batalkan')
        ->assertSee(route('admin.infaq.mark-batal', $pendingBukti), false)
        ->assertSee(route('admin.infaq.mark-lunas', $pendingBukti), false)
        ->assertDontSee(route('admin.infaq.mark-batal', $lunas), false);
});

test('pending rows without bukti use the plain tandai lunas label', function () {
    buatInfaq(['kode_transaksi' => 'INF-TOMBOL3', 'status' => 'pending']);

    $this->actingAs(infaqAdmin())
        ->get('/admin/infaq')
        ->assertOk()
        ->assertSee('Tandai Lunas')
        ->assertDontSee('Setujui & Lunas');
});

test('dashboard cards explain themselves in plain language for new admins', function () {
    $html = $this->actingAs(infaqAdmin())
        ->get('/dashboard')
        ->assertOk()
        ->getContent();

    $penjelasan = [
        'Lihat jumlah infak terkumpul dan catat transaksi baru.',
        'Data seluruh anggota, termasuk menyetujui pendaftar baru.',
        'Susunan pengurus tiap organisasi dan badan otonom.',
        'Buat, ubah, dan terbitkan berita kegiatan.',
        'Simpan foto dokumentasi kegiatan.',
        'Atur jadwal dan agenda kegiatan.',
    ];

    foreach ($penjelasan as $teks) {
        expect($html)->toContain($teks);
    }
});

test('dashboard card buttons carry specific labels instead of a bare Kelola', function () {
    $this->actingAs(infaqAdmin())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Kelola Infaq')
        ->assertSee('Kelola Anggota')
        ->assertSee('Kelola Pengurus')
        ->assertSee('Kelola Berita')
        ->assertSee('Kelola Galeri')
        ->assertSee('Kelola Agenda');
});

test('non admin sees a view label instead of a manage label on the infaq card', function () {
    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Lihat Infaq')
        ->assertDontSee('Kelola Infaq');
});

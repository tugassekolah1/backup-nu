<?php

use App\Models\Agenda;

test('public agenda page is reachable by guests', function () {
    $response = $this->get('/agenda');

    $response->assertOk()
        ->assertSee('Majelis Taklim', false)
        ->assertSee('Agenda Terdekat', false)
        ->assertSee('Agenda Telah Berlangsung', false);
});

test('public agenda page uses the agenda.public route name', function () {
    expect(route('agenda.public'))->toBe(url('/agenda'));

    $this->get(route('agenda.public'))->assertOk();
});

test('public agenda page lists upcoming agenda', function () {
    $agenda = Agenda::create([
        'title' => 'Pengajian Rutin Malam Rabu Pon',
        'category' => 'Pengajian',
        'event_date' => now()->addDays(7)->toDateString(),
        'event_time' => '19:30 WIB',
        'location' => 'Masjid Baiturrahman',
        'description' => 'Pengajian kitab kuning bersama jamaah Kecamatan Cilongok.',
    ]);

    $this->get('/agenda')
        ->assertOk()
        ->assertSee('Pengajian Rutin Malam Rabu Pon')
        ->assertSee('Pengajian')
        ->assertSee('19:30 WIB')
        ->assertSee('Masjid Baiturrahman');
});

test('public agenda page keeps past agenda in the archive section only', function () {
    $upcoming = Agenda::create([
        'title' => 'Agenda Mendatang Khusus',
        'category' => 'Kegiatan',
        'event_date' => now()->addDays(3)->toDateString(),
        'event_time' => '08:00 WIB',
    ]);

    $past = Agenda::create([
        'title' => 'Agenda Lama Yang Telah Selesai',
        'category' => 'Sosial',
        'event_date' => now()->subDays(10)->toDateString(),
        'event_time' => '10:00 WIB',
        'location' => 'Aula Ranting',
    ]);

    $response = $this->get('/agenda');

    $response->assertOk()
        ->assertSee($upcoming->title)
        ->assertSee($past->title);

    // Agenda lampau tidak boleh muncul di bagian terdekat (setelah judul bagian arsip)
    $html = $response->getContent();
    $arsipPos = strpos($html, 'Agenda Telah Berlangsung');
    $pastPos = strpos($html, $past->title);
    $nextPos = strpos($html, $upcoming->title);

    expect($arsipPos)->not->toBeFalse();
    expect($pastPos)->toBeGreaterThan($arsipPos);
    expect($nextPos)->toBeLessThan($arsipPos);
});

test('public agenda page shows an empty state when there is no agenda', function () {
    $this->get('/agenda')
        ->assertOk()
        ->assertSee('Belum ada agenda terdekat')
        ->assertSee('Tanya Jadwal via WhatsApp', false);
});

test('public agenda page paginates upcoming agenda', function () {
    foreach (range(1, 12) as $i) {
        Agenda::create([
            'title' => "Agenda Nomor {$i}",
            'category' => 'Kegiatan',
            'event_date' => now()->addDays($i)->toDateString(),
        ]);
    }

    $pageOne = $this->get('/agenda');
    $pageOne->assertOk()->assertSee('Agenda Nomor 1')->assertSee('Halaman 1 dari 2');

    $this->get('/agenda?page=2')
        ->assertOk()
        ->assertSee('Agenda Nomor 10');
});

test('public agenda page escapes agenda content', function () {
    Agenda::create([
        'title' => 'Kegiatan <script>alert(1)</script>',
        'category' => 'Kegiatan',
        'event_date' => now()->addDay()->toDateString(),
        'description' => '<b>Deskripsi</b> berbahaya',
    ]);

    $this->get('/agenda')
        ->assertOk()
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('&lt;script&gt;', false);
});

<?php

use App\Models\Berita;
use App\Models\BeritaView;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

test('admin can create berita with jenis and slug', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->post('/admin/berita', [
            'judul' => 'Rapat Kerja NU',
            'slug' => 'rapat-kerja-nu',
            'jenis' => 'Kegiatan',
            'isi' => 'Isi berita rapat kerja.',
            'status' => 1,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/berita');

    $berita = Berita::sole();

    expect($berita->slug)->toBe('rapat-kerja-nu')
        ->and($berita->jenis)->toBe('Kegiatan')
        ->and($berita->user_id)->toBe($user->id);
});

test('admin can create berita with an uploaded image on the supabase disk', function () {
    fakeSupabaseDisk();

    $this->actingAs(User::factory()->admin()->create())
        ->post('/admin/berita', [
            'judul' => 'Berita Dengan Foto',
            'slug' => 'berita-dengan-foto',
            'jenis' => 'Kegiatan',
            'isi' => 'Isi berita berfoto.',
            'gambar' => fakePngUpload('foto-berita.png'),
            'status' => 1,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/berita');

    $berita = Berita::sole();

    expect($berita->gambar)->not->toBeNull();

    Storage::disk('supabase')->assertExists($berita->gambar);
});

test('gambar utama resolves the supabase url and keeps absolute urls', function () {
    $user = User::factory()->admin()->create();

    $relatif = Berita::factory()->create(['user_id' => $user->id, 'gambar' => 'berita/rapat.jpg']);
    $absolut = Berita::factory()->create(['user_id' => $user->id, 'gambar' => 'https://picsum.photos/800/600']);
    $kosong = Berita::factory()->create(['user_id' => $user->id, 'gambar' => null]);

    expect($relatif->gambar_utama)->toBe(Storage::disk('supabase')->url('berita/rapat.jpg'))
        ->and($absolut->gambar_utama)->toBe('https://picsum.photos/800/600')
        ->and($kosong->gambar_utama)->toBeNull();
});

test('slug is generated from judul when slug is left empty', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post('/admin/berita', [
            'judul' => 'Syuriah Gelar Mubes',
            'slug' => '',
            'jenis' => 'Pengumuman',
            'isi' => 'Isi berita mubes.',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/berita');

    expect(Berita::sole()->slug)->toBe('syuriah-gelar-mubes');
});

test('invalid jenis is rejected', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post('/admin/berita', [
            'judul' => 'Berita Validasi Jenis',
            'slug' => 'berita-validasi-jenis',
            'jenis' => 'Opini',
            'isi' => 'Isi berita.',
        ])
        ->assertSessionHasErrors('jenis');
});

test('jenis is required', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post('/admin/berita', [
            'judul' => 'Berita Tanpa Jenis',
            'slug' => 'berita-tanpa-jenis',
            'isi' => 'Isi berita.',
        ])
        ->assertSessionHasErrors('jenis');
});

test('duplicate slug is rejected', function () {
    $user = User::factory()->admin()->create();

    Berita::create([
        'judul' => 'Berita Pertama',
        'slug' => 'berita-pertama',
        'jenis' => 'Artikel',
        'isi' => 'Isi berita pertama.',
        'user_id' => $user->id,
        'status' => true,
    ]);

    $this->actingAs($user)
        ->post('/admin/berita', [
            'judul' => 'Berita Kedua',
            'slug' => 'berita-pertama',
            'jenis' => 'Artikel',
            'isi' => 'Isi berita kedua.',
        ])
        ->assertSessionHasErrors('slug');
});

test('admin can update jenis and slug of a berita', function () {
    $user = User::factory()->admin()->create();

    $berita = Berita::create([
        'judul' => 'Berita Lama',
        'slug' => 'berita-lama',
        'jenis' => 'Berita',
        'isi' => 'Isi berita lama.',
        'user_id' => $user->id,
        'status' => true,
    ]);

    $this->actingAs($user)
        ->put("/admin/berita/{$berita->id}", [
            'judul' => 'Berita Lama',
            'slug' => 'berita-lama-diubah',
            'jenis' => 'Pengumuman',
            'isi' => 'Isi berita diperbarui.',
            'status' => 1,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/berita');

    expect($berita->refresh()->slug)->toBe('berita-lama-diubah')
        ->and($berita->jenis)->toBe('Pengumuman');
});

test('slug can be left empty on update and is regenerated from judul', function () {
    $user = User::factory()->admin()->create();

    $berita = Berita::create([
        'judul' => 'Judul Baru Sekali',
        'slug' => 'judul-baru-sekali-2',
        'jenis' => 'Berita',
        'isi' => 'Isi berita.',
        'user_id' => $user->id,
        'status' => true,
    ]);

    $this->actingAs($user)
        ->put("/admin/berita/{$berita->id}", [
            'judul' => 'Judul Baru',
            'slug' => '',
            'jenis' => 'Kegiatan',
            'isi' => 'Isi berita.',
            'status' => 1,
        ])
        ->assertSessionHasNoErrors();

    expect($berita->refresh()->slug)->toBe('judul-baru');
});

test('berita form shows jenis options and slug field', function () {
    $content = $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/berita/create')
        ->assertOk()
        ->assertSee('name="slug"', false)
        ->assertSee('name="jenis"', false)
        ->getContent();

    foreach (Berita::JENIS as $jenis) {
        expect($content)->toContain('value="'.$jenis.'"');
    }
});

test('admin berita list shows jenis and slug', function () {
    $user = User::factory()->admin()->create();

    Berita::create([
        'judul' => 'Berita Ditampilkan',
        'slug' => 'berita-ditampilkan',
        'jenis' => 'Pengumuman',
        'isi' => 'Isi berita.',
        'user_id' => $user->id,
        'status' => true,
    ]);

    $this->actingAs($user)
        ->get('/admin/berita')
        ->assertOk()
        ->assertSee('Pengumuman')
        ->assertSee('/berita-ditampilkan', false);
});

test('public berita detail page shows jenis', function () {
    $user = User::factory()->admin()->create();

    $berita = Berita::create([
        'judul' => 'Berita Publik',
        'slug' => 'berita-publik',
        'jenis' => 'Pengumuman',
        'isi' => 'Isi berita publik.',
        'user_id' => $user->id,
        'status' => true,
    ]);

    $this->get("/berita/{$berita->slug}")
        ->assertOk()
        ->assertSee('Pengumuman');
});

function seedBeritasForSearch(): User
{
    $user = User::factory()->admin()->create();

    Berita::create([
        'judul' => 'Rapat Koordinasi',
        'slug' => 'rapat-koordinasi',
        'jenis' => 'Kegiatan',
        'isi' => 'Agenda rapat koordinasi pengurus.',
        'user_id' => $user->id,
        'status' => true,
    ]);

    Berita::create([
        'judul' => 'Syuriah Gelar Mubes',
        'slug' => 'syuriah-gelar-mubes',
        'jenis' => 'Pengumuman',
        'isi' => 'Pengumuman mubes tahunan.',
        'user_id' => $user->id,
        'status' => true,
    ]);

    Berita::create([
        'judul' => 'Draft Internal',
        'slug' => 'draft-internal',
        'jenis' => 'Artikel',
        'isi' => 'Artikel yang belum dipublish.',
        'user_id' => $user->id,
        'status' => false,
    ]);

    return $user;
}

test('admin can search berita by judul, slug, and isi', function () {
    $user = seedBeritasForSearch();

    $this->actingAs($user)
        ->get('/admin/berita?q=mubes')
        ->assertOk()
        ->assertSee('Syuriah Gelar Mubes')
        ->assertDontSee('Rapat Koordinasi')
        ->assertDontSee('Draft Internal');

    $this->actingAs($user)
        ->get('/admin/berita?q=rapat-koordinasi')
        ->assertOk()
        ->assertSee('Rapat Koordinasi')
        ->assertDontSee('Syuriah Gelar Mubes');

    $this->actingAs($user)
        ->get('/admin/berita?q=agenda+rapat')
        ->assertOk()
        ->assertSee('Rapat Koordinasi')
        ->assertDontSee('Syuriah Gelar Mubes');
});

test('admin can filter berita by jenis', function () {
    $user = seedBeritasForSearch();

    $this->actingAs($user)
        ->get('/admin/berita?jenis=Kegiatan')
        ->assertOk()
        ->assertSee('Rapat Koordinasi')
        ->assertDontSee('Syuriah Gelar Mubes')
        ->assertDontSee('Draft Internal');
});

test('unknown jenis filter is ignored on the admin list', function () {
    $user = seedBeritasForSearch();

    $this->actingAs($user)
        ->get('/admin/berita?jenis=TidakAda')
        ->assertOk()
        ->assertSee('Rapat Koordinasi')
        ->assertSee('Syuriah Gelar Mubes')
        ->assertSee('Draft Internal');
});

test('admin search shows empty state when nothing matches', function () {
    $user = seedBeritasForSearch();

    $this->actingAs($user)
        ->get('/admin/berita?q=zzzzz')
        ->assertOk()
        ->assertDontSee('Rapat Koordinasi')
        ->assertSee('Tidak ada berita yang cocok dengan pencarian Anda.');
});

test('public berita list can be searched', function () {
    seedBeritasForSearch();

    $this->get('/berita?q=mubes')
        ->assertOk()
        ->assertSee('Hasil Pencarian')
        ->assertSee('Syuriah Gelar Mubes')
        ->assertDontSee('Rapat Koordinasi');
});

test('public berita list can be filtered by jenis', function () {
    seedBeritasForSearch();

    $this->get('/berita?jenis=Kegiatan')
        ->assertOk()
        ->assertSee('Rapat Koordinasi')
        ->assertDontSee('Syuriah Gelar Mubes');
});

test('public search never shows draft berita', function () {
    seedBeritasForSearch();

    $this->get('/berita?q=internal')
        ->assertOk()
        ->assertDontSee('Draft Internal');
});

test('public search shows empty state when nothing matches', function () {
    seedBeritasForSearch();

    $this->get('/berita?q=zzzzz')
        ->assertOk()
        ->assertSee('Tidak ada berita yang cocok');
});

test('search and jenis filters are kept in the public pagination links', function () {
    $user = seedBeritasForSearch();

    foreach (range(1, 6) as $i) {
        Berita::create([
            'judul' => "Berita Tambahan {$i}",
            'slug' => "berita-tambahan-{$i}",
            'jenis' => 'Artikel',
            'isi' => 'Isi berita tambahan.',
            'user_id' => $user->id,
            'status' => true,
        ]);
    }

    $this->get('/berita?q=berita&jenis=Artikel')
        ->assertOk()
        ->assertSee('page=2', false);
});

test('public berita list renders the editorial layout when there is no search', function () {
    seedBeritasForSearch();

    $this->get('/berita')
        ->assertOk()
        ->assertSee('Berita Utama')
        ->assertSee('Berita Terbaru')
        ->assertSee('Terbaru dari Redaksi')
        ->assertDontSee('Hasil Pencarian');
});

/**
 * Urutan kemunculan judul berita di dalam HTML halaman.
 */
function urutanTampil(string $html, array $juduls): array
{
    $posisi = [];

    foreach ($juduls as $judul) {
        $pos = strpos($html, $judul);
        if ($pos !== false) {
            $posisi[$judul] = $pos;
        }
    }

    asort($posisi);

    return array_keys($posisi);
}

function seedBeritasPerUrutan(): User
{
    $user = User::factory()->admin()->create();

    $data = [
        ['judul' => 'Laporan Pertama Banom', 'views' => 10],
        ['judul' => 'Laporan Kedua Banom', 'views' => 50],
        ['judul' => 'Laporan Ketiga Banom', 'views' => 30],
    ];

    foreach ($data as $item) {
        Berita::factory()->create([
            'judul' => $item['judul'],
            'slug' => Str::slug($item['judul']),
            'jenis' => 'Kegiatan',
            'isi' => 'Isi laporan banom.',
            'user_id' => $user->id,
            'status' => true,
            'views' => $item['views'],
        ]);
    }

    return $user;
}

test('public berita list sorts by terpopuler', function () {
    seedBeritasPerUrutan();

    $content = $this->get('/berita?q=laporan&sort=terpopuler')->assertOk()->getContent();

    expect(urutanTampil($content, [
        'Laporan Pertama Banom',
        'Laporan Kedua Banom',
        'Laporan Ketiga Banom',
    ]))->toBe([
        'Laporan Kedua Banom',   // 50 dibaca
        'Laporan Ketiga Banom',  // 30 dibaca
        'Laporan Pertama Banom', // 10 dibaca
    ]);
});

test('public berita list sorts by terbaru and terlama', function () {
    seedBeritasPerUrutan();

    $baru = $this->get('/berita?q=laporan&sort=terbaru')->assertOk()->getContent();
    expect(urutanTampil($baru, [
        'Laporan Pertama Banom',
        'Laporan Kedua Banom',
        'Laporan Ketiga Banom',
    ]))->toBe([
        'Laporan Ketiga Banom',
        'Laporan Kedua Banom',
        'Laporan Pertama Banom',
    ]);

    $lama = $this->get('/berita?q=laporan&sort=terlama')->assertOk()->getContent();
    expect(urutanTampil($lama, [
        'Laporan Pertama Banom',
        'Laporan Kedua Banom',
        'Laporan Ketiga Banom',
    ]))->toBe([
        'Laporan Pertama Banom',
        'Laporan Kedua Banom',
        'Laporan Ketiga Banom',
    ]);
});

test('unknown sort option falls back to terbaru on the public list', function () {
    seedBeritasPerUrutan();

    $content = $this->get('/berita?q=laporan&sort=acak-kadang')->assertOk()->getContent();

    expect(urutanTampil($content, [
        'Laporan Pertama Banom',
        'Laporan Kedua Banom',
        'Laporan Ketiga Banom',
    ]))->toBe([
        'Laporan Ketiga Banom',
        'Laporan Kedua Banom',
        'Laporan Pertama Banom',
    ]);
});

test('sort option is kept in the public pagination links', function () {
    $user = seedBeritasPerUrutan();

    foreach (range(1, 6) as $i) {
        Berita::factory()->create([
            'judul' => "Laporan Tambahan {$i}",
            'slug' => "laporan-tambahan-{$i}",
            'jenis' => 'Kegiatan',
            'isi' => 'Isi laporan tambahan.',
            'user_id' => $user->id,
            'status' => true,
            'views' => $i,
        ]);
    }

    $this->get('/berita?q=laporan&sort=terpopuler')
        ->assertOk()
        ->assertSee('sort=terpopuler', false);
});

test('public berita list highlights the selected sort option', function () {
    seedBeritasPerUrutan();

    $this->get('/berita?sort=terpopuler')
        ->assertOk()
        ->assertSee('Terpopuler')
        ->assertSee('Berita Terpopuler')
        ->assertDontSee('Berita Terbaru');
});

test('public news card shows the view count', function () {
    $user = seedBeritasPerUrutan();

    $this->get('/berita?q=laporan')
        ->assertOk()
        ->assertSee('dibaca')
        ->assertSee('50 dibaca');

    expect(Berita::where('judul', 'Laporan Kedua Banom')->value('views'))->toBe(50)
        ->and($user->exists)->toBeTrue();
});

test('public berita detail page counts the view and shows it', function () {
    $user = User::factory()->admin()->create();

    $berita = Berita::factory()->create([
        'judul' => 'Berita Terhitung',
        'slug' => 'berita-terhitung',
        'jenis' => 'Berita',
        'isi' => 'Isi berita.',
        'user_id' => $user->id,
        'status' => true,
        'views' => 5,
    ]);

    $this->get("/berita/{$berita->slug}")
        ->assertOk()
        ->assertSee('6 kali dibaca');

    expect($berita->refresh()->views)->toBe(6);
});

test('draft berita view count is not incremented', function () {
    $user = User::factory()->admin()->create();

    $berita = Berita::factory()->create([
        'judul' => 'Draft Tidak Dihitung',
        'slug' => 'draft-tidak-dihitung',
        'jenis' => 'Artikel',
        'isi' => 'Isi draft.',
        'user_id' => $user->id,
        'status' => false,
        'views' => 3,
    ]);

    $this->get("/berita/{$berita->slug}")->assertNotFound();

    expect($berita->refresh()->views)->toBe(3);
});

test('admin berita list shows the view statistics', function () {
    seedBeritasPerUrutan();

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/berita')
        ->assertOk()
        ->assertSee('Total Berita')
        ->assertSee('Total Dibaca')
        ->assertSee('Rata-rata / Berita')
        ->assertSee('90') // 10 + 50 + 30
        ->assertSee('30'); // rata-rata 90/3
});

test('admin berita list shows the views column and popular ranking', function () {
    seedBeritasPerUrutan();

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/berita')
        ->assertOk()
        ->assertSee('Dibaca')
        ->assertSee('Paling Banyak Dibaca')
        ->assertSeeInOrder([
            'Laporan Kedua Banom',   // 50 dibaca
            'Laporan Ketiga Banom',  // 30 dibaca
            'Laporan Pertama Banom', // 10 dibaca
        ]);
});

test('admin berita list can be sorted by views, newest, and oldest', function () {
    $user = seedBeritasPerUrutan();

    $terpopuler = $this->actingAs($user)->get('/admin/berita?sort=terpopuler')->assertOk()->getContent();
    expect(urutanTampil($terpopuler, [
        'Laporan Pertama Banom',
        'Laporan Kedua Banom',
        'Laporan Ketiga Banom',
    ]))->toBe([
        'Laporan Kedua Banom',
        'Laporan Ketiga Banom',
        'Laporan Pertama Banom',
    ]);

    $terbaru = $this->actingAs($user)->get('/admin/berita?sort=terbaru')->assertOk()->getContent();
    expect(urutanTampil($terbaru, [
        'Laporan Pertama Banom',
        'Laporan Kedua Banom',
        'Laporan Ketiga Banom',
    ]))->toBe([
        'Laporan Ketiga Banom',
        'Laporan Kedua Banom',
        'Laporan Pertama Banom',
    ]);

    $terlama = $this->actingAs($user)->get('/admin/berita?sort=terlama')->assertOk()->getContent();
    expect(urutanTampil($terlama, [
        'Laporan Pertama Banom',
        'Laporan Kedua Banom',
        'Laporan Ketiga Banom',
    ]))->toBe([
        'Laporan Pertama Banom',
        'Laporan Kedua Banom',
        'Laporan Ketiga Banom',
    ]);
});

test('unknown sort option falls back to terbaru on the admin list', function () {
    $user = seedBeritasPerUrutan();

    $content = $this->actingAs($user)->get('/admin/berita?sort=ngawur')->assertOk()->getContent();

    expect(urutanTampil($content, [
        'Laporan Pertama Banom',
        'Laporan Kedua Banom',
        'Laporan Ketiga Banom',
    ]))->toBe([
        'Laporan Ketiga Banom',
        'Laporan Kedua Banom',
        'Laporan Pertama Banom',
    ]);
});

/*
|--------------------------------------------------------------------------
| Statistik & grafik pembaca (admin)
|--------------------------------------------------------------------------
*/

test('admin statistik berita page shows the charts', function () {
    seedBeritasPerUrutan();

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/berita/statistik')
        ->assertOk()
        ->assertSee('Statistik Pembaca Berita')
        ->assertSee('Tren Pembaca 30 Hari Terakhir')
        ->assertSee('10 Berita Terpopuler')
        ->assertSee('Pembaca per Kanal')
        ->assertSee('Total Dibaca');
});

test('admin statistik page needs an admin account', function () {
    $this->get('/admin/berita/statistik')->assertRedirect();

    $this->actingAs(User::factory()->create())
        ->get('/admin/berita/statistik')
        ->assertForbidden();
});

test('visiting a berita records the daily reader for the chart', function () {
    $user = User::factory()->admin()->create();

    $berita = Berita::factory()->create([
        'judul' => 'Berita Bergrafik',
        'slug' => 'berita-bergrafik',
        'jenis' => 'Kegiatan',
        'isi' => 'Isi berita.',
        'user_id' => $user->id,
        'status' => true,
        'views' => 0,
    ]);

    expect(BeritaView::count())->toBe(0);

    $this->get("/berita/{$berita->slug}")->assertOk();

    $rekap = BeritaView::first();

    expect($rekap->berita_id)->toBe($berita->id)
        ->and($rekap->tanggal->toDateString())->toBe(now()->toDateString())
        ->and($rekap->jumlah)->toBe(1)
        ->and($berita->refresh()->views)->toBe(1);

    // Kunjungan berikutnya menambah rekap hari ini, bukan membuat baris baru.
    $this->get("/berita/{$berita->slug}")->assertOk();

    expect(BeritaView::count())->toBe(1)
        ->and($berita->refresh()->views)->toBe(2)
        ->and(BeritaView::first()->jumlah)->toBe(2);
});

test('daily reader recap appears on the admin statistik page', function () {
    $user = User::factory()->admin()->create();

    $berita = Berita::factory()->create([
        'judul' => 'Berita Rekap Harian',
        'slug' => 'berita-rekap-harian',
        'jenis' => 'Pengumuman',
        'isi' => 'Isi berita.',
        'user_id' => $user->id,
        'status' => true,
        'views' => 7,
    ]);

    foreach (range(1, 3) as $i) {
        $this->get("/berita/{$berita->slug}")->assertOk();
    }

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/berita/statistik')
        ->assertOk()
        ->assertSee('Tren Pembaca 30 Hari Terakhir')
        ->assertDontSee('Belum ada pembaca tercatat')
        ->assertSee('Berita Rekap Harian');
});

test('statistik berita page is reachable and not swallowed by the berita resource', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/berita/statistik')
        ->assertOk()
        ->assertSee('Tren Pembaca 30 Hari Terakhir');
});

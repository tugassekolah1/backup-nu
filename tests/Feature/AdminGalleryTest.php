<?php

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function adminGaleri(): User
{
    return User::factory()->admin()->create();
}

/**
 * PNG 1x1 valid agar rule `image` lolos tanpa perlu ekstensi GD.
 */
function fotoGaleriPalsu(): UploadedFile
{
    $png = base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
    );

    return UploadedFile::fake()->createWithContent('foto-galeri.png', $png);
}

test('the gallery create form offers the verified organisations grouped by category', function () {
    $this->actingAs(adminGaleri())
        ->get(route('gallery.create'))
        ->assertOk()
        ->assertSee('id="organisasi"', false)
        ->assertSee('Tanpa organisasi (kegiatan umum)', false)
        // Kategori berasal dari config('organisasi.kategori').
        ->assertSee('Badan Otonom', false)
        ->assertSee('Lembaga NU', false)
        ->assertSee('Pengurus NU', false)
        // Organisasi terverifikasi ditawarkan; yang contoh data tidak.
        ->assertSee('value="gp-ansor"', false)
        ->assertDontSee('value="pagar-nusa"', false)
        ->assertDontSee('value="prnu-contoh-1"', false);
});

test('the organisasi options come from the verified organisasi config', function () {
    $opsi = Gallery::daftarOrganisasi();
    $terverifikasi = collect(config('organisasi.organisasi'))->where('terverifikasi', true);

    // Keanggotaan daftarnya identik dengan daftar terverifikasi di config
    // (urutannya berbeda: di sini dikelompokkan per kategori lalu alfabet).
    expect(array_column($opsi, 'slug'))->toEqualCanonicalizing($terverifikasi->pluck('slug')->all());
    expect(array_column($opsi, 'slug'))->toContain('mwcnu', 'gp-ansor', 'lazisnu');

    // Nama pendek dirapikan (singkatan di config ada yang berspasi di ekor).
    expect(collect($opsi)->firstWhere('slug', 'prnu')['singkatan'])->toBe('PRNU');
});

test('organisasi options are grouped by category in directory order', function () {
    $urutKategori = collect(Gallery::daftarOrganisasi())->pluck('kategori');

    expect($urutKategori->all())->toBe([
        'pengurus', 'pengurus',
        'banom', 'banom', 'banom', 'banom', 'banom',
        'lembaga', 'lembaga', 'lembaga',
    ]);

    $grup = collect(Gallery::opsiOrganisasiPerKategori());

    expect($grup->pluck('kode')->all())->toBe(['pengurus', 'banom', 'lembaga']);
    expect($grup->firstWhere('kode', 'banom')['label'])->toBe('Badan Otonom');
    expect($grup->firstWhere('kode', 'banom')['opsi'])->toHaveCount(5);
});

test('admin can store a photo tagged with an organisation', function () {
    Storage::fake('public');

    $this->actingAs(adminGaleri())
        ->post(route('gallery.store'), [
            'judul' => 'Rapat Kerja GP Ansor',
            'organisasi' => 'gp-ansor',
            'deskripsi' => 'Rapat kerja pengurus.',
            'foto' => fotoGaleriPalsu(),
            'urutan' => 1,
        ])
        ->assertRedirect(route('gallery.index'));

    $foto = Gallery::firstWhere('judul', 'Rapat Kerja GP Ansor');

    expect($foto)->not->toBeNull();
    expect($foto->organisasi)->toBe('gp-ansor');
    expect($foto->organisasiLabel())->toBe('GP Ansor');
});

test('a photo can be saved without any organisation', function () {
    Storage::fake('public');

    $this->actingAs(adminGaleri())
        ->post(route('gallery.store'), [
            'judul' => 'Pengajian Rutin Umum',
            'organisasi' => '',
            'foto' => fotoGaleriPalsu(),
        ])
        ->assertRedirect(route('gallery.index'));

    $foto = Gallery::firstWhere('judul', 'Pengajian Rutin Umum');

    expect($foto->organisasi)->toBeNull();
    expect($foto->organisasiLabel())->toBe('');
});

test('admin cannot store a photo with an unknown organisation', function () {
    Storage::fake('public');

    $this->actingAs(adminGaleri())
        ->from(route('gallery.create'))
        ->post(route('gallery.store'), [
            'judul' => 'Foto Ditolak',
            'organisasi' => 'organisasi-tidak-ada',
            'foto' => fotoGaleriPalsu(),
        ])
        ->assertRedirect(route('gallery.create'))
        ->assertSessionHasErrors('organisasi');

    expect(Gallery::count())->toBe(0);
});

test('admin can change the organisation of an existing photo', function () {
    Storage::fake('public');

    $foto = Gallery::create([
        'judul' => 'Foto Lama',
        'organisasi' => 'muslimat',
        'foto' => 'galleries/lama.jpg',
        'urutan' => 1,
    ]);

    $this->actingAs(adminGaleri())
        ->put(route('gallery.update', $foto), [
            'judul' => 'Foto Lama',
            'organisasi' => 'ipnu',
            'urutan' => 1,
        ])
        ->assertRedirect(route('gallery.index'));

    expect($foto->fresh()->organisasi)->toBe('ipnu');
});

test('the edit form keeps a legacy organisation slug that left the directory', function () {
    $foto = Gallery::create([
        'judul' => 'Foto Data Lama',
        'organisasi' => 'banom-sudah-tidak-ada',
        'foto' => 'galleries/lama.jpg',
        'urutan' => 1,
    ]);

    $this->actingAs(adminGaleri())
        ->get(route('gallery.edit', $foto))
        ->assertOk()
        ->assertSee('value="banom-sudah-tidak-ada"', false)
        ->assertSee('Data lama', false);

    // Label tetap terbaca meski slug tidak ada di config.
    expect($foto->organisasiLabel())->toBe('banom-sudah-tidak-ada');
});

test('admin can filter the gallery list by organisation', function () {
    Gallery::create(['judul' => 'Foto Ansor Saja', 'organisasi' => 'gp-ansor', 'foto' => 'galleries/a.jpg', 'urutan' => 1]);
    Gallery::create(['judul' => 'Foto Muslimat Saja', 'organisasi' => 'muslimat', 'foto' => 'galleries/b.jpg', 'urutan' => 2]);
    Gallery::create(['judul' => 'Foto Umum Banjar', 'organisasi' => null, 'foto' => 'galleries/c.jpg', 'urutan' => 3]);

    $this->actingAs(adminGaleri())
        ->get(route('gallery.index', ['organisasi' => 'gp-ansor']))
        ->assertOk()
        ->assertSee('Foto Ansor Saja')
        ->assertDontSee('Foto Muslimat Saja')
        ->assertDontSee('Foto Umum Banjar');
});

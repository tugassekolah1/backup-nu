<?php

use App\Models\Gallery;
use App\Models\Pengurus;
use Illuminate\Support\Facades\Storage;

test('landing page renders the shared public footer', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Tautan Navigasi')
        ->assertSee('NU KECAMATAN CILONGOK');
});

test('landing page shows the core pengurus inti section', function () {
    Pengurus::create([
        'nama' => 'Ahmad Fauzi',
        'jabatan' => 'Ketua',
        'banom' => 'mwcnu',
        'label_banom' => 'MWCNU',
        'urutan' => 1,
    ]);

    Pengurus::create([
        'nama' => 'Bambang Anggota',
        'jabatan' => 'Anggota',
        'banom' => 'mwcnu',
        'label_banom' => 'MWCNU',
        'urutan' => 5,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Pengurus Inti')
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Bambang Anggota');
});

test('/profil redirects to the organisasi profil tab', function () {
    $this->get('/profil')
        ->assertRedirect('/organisasi/mwcnu?tab=profil');
});

test('/struktur redirects to the organisasi struktur tab', function () {
    $this->get('/struktur')
        ->assertRedirect('/organisasi/mwcnu?tab=struktur');
});

test('pengurus are listed on the struktur tab of their own organisation', function () {
    Pengurus::create([
        'nama' => 'Ahmad Fauzi',
        'jabatan' => 'Ketua',
        'banom' => 'ranting',
        'label_banom' => 'Ranting NU',
        'urutan' => 1,
    ]);

    Pengurus::create([
        'nama' => 'Zahra Aulia',
        'jabatan' => 'Ketua',
        'banom' => 'pac_ipnu',
        'label_banom' => 'PAC IPNU',
        'urutan' => 2,
    ]);

    $this->get(route('organisasi.show', ['slug' => 'prnu', 'tab' => 'struktur']))
        ->assertOk()
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Zahra Aulia');

    $this->get(route('organisasi.show', ['slug' => 'ipnu', 'tab' => 'struktur']))
        ->assertOk()
        ->assertSee('Zahra Aulia')
        ->assertDontSee('Ahmad Fauzi');
});

test('pengurus from an extra banom code appear on the parent organisation page', function () {
    Pengurus::create([
        'nama' => 'Slamet Riyadi',
        'jabatan' => 'Komandan',
        'banom' => 'satkoryon_banser',
        'label_banom' => 'Satkoryon Banser',
        'urutan' => 1,
    ]);

    // Satkoryon Banser menaungi GP Ansor, jadi tampil di halaman GP Ansor.
    $this->get(route('organisasi.show', ['slug' => 'gp-ansor', 'tab' => 'struktur']))
        ->assertOk()
        ->assertSee('Slamet Riyadi');
});

test('struktur pengurus can be filtered by jabatan', function () {
    Pengurus::create([
        'nama' => 'Ahmad Fauzi',
        'jabatan' => 'Ketua',
        'banom' => 'mwcnu',
        'label_banom' => 'MWCNU',
        'urutan' => 1,
    ]);

    Pengurus::create([
        'nama' => 'Budi Santoso',
        'jabatan' => 'Sekretaris',
        'banom' => 'mwcnu',
        'label_banom' => 'MWCNU',
        'urutan' => 2,
    ]);

    $this->get(route('organisasi.show', ['slug' => 'mwcnu', 'tab' => 'struktur', 'jabatan' => 'Sekretaris']))
        ->assertOk()
        ->assertSee('Sekretaris (1)')
        ->assertSee('Budi Santoso')
        ->assertDontSee('Ahmad Fauzi');

    // Nilai jabatan yang tidak dikenal jatuh kembali ke bagan lengkap.
    $this->get(route('organisasi.show', ['slug' => 'mwcnu', 'tab' => 'struktur', 'jabatan' => 'Ngawur']))
        ->assertOk()
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Budi Santoso');
});

test('profil tab shows visi, misi, and the short history for first time visitors', function () {
    $this->get(route('organisasi.show', ['slug' => 'mwcnu', 'tab' => 'profil']))
        ->assertOk()
        ->assertSee('Visi')
        ->assertSee('Misi')
        ->assertSee('Sejarah Singkat')
        ->assertSee('Awal Mula dan Tradisi Pesantren')
        ->assertSee('Tonggak Penting')
        ->assertSee('Pelopor Pendidikan Ma\'arif');
});

test('galeri page renders the shared public head and footer', function () {
    $this->get('/galeri')
        ->assertOk()
        ->assertSee('Dokumentasi rekam jejak')
        ->assertSee('Tautan Navigasi')
        ->assertSee('NU KECAMATAN CILONGOK');
});

test('galeri page renders the collage of photos with lightbox triggers', function () {
    Gallery::create([
        'judul' => 'Rapat Kerja Pengurus',
        'deskripsi' => 'Rapat kerja pengurus ranting.',
        'foto' => 'galleries/rapat.jpg',
        'urutan' => 1,
    ]);

    Gallery::create([
        'judul' => 'Bakti Sosial Ramadhan',
        'deskripsi' => 'Bakti sosial bersama warga.',
        'foto' => 'https://example.com/bakti.jpg',
        'urutan' => 2,
    ]);

    $this->get('/galeri')
        ->assertOk()
        ->assertSee('Semua Dokumentasi')
        ->assertSee('Rapat Kerja Pengurus')
        ->assertSee('Bakti Sosial Ramadhan')
        // Kolase + lightbox
        ->assertSee('columns-1', false)
        ->assertSee('data-galeri-modal', false)
        ->assertSee('id="galeri-modal"', false)
        ->assertSee('id="galeri-modal-img"', false)
        // Path relatif -> disk Supabase; URL absolut tetap apa adanya.
        ->assertSee(Storage::disk('supabase')->url('galleries/rapat.jpg'), false)
        ->assertSee('https://example.com/bakti.jpg', false);
});

test('galeri page shows the empty state when there are no photos', function () {
    $this->get('/galeri')
        ->assertOk()
        ->assertSee('Belum Ada Foto')
        ->assertSee('Kembali ke Beranda');
});

test('gallery photo url resolves relative paths and keeps absolute urls', function () {
    $relatif = Gallery::create(['judul' => 'A', 'foto' => 'galleries/a.jpg', 'urutan' => 1]);
    $absolut = Gallery::create(['judul' => 'B', 'foto' => 'https://example.com/b.jpg', 'urutan' => 2]);
    $kosong = Gallery::create(['judul' => 'C', 'foto' => '', 'urutan' => 3]);

    expect($relatif->url)->toBe(Storage::disk('supabase')->url('galleries/a.jpg'));
    expect($absolut->url)->toBe('https://example.com/b.jpg');
    expect($kosong->url)->toBe('');
});

test('the galeri page offers a filter pill per organisation that has photos', function () {
    Gallery::create(['judul' => 'Foto Ansor', 'organisasi' => 'gp-ansor', 'foto' => 'galleries/a.jpg', 'urutan' => 1]);
    Gallery::create(['judul' => 'Foto Muslimat', 'organisasi' => 'muslimat', 'foto' => 'galleries/b.jpg', 'urutan' => 2]);
    Gallery::create(['judul' => 'Foto Umum', 'organisasi' => null, 'foto' => 'galleries/c.jpg', 'urutan' => 3]);

    $this->get('/galeri')
        ->assertOk()
        ->assertSee('Filter per Organisasi', false)
        ->assertSee(route('galeri.index', ['organisasi' => 'gp-ansor']), false)
        ->assertSee(route('galeri.index', ['organisasi' => 'muslimat']), false)
        // Organisasi tanpa foto tidak ditawarkan agar daftarnya ringkas.
        ->assertDontSee(route('galeri.index', ['organisasi' => 'lazisnu']), false);
});

test('the galeri filter narrows the photos down to one organisation', function () {
    Gallery::create(['judul' => 'Dokumentasi Ansor Saja', 'organisasi' => 'gp-ansor', 'foto' => 'galleries/a.jpg', 'urutan' => 1]);
    Gallery::create(['judul' => 'Dokumentasi Muslimat Saja', 'organisasi' => 'muslimat', 'foto' => 'galleries/b.jpg', 'urutan' => 2]);
    Gallery::create(['judul' => 'Dokumentasi Umum Banjar', 'organisasi' => null, 'foto' => 'galleries/c.jpg', 'urutan' => 3]);

    $this->get('/galeri?organisasi=gp-ansor')
        ->assertOk()
        ->assertSee('Dokumentasi GP Ansor')
        ->assertSee('Dokumentasi Ansor Saja')
        ->assertDontSee('Dokumentasi Muslimat Saja')
        ->assertDontSee('Dokumentasi Umum Banjar');
});

test('an unknown organisasi slug on the galeri page falls back to all photos', function () {
    Gallery::create(['judul' => 'Satu Foto', 'organisasi' => 'gp-ansor', 'foto' => 'galleries/a.jpg', 'urutan' => 1]);

    $this->get('/galeri?organisasi=bukan-organisasi')
        ->assertOk()
        ->assertSee('Semua Dokumentasi')
        ->assertSee('Satu Foto');
});

test('each photo on the galeri page shows its organisation label', function () {
    Gallery::create(['judul' => 'Foto Ansor', 'organisasi' => 'gp-ansor', 'foto' => 'galleries/a.jpg', 'urutan' => 1]);
    Gallery::create(['judul' => 'Foto Umum', 'organisasi' => null, 'foto' => 'galleries/c.jpg', 'urutan' => 2]);

    $this->get('/galeri')
        ->assertOk()
        ->assertSee('data-organisasi="GP Ansor"', false)
        ->assertSee('data-organisasi=""', false);
});

test('an organisation filter with no photos explains itself and links back', function () {
    Gallery::create(['judul' => 'Foto Ansor', 'organisasi' => 'gp-ansor', 'foto' => 'galleries/a.jpg', 'urutan' => 1]);

    $this->get('/galeri?organisasi=lazisnu')
        ->assertOk()
        ->assertSee('Belum Ada Foto dari LAZISNU', false)
        ->assertSee('Lihat Semua Foto', false)
        // `data-src=` hanya dipakai tombol pemicu lightbox pada foto yang dirender.
        ->assertDontSee('data-src=', false);
});

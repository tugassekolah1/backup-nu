<?php

use App\Models\BeritaView;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Backfill rekap harian pembaca berita
|--------------------------------------------------------------------------
|
| Total pembaca tersimpan sebagai angka kumulatif di `beritas.views` tanpa
| riwayat per hari, sehingga grafik tren 30 hari bisa terlihat kosong meski
| beritanya banyak dibaca. Perintah ini menyebarnya kembali ke hari-hari
| sejak tiap berita terbit supaya grafiknya terisi.
|
| Aman dijalankan berkali-kali: baris hasil pencatatan kunjungan asli tidak
| pernah ditimpa.
|
*/
Artisan::command('berita:backfill-pembaca', function () {
    $sebelum = BeritaView::count();

    $dibuat = BeritaView::backfillDariTotalViews();

    $this->info("Rekap harian: {$sebelum} baris sebelumnya, {$dibuat} baris baru ditambahkan.");
    $this->line('Total sekarang: '.BeritaView::count().' baris.');
    $this->line('Grafik tren 30 hari pada halaman Statistik Berita kini terisi.');
})->purpose('Sebar total views berita ke rekap harian agar grafik tren 30 hari terisi');

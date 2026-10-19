<?php

use App\Http\Controllers\Admin\AspirasiController as AdminAspirasiController;
use App\Http\Controllers\Admin\InfaqController as AdminInfaqController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AspirasiController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\InfaqController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\PengurusController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [BeritaController::class, 'landing'])->name('landing');

// Member Registration & Search Card
Route::get('/daftar', [MemberController::class, 'registerForm'])->name('members.register-form');
Route::post('/daftar', [MemberController::class, 'register'])
    ->middleware('throttle:20,1')
    ->name('members.register');

// Kartu Anggota — halaman utama gabungan: cek status pengajuan + preview/cetak/download kartu
Route::get('/kartu-anggota', [MemberController::class, 'memberCard'])->name('members.card');

// Pencarian & Cetak Kartu Anggota (Publik) — RUTE LAMA, diarahkan ke /kartu-anggota
Route::get('/cek-kartu', [MemberController::class, 'searchCard'])->name('members.search');

// Cek Status Pendaftaran Anggota (Publik) — RUTE LAMA, diarahkan ke /kartu-anggota
Route::get('/cek-status', [MemberController::class, 'statusCheck'])->name('members.status-check');

// Public Pages (Non-conflicting)
Route::get('/organisasi', [OrganisasiController::class, 'index'])->name('organisasi');
Route::get('/organisasi/{slug}', [OrganisasiController::class, 'show'])->name('organisasi.show');

// RUTE LAMA — halaman profil & piramida struktur lama sudah digabung ke /organisasi.
// Diarahkan agar tautan lama (bookmark, media sosial) tidak rusak.
Route::redirect('/profil', '/organisasi/mwcnu?tab=profil');
Route::redirect('/struktur', '/organisasi/mwcnu?tab=struktur');
Route::get('/struktur/{pengurus}', [PengurusController::class, 'show'])->name('struktur.show');
Route::get('/kontak', fn () => view('kontak'))->name('kontak');
Route::get('/galeri', [GalleryController::class, 'publicIndex'])->name('galeri.index');
Route::get('/agenda', [AgendaController::class, 'publicIndex'])->name('agenda.public');

// Infaq
Route::get('/infaq', [InfaqController::class, 'index'])->name('infaq.index');
Route::post('/infaq', [InfaqController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('infaq.store');
Route::get('/infaq/checkout/{kode}', [InfaqController::class, 'checkout'])->name('infaq.checkout');
Route::post('/infaq/konfirmasi/{kode}', [InfaqController::class, 'confirmProof'])
    ->middleware('throttle:10,1')
    ->name('infaq.proof');
Route::get('/infaq/success/{kode}', [InfaqController::class, 'success'])->name('infaq.success');

// Jalan pintas "simulasi lunas" HANYA tersedia di environment local/testing
// (dev, demo, dan test otomatis). Di produksi rute ini tidak pernah didaftarkan,
// sehingga tidak ada cara menandai donasi lunas tanpa verifikasi admin.
if (app()->environment(['local', 'testing'])) {
    Route::post('/infaq/simulate/{kode}', [InfaqController::class, 'simulatePayment'])->name('infaq.simulate');
}

Route::get('/berita', [BeritaController::class, 'index_publik'])->name('berita.public');

// Kotak Aspirasi (publik)
Route::get('/aspirasi', [AspirasiController::class, 'index'])->name('aspirasi.index');
Route::get('/aspirasi/lacak', [AspirasiController::class, 'lacak'])->name('aspirasi.lacak');
Route::post('/aspirasi', [AspirasiController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('aspirasi.store');
/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::redirect('/admin/dashboard', '/dashboard')->name('admin.dashboard');
});

Route::middleware(['auth', 'admin'])->group(function () {
    // Infaq Management (admin only) — /admin/infaq agar tidak bentrok dengan /infaq publik
    Route::prefix('admin')->name('admin.')->group(function () {
        // Permintaan Pendaftaran Anggota (terima/tolak)
        Route::get('/members/requests', [MemberController::class, 'registrationRequests'])
            ->name('members.requests');
        Route::patch('/members/requests/{member}/accept', [MemberController::class, 'acceptRegistration'])
            ->name('members.requests.accept');
        Route::patch('/members/requests/{member}/reject', [MemberController::class, 'rejectRegistration'])
            ->name('members.requests.reject');

        Route::resource('infaq', AdminInfaqController::class)
            ->parameters(['infaq' => 'infaq'])
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::post('/infaq/{infaq}/lunas', [AdminInfaqController::class, 'markLunas'])
            ->name('infaq.mark-lunas');

        Route::post('/infaq/{infaq}/batal', [AdminInfaqController::class, 'markBatal'])
            ->name('infaq.mark-batal');

        // Kotak Aspirasi (admin only)
        Route::resource('aspirasi', AdminAspirasiController::class)
            ->parameters(['aspirasi' => 'aspirasi'])
            ->only(['index', 'edit', 'update', 'destroy']);
    });

    // News & Gallery Management (Resource akan mendaftarkan /berita/create terlebih dahulu)
    // Rute statistik harus didaftarkan SEBELUM resource, supaya kata "statistik"
    // tidak dimakan oleh parameter {berita} pada /admin/berita/{berita}.
    Route::get('/admin/berita/statistik', [BeritaController::class, 'statistik'])->name('berita.statistik');
    Route::resource('admin/berita', BeritaController::class)
        ->parameters(['berita' => 'berita'])
        ->except(['show']);
    Route::resource('gallery', GalleryController::class)
        ->except(['show']);

    // Members Management
    Route::prefix('admin')->group(function () {
        Route::resource('members', MemberController::class)
            ->parameters(['members' => 'member'])
            ->except(['show']);
        Route::get('/members/{member}/print-card', [MemberController::class, 'printCard'])
            ->name('members.print-card');
        Route::match(['get', 'post'], '/members/cetak-kta-bulk', [MemberController::class, 'printKtaBulk'])
            ->name('members.cetak-kta-bulk');
        Route::get('/members/{member}/download-kta', [MemberController::class, 'downloadKta'])
            ->name('members.download-kta');
    });

    // Pengurus Management
    Route::get('/pengurus', [PengurusController::class, 'adminIndex'])->name('pengurus.index');
    Route::get('/pengurus/create', [PengurusController::class, 'create'])->name('pengurus.create');
    Route::post('/pengurus', [PengurusController::class, 'store'])->name('pengurus.store');
    Route::get('/pengurus/{pengurus}/edit', [PengurusController::class, 'edit'])->name('pengurus.edit');
    Route::put('/pengurus/{pengurus}', [PengurusController::class, 'update'])->name('pengurus.update');
    Route::delete('/pengurus/{pengurus}', [PengurusController::class, 'destroy'])->name('pengurus.destroy');

    // Agenda Management (URL /admin/agenda — halaman publik memakai /agenda)
    Route::prefix('admin')->group(function () {
        Route::resource('agenda', AgendaController::class)
            ->parameters(['agenda' => 'agenda'])
            ->except(['show']);
    });
});

Route::middleware(['auth'])->group(function () {
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// DIPINDAHKAN KE PALING BAWAH agar kata 'create' tidak dimakan oleh parameter {berita:slug}
Route::get('/berita/{berita:slug}', [BeritaController::class, 'show'])->name('berita.show');

require __DIR__.'/auth.php';

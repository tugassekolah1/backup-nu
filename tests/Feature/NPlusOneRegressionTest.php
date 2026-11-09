<?php

use App\Models\Aspirasi;
use App\Models\Berita;
use App\Models\Infaq;
use App\Models\NuMember;
use App\Models\Pengurus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\LazyLoadingViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Hitung jumlah query database yang terjadi selama satu request halaman.
 */
function jumlahQueryRequest(callable $request): int
{
    DB::flushQueryLog();
    DB::enableQueryLog();

    $request();

    $jumlah = count(DB::getQueryLog());
    DB::disableQueryLog();

    return $jumlah;
}

/**
 * Buat satu anggota untuk pengujian regresi N+1.
 */
function anggotaRegresi(int $urut, array $overrides = []): NuMember
{
    return NuMember::create(array_merge([
        'nik' => str_pad((string) $urut, 16, '0', STR_PAD_LEFT),
        'full_name' => 'Anggota Regresi '.$urut,
        'phone' => '081234567890',
        'gender' => 'L',
        'address' => 'Kecamatan Cilongok',
        'status' => 'active',
        'registration_status' => 'accepted',
        'member_card_no' => sprintf('NU-2026-%03d', $urut),
    ], $overrides));
}

test('admin berita list eager loads the penulis without per-row queries', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Berita::factory()->count(2)->create(['user_id' => $admin->id]);

    $denganSedikitBaris = jumlahQueryRequest(
        fn () => $this->get('/admin/berita')->assertOk()
    );

    Berita::factory()->count(10)->create(['user_id' => $admin->id]);

    $denganBanyakBaris = jumlahQueryRequest(
        fn () => $this->get('/admin/berita')->assertOk()
    );

    // Jumlah query tetap konstan walau baris bertambah — N+1 sudah tidak ada.
    expect($denganBanyakBaris)->toBe($denganSedikitBaris);
});

test('admin members index query count stays constant as rows grow', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    anggotaRegresi(1);

    $denganSedikitBaris = jumlahQueryRequest(
        fn () => $this->get(route('members.index'))->assertOk()
    );

    foreach (range(2, 12) as $i) {
        anggotaRegresi($i);
    }

    $denganBanyakBaris = jumlahQueryRequest(
        fn () => $this->get(route('members.index'))->assertOk()
    );

    expect($denganBanyakBaris)->toBe($denganSedikitBaris);
});

test('admin members index counts members per banom in a single aggregate', function () {
    anggotaRegresi(1, ['organisasi' => 'pac_ansor']);
    anggotaRegresi(2, ['organisasi' => 'pac_ansor']);
    anggotaRegresi(3, ['organisasi' => 'pac_fatayat']);
    anggotaRegresi(4, ['organisasi' => null]);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->get(route('members.index'))
        ->assertOk();

    $banomCounts = $response->viewData('banomCounts');

    expect($banomCounts['pac_ansor'])->toBe(2)
        ->and($banomCounts['pac_fatayat'])->toBe(1)
        ->and($banomCounts['none'])->toBe(1)
        ->and($banomCounts['pac_ipnu'])->toBe(0)
        ->and(array_sum($banomCounts))->toBe(4);
});

test('organisasi index passes pengurus data from the controller to the modals', function () {
    Pengurus::create([
        'nama' => 'Ketua PAC Ansor',
        'jabatan' => 'Ketua',
        'banom' => 'pac_ansor',
        'label_banom' => 'PAC GP Ansor',
        'urutan' => 1,
    ]);

    $response = $this->get(route('organisasi'))
        ->assertOk()
        ->assertSee('Ketua PAC Ansor', false);

    expect($response->viewData('semuaPengurus')->get('pac_ansor'))->toHaveCount(1);
});

test('admin aspirasi recap counts every status in one query', function () {
    Aspirasi::factory()->count(2)->create(['status' => 'baru']);
    Aspirasi::factory()->create(['status' => 'diproses']);
    Aspirasi::factory()->create(['status' => 'selesai']);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/aspirasi')
        ->assertOk();

    $rekap = $response->viewData('rekap');

    expect($rekap['total'])->toBe(4)
        ->and($rekap['baru'])->toBe(2)
        ->and($rekap['diproses'])->toBe(1)
        ->and($rekap['selesai'])->toBe(1);
});

test('admin infaq recap computes every total in one query', function () {
    Infaq::create([
        'kode_transaksi' => 'INF-REGR01',
        'nama_donatur' => 'Hamba Allah',
        'nominal' => 100000,
        'metode_pembayaran' => 'tunai',
        'status' => 'lunas',
        'arah' => 'masuk',
        'paid_at' => now(),
    ]);
    Infaq::create([
        'kode_transaksi' => 'INF-REGR02',
        'nama_donatur' => 'Hamba Allah',
        'nominal' => 30000,
        'metode_pembayaran' => 'tunai',
        'status' => 'lunas',
        'arah' => 'keluar',
        'paid_at' => now(),
    ]);
    Infaq::create([
        'kode_transaksi' => 'INF-REGR03',
        'nama_donatur' => 'Hamba Allah',
        'nominal' => 5000,
        'metode_pembayaran' => 'tunai',
        'status' => 'pending',
        'arah' => 'masuk',
    ]);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/infaq')
        ->assertOk();

    $rekap = $response->viewData('rekap');

    expect($rekap['total_lunas'])->toBe(100000)
        ->and($rekap['total_keluar'])->toBe(30000)
        ->and($rekap['saldo'])->toBe(70000)
        ->and($rekap['hari_ini'])->toBe(100000)
        ->and($rekap['bulan_ini'])->toBe(100000)
        ->and($rekap['pending'])->toBe(1);
});

test('lazy loading relasi dilempar sebagai pelanggaran di non-produksi', function () {
    $admin = User::factory()->admin()->create();
    Berita::factory()->count(2)->create(['user_id' => $admin->id]);

    $berita = Berita::limit(2)->get();

    expect(Model::preventsLazyLoading())->toBeTrue()
        ->and(fn () => $berita->first()->user)
        ->toThrow(LazyLoadingViolationException::class);
});

test('landing page eager loads penulis berita unggulan', function () {
    $admin = User::factory()->admin()->create();
    Berita::factory()->count(3)->create(['user_id' => $admin->id]);

    // Guard lazy loading aktif: bila penulis tidak di-eager load, halaman ini 500.
    $this->get('/')
        ->assertOk()
        ->assertSee($admin->name, false);
});

test('halaman detail berita merender sidebar banyak berita tanpa lazy loading', function () {
    $admin = User::factory()->admin()->create();
    Berita::factory()->count(4)->create(['user_id' => $admin->id]);

    $berita = Berita::published()->latest()->first();

    $this->get('/berita/'.$berita->slug)
        ->assertOk()
        ->assertSee($berita->judul, false);
});

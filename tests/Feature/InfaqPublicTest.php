<?php

use App\Models\Infaq;
use Illuminate\Support\Facades\Storage;

test('public infaq page renders the donation form and totals without listing donor names', function () {
    Infaq::create([
        'kode_transaksi' => 'INF-LUNAS01',
        'nama_donatur' => 'Bu Siti',
        'nominal' => 150000,
        'metode_pembayaran' => 'qris',
        'status' => 'lunas',
        'paid_at' => now(),
    ]);

    $this->get('/infaq')
        ->assertOk()
        ->assertSee('Infak & Sedekah')
        ->assertSee('150.000')
        ->assertSee('1 transaksi')
        ->assertSee('name="nominal"', false)
        ->assertSee(route('infaq.store'), false)
        ->assertSee('Tautan Navigasi')
        // Hanya donatur yang dihapus dari tampilan publik (nama tidak ditampilkan).
        ->assertDontSee('Bu Siti')
        ->assertDontSee('Donatur Terbaru');
});

test('public infaq page keeps an empty state when there is no donation yet', function () {
    $this->get('/infaq')
        ->assertOk()
        ->assertSee('Infak & Sedekah')
        ->assertSee('name="nominal"', false);
});

test('infaq form rejects a missing nominal', function () {
    $this->from('/infaq')
        ->post('/infaq', ['metode_pembayaran' => 'qris'])
        ->assertSessionHasErrors('nominal');

    expect(Infaq::count())->toBe(0);
});

test('infaq donation flow goes from form to checkout to success', function () {
    $this->post('/infaq', [
        'nominal' => 75000,
        'metode_pembayaran' => 'transfer_bank',
        'nama_donatur' => 'Hamba Allah',
    ])->assertSessionHasNoErrors();

    $infaq = Infaq::sole();

    expect($infaq->status)->toBe('pending');

    $this->get(route('infaq.checkout', $infaq->kode_transaksi))
        ->assertOk()
        ->assertSee('Instruksi Pembayaran')
        ->assertSee('75.000')
        ->assertSee($infaq->kode_transaksi);

    $this->post(route('infaq.simulate', $infaq->kode_transaksi))
        ->assertRedirect(route('infaq.success', $infaq->kode_transaksi));

    $this->get(route('infaq.success', $infaq->kode_transaksi))
        ->assertOk()
        ->assertSee('Alhamdulillah')
        ->assertSee($infaq->kode_transaksi)
        ->assertSee('75.000');
});

test('donatur uploads the payment proof to the supabase disk', function () {
    fakeSupabaseDisk();

    $this->post('/infaq', [
        'nominal' => 50000,
        'metode_pembayaran' => 'transfer_bank',
    ])->assertSessionHasNoErrors();

    $infaq = Infaq::sole();

    $this->post(route('infaq.proof', $infaq->kode_transaksi), [
        'bukti' => fakePngUpload('bukti-upload.png'),
    ])->assertSessionHasNoErrors();

    $infaq->refresh();

    expect($infaq->bukti_path)->not->toBeNull();

    Storage::disk('supabase')->assertExists($infaq->bukti_path);

    // Halaman checkout menampilkan bukti lewat URL Supabase.
    $this->get(route('infaq.checkout', $infaq->kode_transaksi))
        ->assertOk()
        ->assertSee(Storage::disk('supabase')->url($infaq->bukti_path), false);
});

<?php

use App\Models\Pengurus;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('create form shows the organization dropdown without raw code inputs', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('pengurus.create'))
        ->assertOk()
        ->assertSee('name="banom"', false)
        ->assertSee('PAC IPNU (Ikatan Pelajar NU)')
        ->assertDontSee('name="label_banom"', false);
});

test('create form shows a jabatan dropdown whose options follow the organization', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('pengurus.create'))
        ->assertOk()
        ->assertSee('id="jabatan"', false)
        ->assertSee('name="jabatan"', false) // input tersembunyi tetap mengirim jabatan
        ->assertSee('Ketua Bidang Dakwah', false) // opsi bidang generik
        ->assertSee('Kepala Bidang Pemberdayaan Umat', false) // opsi khas UPZIS LAZISNU
        ->assertSee('__default__', false);
});

test('edit form preselects the current jabatan as the dropdown value', function () {
    $pengurus = Pengurus::create([
        'nama' => 'Qomar',
        'jabatan' => 'Sekretaris',
        'banom' => 'pac_ipnu',
        'label_banom' => 'PAC IPNU',
        'urutan' => 1,
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('pengurus.edit', $pengurus))
        ->assertOk()
        ->assertSee('value="Sekretaris"', false);
});

test('store derives the display label from the dropdown value', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('pengurus.store'), [
            'nama' => 'Ahmad Syarif',
            'jabatan' => 'Ketua',
            'banom' => 'pac_ipnu',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('pengurus.index'));

    $pengurus = Pengurus::first();

    expect($pengurus->label_banom)->toBe('PAC IPNU')
        ->and($pengurus->urutan)->toBe(1);
});

test('store keeps the uploaded photo on the supabase disk', function () {
    fakeSupabaseDisk();

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('pengurus.store'), [
            'nama' => 'Ahmad Fotogenik',
            'jabatan' => 'Ketua',
            'banom' => 'pac_ipnu',
            'foto' => fakePngUpload('foto-pengurus.png'),
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('pengurus.index'));

    $pengurus = Pengurus::sole();

    expect($pengurus->foto)->not->toBeNull();

    Storage::disk('supabase')->assertExists($pengurus->foto);

    // Daftar admin menampilkan foto lewat URL Supabase, bukan asset('storage/...').
    $this->get(route('pengurus.index'))
        ->assertOk()
        ->assertSee(Storage::disk('supabase')->url($pengurus->foto), false);
});

test('urutan is auto assigned sequentially when the field is left blank', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('pengurus.store'), [
            'nama' => 'Ahmad Syarif',
            'jabatan' => 'Ketua',
            'banom' => 'mwcnu',
        ])
        ->assertSessionHasNoErrors();

    $this->post(route('pengurus.store'), [
        'nama' => 'Budi Santoso',
        'jabatan' => 'Sekretaris',
        'banom' => 'pac_ipnu',
    ])
        ->assertSessionHasNoErrors();

    expect(Pengurus::orderBy('id')->pluck('urutan')->all())->toBe([1, 2]);
});

test('store keeps a manually entered urutan', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('pengurus.store'), [
            'nama' => 'Ahmad Syarif',
            'jabatan' => 'Ketua',
            'banom' => 'mwcnu',
            'urutan' => '5',
        ])
        ->assertSessionHasNoErrors();

    expect(Pengurus::first()->urutan)->toBe(5);
});

test('store rejects a duplicate jabatan in the same organization', function () {
    Pengurus::create([
        'nama' => 'Ferdy',
        'jabatan' => 'Ketua',
        'banom' => 'mwcnu',
        'label_banom' => 'MWCNU',
        'urutan' => 1,
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('pengurus.store'), [
            'nama' => 'Peserta Baru',
            'jabatan' => '  ketua  ',
            'banom' => 'mwcnu',
        ])
        ->assertSessionHasErrors('jabatan');

    expect(Pengurus::count())->toBe(1)
        ->and(session('errors')->first('jabatan'))->toContain('sudah dipakai di MWCNU');
});

test('the same jabatan is allowed in a different organization', function () {
    Pengurus::create([
        'nama' => 'Ferdy',
        'jabatan' => 'Ketua',
        'banom' => 'mwcnu',
        'label_banom' => 'MWCNU',
        'urutan' => 1,
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('pengurus.store'), [
            'nama' => 'Firda',
            'jabatan' => 'Ketua',
            'banom' => 'pac_fatayat',
        ])
        ->assertSessionHasNoErrors();

    expect(Pengurus::count())->toBe(2);
});

test('store rejects an organization code that is not in the dropdown', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('pengurus.store'), [
            'nama' => 'Ahmad Syarif',
            'jabatan' => 'Ketua',
            'banom' => 'ranting',
        ])
        ->assertSessionHasErrors('banom');
});

test('edit form shows the dropdown with the current organization selected', function () {
    $pengurus = Pengurus::create([
        'nama' => 'Firda',
        'jabatan' => 'Ketua',
        'banom' => 'pac_fatayat',
        'label_banom' => 'PAC Fatayat NU',
        'urutan' => 1,
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('pengurus.edit', $pengurus))
        ->assertOk()
        ->assertSee('name="banom"', false)
        ->assertSee('value="pac_fatayat" data-label="PAC Fatayat NU" selected', false)
        ->assertDontSee('name="label_banom"', false);
});

test('update blocks taking another member jabatan in the same organization', function () {
    Pengurus::create([
        'nama' => 'Ferdy',
        'jabatan' => 'Ketua',
        'banom' => 'mwcnu',
        'label_banom' => 'MWCNU',
        'urutan' => 1,
    ]);
    $sekre = Pengurus::create([
        'nama' => 'Qomar',
        'jabatan' => 'Sekretaris',
        'banom' => 'mwcnu',
        'label_banom' => 'MWCNU',
        'urutan' => 2,
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('pengurus.update', $sekre), [
            'nama' => 'Qomar',
            'jabatan' => 'Ketua',
            'banom' => 'mwcnu',
            'urutan' => 2,
        ])
        ->assertSessionHasErrors('jabatan');

    // Menyimpan jabatannya sendiri tetap boleh (data sendiri diabaikan saat cek duplikat)
    $this->put(route('pengurus.update', $sekre), [
        'nama' => 'Qomar',
        'jabatan' => 'Sekretaris',
        'banom' => 'mwcnu',
        'urutan' => 2,
    ])
        ->assertSessionHasNoErrors();

    expect($sekre->fresh()->jabatan)->toBe('Sekretaris');
});

test('update refreshes the display label when the organization changes', function () {
    $pengurus = Pengurus::create([
        'nama' => 'Firda',
        'jabatan' => 'Ketua',
        'banom' => 'pac_fatayat',
        'label_banom' => 'PAC Fatayat NU',
        'urutan' => 1,
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('pengurus.update', $pengurus), [
            'nama' => 'Firda',
            'jabatan' => 'Ketua',
            'banom' => 'mwcnu',
            'urutan' => 1,
        ])
        ->assertSessionHasNoErrors();

    expect($pengurus->fresh()->label_banom)->toBe('MWCNU');
});

test('a failed submission shows errors and keeps the old input', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->followingRedirects()
        ->from(route('pengurus.create'))
        ->post(route('pengurus.store'), [
            'nama' => 'Budi Santoso',
            'jabatan' => '',
            'banom' => 'pac_ipnu',
        ])
        ->assertSee('Terjadi kesalahan pada inputan:')
        ->assertSee('Jabatan wajib diisi.')
        ->assertSee('value="Budi Santoso"', false)
        ->assertSee('value="pac_ipnu" data-label="PAC IPNU" selected', false);
});

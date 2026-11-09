<?php

use App\Models\Agenda;
use App\Models\User;

test('admin can open the agenda edit page', function () {
    $agenda = Agenda::create([
        'title' => 'Agenda Uji Edit',
        'category' => 'Kegiatan',
        'event_date' => now()->addDays(5)->toDateString(),
        'event_time' => '19:00 WIB',
        'location' => 'Aula Ranting',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('agenda.edit', $agenda))
        ->assertOk()
        ->assertSee('Agenda Uji Edit');
});

test('admin can update an agenda', function () {
    $agenda = Agenda::create([
        'title' => 'Agenda Sebelum Update',
        'category' => 'Kegiatan',
        'event_date' => now()->addDays(5)->toDateString(),
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->from(route('agenda.edit', $agenda))
        ->put(route('agenda.update', $agenda), [
            'title' => 'Agenda Sesudah Update',
            'category' => 'Pengajian',
            'event_date' => now()->addDays(7)->toDateString(),
            'event_time' => '20:00 WIB',
            'location' => 'Masjid Utama',
            'description' => 'Deskripsi baru',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('agenda.index'));

    expect($agenda->fresh()->title)->toBe('Agenda Sesudah Update');
});

test('admin can delete an agenda', function () {
    $agenda = Agenda::create([
        'title' => 'Agenda Untuk Dihapus',
        'category' => 'Kegiatan',
        'event_date' => now()->addDays(5)->toDateString(),
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('agenda.destroy', $agenda))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('agenda.index'));

    expect(Agenda::find($agenda->id))->toBeNull();
});

test('update does not create a duplicate agenda', function () {
    $agenda = Agenda::create([
        'title' => 'Judul Lama',
        'category' => 'Kegiatan',
        'event_date' => now()->addDays(5)->toDateString(),
    ]);

    $before = Agenda::count();

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('agenda.update', $agenda), [
            'title' => 'Judul Baru',
            'category' => 'Kegiatan',
            'event_date' => now()->addDays(6)->toDateString(),
        ])
        ->assertSessionHasNoErrors();

    expect(Agenda::count())->toBe($before)
        ->and($agenda->fresh()->title)->toBe('Judul Baru');
});

test('storing an agenda dated before today is rejected', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->from(route('agenda.create'))
        ->post(route('agenda.store'), [
            'title' => 'Agenda Selesai Kemarin',
            'category' => 'Kegiatan',
            'event_date' => now()->subDay()->toDateString(),
        ])->assertSessionHasErrors('event_date');

    expect(Agenda::where('title', 'Agenda Selesai Kemarin')->exists())->toBeFalse();
});

test('storing an agenda dated today is not past', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('agenda.store'), [
            'title' => 'Agenda Hari Ini',
            'category' => 'Kegiatan',
            'event_date' => now()->toDateString(),
        ])->assertSessionHasNoErrors()
        ->assertSessionHas('success')
        ->assertSessionMissing('warning');
});

test('storing a future agenda is accepted', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('agenda.store'), [
            'title' => 'Agenda Mendatang',
            'category' => 'Kegiatan',
            'event_date' => now()->addDays(5)->toDateString(),
        ])->assertSessionHasNoErrors()
        ->assertSessionHas('success');
});

test('editing an agenda to a past date is rejected', function () {
    $agenda = Agenda::create([
        'title' => 'Agenda Bergeser',
        'category' => 'Kegiatan',
        'event_date' => now()->addDays(5)->toDateString(),
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('agenda.update', $agenda), [
            'title' => 'Agenda Bergeser',
            'category' => 'Kegiatan',
            'event_date' => now()->subDays(3)->toDateString(),
        ])->assertSessionHasErrors('event_date');

    expect($agenda->fresh()->event_date->toDateString())->toBe(now()->addDays(5)->toDateString());
});

test('the agenda form enforces a minimum date of today', function () {
    $today = now()->toDateString();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('agenda.create'))
        ->assertOk()
        ->assertSee('id="past-date-hint"', false)
        ->assertSee('min="'.$today.'"', false)
        ->assertSee('tidak boleh sebelum hari ini', false);
});

test('agenda isPast is true only before today', function () {
    expect((new Agenda(['event_date' => now()->subDay()->toDateString()]))->isPast())->toBeTrue()
        ->and((new Agenda(['event_date' => now()->toDateString()]))->isPast())->toBeFalse()
        ->and((new Agenda(['event_date' => now()->addDay()->toDateString()]))->isPast())->toBeFalse();
});

<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Halaman publik akan menampilkan agenda ini sebagai agenda yang
     * sudah lewat, karena tanggalnya sebelum hari ini.
     */
    private function pastWarning(Agenda $agenda): ?string
    {
        return $agenda->isPast()
            ? 'Catatan: tanggal acara sebelum hari ini, jadi agenda ini akan tampil sebagai agenda yang sudah lewat di halaman publik.'
            : null;
    }

    public function publicIndex()
    {
        $upcomingAgenda = Agenda::where('event_date', '>=', today())
            ->orderBy('event_date')
            ->paginate(9)
            ->withQueryString();

        $pastAgenda = Agenda::where('event_date', '<', today())
            ->orderByDesc('event_date')
            ->take(6)
            ->get();

        return view('agenda', compact('upcomingAgenda', 'pastAgenda'));
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $agendaList = Agenda::query()
            ->when($q !== '', function (Builder $query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function (Builder $query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('category', 'like', $like)
                        ->orWhere('location', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->orderBy('event_date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.agenda.index', compact('agendaList', 'q'));
    }

    public function create()
    {
        return view('admin.agenda.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'category' => 'required|max:100',
            'event_date' => ['required', 'date', 'after_or_equal:'.today()->toDateString()],
            'event_time' => 'nullable|max:50',
            'location' => 'nullable|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'maps_url' => 'nullable|url|max:500',
            'description' => 'nullable',
        ], [
            'event_date.after_or_equal' => 'Tanggal agenda tidak boleh sebelum hari ini.',
        ]);

        $agenda = Agenda::create($validated);

        return redirect()
            ->route('agenda.index')
            ->with('success', 'Agenda berhasil ditambahkan.');
    }

    public function edit(Agenda $agenda)
    {
        return view('admin.agenda.edit', compact('agenda'));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'category' => 'required|max:100',
            'event_date' => ['required', 'date', 'after_or_equal:'.today()->toDateString()],
            'event_time' => 'nullable|max:50',
            'location' => 'nullable|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'maps_url' => 'nullable|url|max:500',
            'description' => 'nullable',
        ], [
            'event_date.after_or_equal' => 'Tanggal agenda tidak boleh sebelum hari ini.',
        ]);

        $agenda->update($validated);

        return redirect()
            ->route('agenda.index')
            ->with('success', 'Agenda berhasil diperbarui.');
    }

    public function destroy(Agenda $agenda)
    {
        $agenda->delete();

        return redirect()
            ->route('agenda.index')
            ->with('success', 'Agenda berhasil dihapus.');
    }
}

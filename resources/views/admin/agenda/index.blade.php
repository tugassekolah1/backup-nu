<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start sm:items-center gap-3 min-w-0">
                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Manajemen Agenda Kegiatan
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola semua agenda dan kegiatan terbaru di sini.</p>
                </div>
            </div>

            <a href="{{ route('agenda.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-xs hover:shadow-emerald-200 hover:shadow-md w-full sm:w-auto">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Agenda</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Alert Sukses --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Alert Peringatan: tanggal sebelum hari ini --}}
            @if (session('warning'))
                <div class="p-4 bg-amber-50 border border-amber-300 text-amber-800 rounded-2xl flex items-start gap-3 shadow-xs" role="alert">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('warning') }}</span>
                </div>
            @endif

            {{-- Main Table Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-800">Daftar Seluruh Agenda</h3>
                        <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">
                            Total: {{ $agendaList->total() }} Data
                        </span>
                    </div>

                    <form method="GET" action="{{ route('agenda.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <div class="flex-1 relative min-w-0">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul, kategori, lokasi, atau deskripsi..." 
                                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 sm:flex-none px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                                Cari
                            </button>
                            @if ($q !== '')
                                <a href="{{ route('agenda.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-300 transition-colors">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-xs uppercase font-bold text-slate-500 tracking-wider">
                                <th class="px-4 sm:px-6 py-4">Tanggal</th>
                                <th class="px-4 sm:px-6 py-4">Agenda</th>
                                <th class="px-4 sm:px-6 py-4">Kategori</th>
                                <th class="px-4 sm:px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($agendaList as $agenda)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    {{-- Tanggal --}}
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-900">
                                            {{ $agenda->event_date->translatedFormat('d M Y') }}
                                        </div>
                                        @if ($agenda->event_time)
                                            <div class="text-xs text-slate-400 mt-0.5">{{ $agenda->event_time }}</div>
                                        @endif
                                    </td>

                                    {{-- Agenda (judul + lokasi di bawah) --}}
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="font-bold text-slate-900 max-w-[16rem] sm:max-w-xs truncate">{{ $agenda->title }}</div>
                                        <div class="mt-1 flex items-start gap-1.5 text-xs text-slate-500">
                                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span class="break-words leading-snug">{{ $agenda->location ?? '-' }}</span>
                                            @if ($agenda->maps_link)
                                                <a href="{{ $agenda->maps_link }}" target="_blank" rel="noopener noreferrer"
                                                   title="Buka di Google Maps"
                                                   class="shrink-0 font-semibold text-emerald-700 hover:underline">
                                                    Maps
                                                </a>
                                            @endif
                                        </div>
                                        @if ($agenda->latitude && $agenda->longitude)
                                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $agenda->latitude }}, {{ $agenda->longitude }}</div>
                                        @endif
                                    </td>

                                    {{-- Kategori --}}
                                    <td class="px-4 sm:px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100/80 text-emerald-800">
                                            {{ $agenda->category }}
                                        </span>
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="px-4 sm:px-6 py-4 text-center">
                                        <div class="inline-flex items-center gap-1.5 bg-slate-100/80 p-1.5 rounded-xl border border-slate-200/60">
                                            <a href="{{ route('agenda.edit', $agenda) }}"
                                               title="Edit Data"
                                               class="p-1.5 rounded-lg text-slate-600 hover:bg-white hover:text-blue-600 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            <form action="{{ route('agenda.destroy', $agenda) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus agenda ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="Hapus Data"
                                                        class="p-1.5 rounded-lg text-slate-600 hover:bg-white hover:text-rose-600 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-12">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <div class="p-4 bg-slate-100 text-slate-400 rounded-full">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <p class="text-slate-500 font-medium">Belum ada agenda yang ditambahkan.</p>
                                            <a href="{{ route('agenda.create') }}" class="text-xs font-bold text-blue-600 hover:underline">
                                                + Klik di sini untuk menambah agenda pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($agendaList->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $agendaList->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
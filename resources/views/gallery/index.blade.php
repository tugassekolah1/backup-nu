<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Manajemen Galeri
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola semua foto dokumentasi kegiatan di sini.</p>
                </div>
            </div>

            <a href="{{ route('gallery.create') }}"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-xs hover:shadow-emerald-200 hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Foto</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-800">Daftar Seluruh Foto</h3>
                        <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">
                            Total: {{ $galleries->count() }} Data
                        </span>
                    </div>

                    <form method="GET" action="{{ route('gallery.index') }}" class="flex flex-wrap items-center gap-2">
                        <div class="flex-1 min-w-[14rem] relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul atau deskripsi foto..." 
                                   class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>

                        <select name="organisasi"
                                class="px-3 py-2 text-sm border border-slate-200 rounded-lg bg-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="">Semua organisasi</option>
                            @foreach ($daftarOrganisasi as $opsi)
                                <option value="{{ $opsi['slug'] }}" @selected($organisasiAktif === $opsi['slug'])>
                                    {{ $opsi['singkatan'] }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors">
                            Cari
                        </button>
                        @if ($q !== '' || $organisasiAktif !== '')
                            <a href="{{ route('gallery.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300 transition-colors">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-xs uppercase font-bold text-slate-500 tracking-wider">
                                <th class="px-6 py-4">Foto</th>
                                <th class="px-6 py-4">Judul</th>
                                <th class="px-6 py-4">Organisasi</th>
                                <th class="px-6 py-4">Deskripsi</th>
                                <th class="px-6 py-4">Urutan</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($galleries as $item)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4">
                                        <img src="{{ $item->url }}" alt="{{ $item->judul }}" class="w-16 h-12 object-cover rounded-lg shadow-sm border border-slate-200">
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-900 max-w-xs truncate">{{ $item->judul }}</td>
                                    <td class="px-6 py-4">
                                        @if ($item->organisasiLabel() !== '')
                                            <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                                                {{ $item->organisasiLabel() }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium max-w-xs truncate">{{ $item->deskripsi ?? '-' }}</td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">{{ $item->urutan }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center gap-1.5 bg-slate-100/80 p-1.5 rounded-xl border border-slate-200/60">
                                            <a href="{{ route('gallery.edit', $item->id) }}"
                                               title="Edit Data"
                                               class="p-1.5 rounded-lg text-slate-600 hover:bg-white hover:text-blue-600 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('gallery.destroy', $item->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto ini?')">
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
                                    <td colspan="6" class="text-center py-12">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <div class="p-4 bg-slate-100 text-slate-400 rounded-full">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <p class="text-slate-500 font-medium">Belum ada foto galeri.</p>
                                            <a href="{{ route('gallery.create') }}" class="text-xs font-bold text-blue-600 hover:underline">
                                                + Klik di sini untuk menambah foto pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>


<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Manajemen Anggota
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola data pendaftaran dan akun anggota.</p>
                </div>
            </div>

            <a href="{{ route('members.create') }}"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-xs hover:shadow-emerald-200 hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Anggota Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Alert Sukses --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Alert Informasi --}}
            @if (session('info'))
                <div class="p-4 bg-sky-50 border border-sky-200 text-sky-800 rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('info') }}</span>
                </div>
            @endif

            {{-- Alert Gagal --}}
            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Error validasi (mis. alasan penolakan kosong) --}}
            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-xs">
                    <strong class="text-sm">Periksa kembali isian berikut:</strong>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Ringkasan per Organisasi: satu baris chip, bukan banyak kartu kotak --}}
            @php
                $filterAktif = $banomFilter ?? 'all';
                $totalMembers = array_sum($banomCounts);

                $chipOrganisasi = [
                    [
                        'kode' => 'all',
                        'label' => 'Semua',
                        'jumlah' => $totalMembers,
                        'href' => route('members.index'),
                        'cetak' => route('members.cetak-kta-bulk'),
                        'cetakLabel' => 'Seluruh anggota',
                        'aksen' => 'emerald',
                    ],
                    [
                        'kode' => 'none',
                        'label' => 'Tanpa Organisasi',
                        'jumlah' => $banomCounts['none'] ?? 0,
                        'href' => route('members.index', ['banom' => 'none']),
                        'cetak' => route('members.cetak-kta-bulk', ['banom' => 'none']),
                        'cetakLabel' => 'Tanpa organisasi',
                        'aksen' => 'amber',
                    ],
                ];

                foreach (\App\Models\NuMember::ORGANISASI_GROUPS as $items) {
                    foreach ($items as $code => $desc) {
                        $chipOrganisasi[] = [
                            'kode' => $code,
                            'label' => \App\Models\Pengurus::BANOMS[$code]['label'] ?? $code,
                            'jumlah' => $banomCounts[$code] ?? 0,
                            'href' => route('members.index', ['banom' => $code]),
                            'cetak' => route('members.cetak-kta-bulk', ['banom' => $code]),
                            'cetakLabel' => \App\Models\Pengurus::BANOMS[$code]['label'] ?? $code,
                            'aksen' => 'emerald',
                        ];
                    }
                }

                // Status aktif dihitung di sini supaya tidak perlu @php() inline
                // di dalam loop (direktif inline begitu tidak ikut ter-compile).
                foreach ($chipOrganisasi as $index => $chip) {
                    $chipOrganisasi[$index]['aktif'] = $filterAktif === $chip['kode'];
                }
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-3">
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach ($chipOrganisasi as $chip)
                        <div @class([
                            'group inline-flex items-stretch rounded-full border transition-colors',
                            'bg-emerald-600 border-emerald-600 shadow-xs' => $chip['aktif'] && $chip['aksen'] === 'emerald',
                            'bg-amber-500 border-amber-500 shadow-xs' => $chip['aktif'] && $chip['aksen'] === 'amber',
                            'bg-white border-slate-200 hover:border-emerald-300' => ! $chip['aktif'],
                        ])>
                            <a href="{{ $chip['href'] }}"
                               @class([
                                   'inline-flex items-center gap-2 rounded-l-full py-1.5 pl-3.5 pr-2.5',
                               ])
                               {{-- Nilai literal, bukan input pengguna: aman memakai {!! !!} agar kutip tidak ikut di-escape. --}}
                               {!! $chip['aktif'] ? 'aria-current="true"' : '' !!}
                               title="{{ $chip['aktif'] ? 'Menampilkan: ' : 'Saring menurut ' }}{{ $chip['label'] }}">
                                <span @class([
                                    'text-[11px] font-bold uppercase tracking-wide',
                                    'text-white' => $chip['aktif'],
                                    'text-slate-600 group-hover:text-emerald-700' => ! $chip['aktif'],
                                ])>{{ $chip['label'] }}</span>

                                <span @class([
                                    'min-w-[1.75rem] rounded-full px-1.5 py-0.5 text-center text-xs font-bold tabular-nums',
                                    'bg-white/20 text-white' => $chip['aktif'],
                                    'bg-slate-100 text-slate-500' => ! $chip['aktif'],
                                ])>{{ $chip['jumlah'] }}</span>
                            </a>

                            <span @class([
                                'w-px',
                                'bg-white/25' => $chip['aktif'],
                                'bg-slate-200' => ! $chip['aktif'],
                            ]) aria-hidden="true"></span>

                            <a href="{{ $chip['cetak'] }}"
                               target="_blank" rel="noopener"
                               title="Cetak KTA berjamaah {{ $chip['cetakLabel'] }}"
                               @class([
                                   'inline-flex items-center rounded-r-full px-2.5',
                                   'text-white/70 hover:text-white' => $chip['aktif'],
                                   'text-slate-300 hover:text-emerald-600' => ! $chip['aktif'],
                               ])>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                <span class="sr-only">Cetak KTA {{ $chip['cetakLabel'] }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Search Box --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
                <form action="{{ route('members.index') }}" method="GET" class="flex items-center gap-2">
                    @if (($statusFilter ?? 'all') !== 'all')
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                    @endif
                    @if (($banomFilter ?? 'all') !== 'all')
                        <input type="hidden" name="banom" value="{{ $banomFilter }}">
                    @endif
                    <div class="flex-1 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" placeholder="Cari berdasarkan Nama atau NIK..." 
                               value="{{ $search ?? '' }}"
                               class="flex-1 text-sm text-slate-700 border-0 bg-transparent placeholder-slate-400 focus:outline-none focus:ring-0">
                    </div>
                    @if (($search ?? '') !== '')
                        <a href="{{ route('members.index', array_filter(['status' => ($statusFilter ?? 'all') === 'all' ? null : $statusFilter, 'banom' => ($banomFilter ?? 'all') === 'all' ? null : $banomFilter])) }}"
                           class="text-xs font-bold text-slate-400 hover:text-rose-600 transition-colors px-2 py-1">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Filter Status & Banom --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
                <div class="flex flex-wrap items-center gap-2">
                    @php
                        $statusTabs = ['all' => 'Semua', 'pending' => 'Menunggu Disetujui', 'approved' => 'Sudah Disetujui'];
                        $banomQuery = array_filter(['banom' => ($banomFilter ?? 'all') === 'all' ? null : ($banomFilter ?? null)]);
                    @endphp
                    @foreach ($statusTabs as $value => $label)
                        <a href="{{ route('members.index', array_merge($banomQuery, $value === 'all' ? [] : ['status' => $value])) }}"
                           @class([
                               'px-3.5 py-2 rounded-xl text-xs font-bold transition-all active:scale-95',
                               'bg-emerald-600 text-white shadow-xs' => ($statusFilter ?? 'all') === $value,
                               'bg-white text-slate-600 border border-slate-200 hover:border-emerald-300 hover:text-emerald-700' => ($statusFilter ?? 'all') !== $value,
                           ])>
                            {{ $label }}
                        </a>
                    @endforeach

                    <form action="{{ route('members.index') }}" method="GET" class="flex items-center gap-2 ml-auto">
                        @if (($statusFilter ?? 'all') !== 'all')
                            <input type="hidden" name="status" value="{{ $statusFilter }}">
                        @endif
                        <select name="banom" onchange="this.form.submit()"
                                class="text-xs font-semibold text-slate-700 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 cursor-pointer">
                            <option value="all">Semua Banom</option>
                            @foreach (\App\Models\NuMember::ORGANISASI_GROUPS as $group => $items)
                                <optgroup label="{{ $group }}">
                                    @foreach ($items as $code => $desc)
                                        <option value="{{ $code }}" @selected(($banomFilter ?? 'all') === $code)>
                                            {{ \App\Models\Pengurus::BANOMS[$code]['label'] ?? $code }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                            <option value="none" @selected(($banomFilter ?? 'all') === 'none')>Tanpa organisasi</option>
                        </select>
                        @if (($banomFilter ?? 'all') !== 'all')
                            <a href="{{ route('members.cetak-kta-bulk', ['banom' => $banomFilter]) }}"
                               target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak KTA Banom Ini
                            </a>
                        @endif
                        @if (($search ?? '') !== '' || ($statusFilter ?? 'all') !== 'all' || ($banomFilter ?? 'all') !== 'all')
                            <a href="{{ route('members.index') }}"
                               class="text-xs font-bold text-slate-400 hover:text-rose-600 transition-colors px-1">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Daftar anggota: baris kartu responsif, tanpa scroll horizontal --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Daftar Seluruh Anggota</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Data ditampilkan per baris agar mudah dibaca, baik di HP maupun komputer.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <label for="select-all" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)"
                                   class="w-4 h-4 text-emerald-600 border-slate-300 rounded cursor-pointer">
                            Pilih semua
                        </label>
                        <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">
                            Total: {{ $members->total() ?? count($members) }} Data
                        </span>
                    </div>
                </div>

                <form id="bulk-action-form" method="POST" action="">
                    @csrf
                    {{-- Bulk Action Bar (Hidden by default) --}}
                    <div id="bulk-action-bar" class="hidden sticky bottom-0 left-0 right-0 bg-emerald-50 border-t border-emerald-200 px-5 py-3 flex items-center justify-between shadow-lg z-40">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-emerald-900">
                                <span id="selected-count">0</span> dipilih
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="clearBulkSelection()"
                                    class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                                Batal
                            </button>
                            <button type="button" onclick="submitBulkPrint()"
                                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-4 py-1.5 rounded-lg text-xs font-semibold transition-all shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak KTA Terpilih
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Tabel anggota: kolom ringkas, tidak perlu digeser ke samping --}}
                <div class="w-full overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/80 border-b border-slate-200">
                            <tr class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                                <th class="w-10 px-4 py-3"></th>
                                <th class="px-3 py-3">Anggota</th>
                                <th class="hidden md:table-cell px-3 py-3">Organisasi</th>
                                <th class="hidden xl:table-cell px-3 py-3">No. Kartu</th>
                                <th class="hidden sm:table-cell px-3 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($members as $member)
                                @php
                                    $state = $member->registrationState();
                                    $statusPill = match ($state) {
                                        'accepted' => ['label' => 'Aktif', 'class' => 'bg-emerald-100/80 text-emerald-800', 'dot' => 'bg-emerald-500'],
                                        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-rose-100/80 text-rose-800', 'dot' => 'bg-rose-500'],
                                        default => ['label' => 'Menunggu', 'class' => 'bg-amber-100/80 text-amber-800', 'dot' => 'bg-amber-500'],
                                    };
                                @endphp

                                <tr class="hover:bg-slate-50/70 transition-colors align-middle">
                                    {{-- Pilih (hanya anggota aktif yang bisa dicetak) --}}
                                    <td class="px-4 py-3">
                                        @if ($member->status === 'active')
                                            <input type="checkbox" name="ids[]" value="{{ $member->id }}"
                                                   onchange="updateBulkActionBar()"
                                                   class="member-checkbox w-4 h-4 text-emerald-600 border-slate-300 rounded cursor-pointer">
                                        @endif
                                    </td>

                                    {{-- Identitas --}}
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            @if ($member->photo)
                                                <img src="{{ asset('storage/' . $member->photo) }}"
                                                     alt="Foto {{ $member->full_name }}"
                                                     class="w-10 h-10 shrink-0 rounded-full object-cover border border-slate-200 bg-slate-50">
                                            @else
                                                <span class="w-10 h-10 shrink-0 rounded-full bg-emerald-50 text-emerald-700 inline-flex items-center justify-center font-bold text-sm">
                                                    {{ strtoupper(substr($member->full_name, 0, 1)) }}
                                                </span>
                                            @endif

                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 truncate max-w-[15rem]">
                                                    {{ $member->full_name }}
                                                </div>
                                                <div class="text-[11px] font-mono text-slate-500 mt-0.5">
                                                    NIK: {{ $member->nik }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 mt-0.5">
                                                    HP: {{ $member->phone }}
                                                </div>

                                                {{-- Info tambahan saat kolom lain disembunyikan di layar kecil --}}
                                                <div class="flex flex-wrap items-center gap-1 mt-1 md:hidden">
                                                    @if ($member->organisasi_label)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100/80 text-emerald-800">
                                                            {{ $member->organisasi_label }}
                                                        </span>
                                                    @endif
                                                    @if ($member->member_card_no)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-mono text-[10px] font-semibold">
                                                            {{ $member->member_card_no }}
                                                        </span>
                                                    @endif
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusPill['class'] }}">
                                                        <span class="w-1 h-1 rounded-full {{ $statusPill['dot'] }}"></span>
                                                        {{ $statusPill['label'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Organisasi --}}
                                    <td class="hidden md:table-cell px-3 py-3">
                                        @if ($member->organisasi_label)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100/80 text-emerald-800 truncate max-w-[9.5rem]">
                                                {{ $member->organisasi_label }}
                                            </span>
                                        @else
                                            <span class="text-[11px] text-slate-400 italic">Tanpa organisasi</span>
                                        @endif
                                    </td>

                                    {{-- Nomor kartu --}}
                                    <td class="hidden xl:table-cell px-3 py-3">
                                        @if ($member->member_card_no)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-slate-100 text-slate-700 font-mono text-[11px] font-semibold">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $member->member_card_no }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-300">—</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="hidden sm:table-cell px-3 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $statusPill['class'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $statusPill['dot'] }}"></span>
                                            {{ $statusPill['label'] }}
                                        </span>
                                        @if ($state === 'rejected' && $member->rejection_reason)
                                            <div class="text-[10px] text-rose-500 mt-1 max-w-[9rem] truncate"
                                                 title="{{ $member->rejection_reason }}">
                                                {{ $member->rejection_reason }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap items-center justify-end gap-1.5">
                                            @if ($state === 'pending')
                                                <form action="{{ route('admin.members.requests.accept', $member) }}"
                                                      method="POST" class="inline"
                                                      onsubmit="return confirm('Terima pendaftaran {{ $member->full_name }} sebagai anggota aktif?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs transition-all active:scale-95">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        Terima
                                                    </button>
                                                </form>

                                                <button type="button"
                                                        data-reject-url="{{ route('admin.members.requests.reject', $member) }}"
                                                        data-reject-name="{{ $member->full_name }}"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] font-bold border border-rose-200 text-rose-600 hover:bg-rose-50 transition-all active:scale-95">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Tolak
                                                </button>
                                            @endif

                                            <div class="inline-flex items-center gap-0.5 bg-slate-100/80 p-1 rounded-lg border border-slate-200/60">
                                                @if ($member->status === 'active')
                                                    <a href="{{ route('members.print-card', $member) }}"
                                                       target="_blank" rel="noopener"
                                                       title="Cetak Kartu Anggota"
                                                       class="p-1.5 rounded-md text-slate-600 hover:bg-white hover:text-emerald-600 transition-all">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                        </svg>
                                                    </a>

                                                    <a href="{{ route('members.download-kta', $member) }}"
                                                       target="_blank" rel="noopener"
                                                       title="Download KTA"
                                                       class="p-1.5 rounded-md text-slate-600 hover:bg-white hover:text-blue-600 transition-all">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                @endif

                                                <a href="{{ route('members.edit', $member) }}"
                                                   title="Edit Data"
                                                   class="p-1.5 rounded-md text-slate-600 hover:bg-white hover:text-emerald-600 transition-all">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>

                                                <form action="{{ route('members.destroy', $member) }}"
                                                      method="POST" class="inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data anggota ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            title="Hapus Data"
                                                            class="p-1.5 rounded-md text-slate-600 hover:bg-white hover:text-rose-600 transition-all">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-16 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <div class="p-4 bg-slate-100 text-slate-400 rounded-full">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                                </svg>
                                            </div>
                                            <p class="text-slate-500 font-medium">Belum ada data anggota yang terdaftar.</p>
                                            <a href="{{ route('members.create') }}" class="text-xs font-bold text-emerald-600 hover:underline">
                                                + Klik di sini untuk menambah anggota pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                    @if($members->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                            {{ $members->links() }}
                        </div>
                    @endif

                <script>
                    const STORAGE_KEY = 'member_selections_{{ $statusFilter }}_{{ $banomFilter }}';

                    // Load saved selections saat page load
                    document.addEventListener('DOMContentLoaded', function() {
                        restoreSelections();
                        updateBulkActionBar();
                    });

                    function getStoredSelections() {
                        const stored = localStorage.getItem(STORAGE_KEY);
                        return stored ? JSON.parse(stored) : [];
                    }

                    function saveSelections() {
                        const checked = Array.from(document.querySelectorAll('.member-checkbox:checked'))
                            .map(cb => parseInt(cb.value));
                        localStorage.setItem(STORAGE_KEY, JSON.stringify(checked));
                    }

                    function restoreSelections() {
                        const stored = getStoredSelections();
                        document.querySelectorAll('.member-checkbox').forEach(cb => {
                            cb.checked = stored.includes(parseInt(cb.value));
                        });
                    }

                    function toggleSelectAll(checkbox) {
                        const memberCheckboxes = document.querySelectorAll('.member-checkbox');
                        memberCheckboxes.forEach(cb => cb.checked = checkbox.checked);
                        saveSelections();
                        updateBulkActionBar();
                    }

                    function updateBulkActionBar() {
                        const stored = getStoredSelections();
                        const bulkBar = document.getElementById('bulk-action-bar');
                        const selectedCount = document.getElementById('selected-count');
                        
                        selectedCount.textContent = stored.length;
                        
                        if (stored.length > 0) {
                            bulkBar.classList.remove('hidden');
                        } else {
                            bulkBar.classList.add('hidden');
                        }

                        // Update checkbox state halaman ini
                        const memberCheckboxes = document.querySelectorAll('.member-checkbox');
                        const currentPageChecked = Array.from(memberCheckboxes).filter(cb => cb.checked).length;
                        const totalOnPage = memberCheckboxes.length;
                        document.getElementById('select-all').checked = currentPageChecked > 0 && currentPageChecked === totalOnPage;
                    }

                    function clearBulkSelection() {
                        document.getElementById('select-all').checked = false;
                        document.querySelectorAll('.member-checkbox').forEach(cb => cb.checked = false);
                        localStorage.removeItem(STORAGE_KEY);
                        updateBulkActionBar();
                    }

                    function submitBulkPrint() {
                        const stored = getStoredSelections();
                        if (stored.length === 0) {
                            alert('Pilih minimal 1 anggota untuk dicetak.');
                            return;
                        }

                        const form = document.getElementById('bulk-action-form');
                        // Clear existing ids
                        form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                        // Add stored selections
                        stored.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });
                        form.action = '{{ route("members.cetak-kta-bulk") }}';
                        form.method = 'POST';
                        form.submit();
                    }

                    // Save selections saat checkbox berubah
                    document.addEventListener('change', function(e) {
                        if (e.target.classList.contains('member-checkbox')) {
                            saveSelections();
                            updateBulkActionBar();
                        }
                    });
                </script>
            </div>

        </div>
    </div>
    {{-- Modal: Tolak pendaftaran (wajib alasan) --}}
    <div id="reject-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-xl">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 p-3 bg-rose-50 text-rose-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="pt-1 w-full">
                        <h3 class="text-lg font-bold text-slate-800">Tolak Pendaftaran?</h3>
                        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                            Permintaan dari <span id="reject-member-name" class="font-bold text-slate-700"></span>
                            akan <strong class="text-rose-600">ditolak</strong>. Tuliskan alasannya agar pendaftar memahami.
                        </p>

                        <form id="reject-form" method="POST" action="">
                            @csrf
                            @method('PATCH')

                            <label for="reject-reason" class="block text-sm font-bold text-slate-700 mt-4 mb-1.5">
                                Alasan Penolakan <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="reject-reason" name="reason" rows="3" maxlength="500" required
                                      placeholder="Contoh: Data NIK tidak sesuai dengan dokumen identitas."
                                      class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:border-rose-400 focus:ring-2 focus:ring-rose-100 transition-all"></textarea>
                            <div class="flex items-center justify-end mt-1.5">
                                <span class="text-xs text-slate-400"><span id="reject-count">0</span>/500</span>
                            </div>

                            <div class="mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                                <button type="button" id="reject-cancel"
                                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-600 hover:bg-slate-50 transition-all active:scale-95">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold transition-all shadow-xs active:scale-95">
                                    Ya, Tolak Pendaftaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById('reject-modal');
            var form = document.getElementById('reject-form');
            var nameEl = document.getElementById('reject-member-name');
            var reasonEl = document.getElementById('reject-reason');
            var countEl = document.getElementById('reject-count');

            if (!modal || !form || !reasonEl) return;

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }

            document.querySelectorAll('[data-reject-url]').forEach(function (button) {
                button.addEventListener('click', function () {
                    form.action = button.dataset.rejectUrl;
                    nameEl.textContent = button.dataset.rejectName || 'anggota ini';
                    reasonEl.value = '';
                    countEl.textContent = '0';
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                    window.setTimeout(function () { reasonEl.focus(); }, 120);
                });
            });

            document.getElementById('reject-cancel').addEventListener('click', closeModal);

            modal.addEventListener('click', function (event) {
                if (event.target === modal) closeModal();
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') closeModal();
            });

            reasonEl.addEventListener('input', function () {
                countEl.textContent = String(reasonEl.value.length);
            });
        });
    </script>

</x-app-layout>
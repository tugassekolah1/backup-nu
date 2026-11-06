{{--
    Kartu profil satu pengurus organisasi (MWC/Banom — bukan anggota).
    Seluruh kartu adalah tautan ke halaman detail profil.
    Butuh: $p (Pengurus), $besar (bool, default false) untuk kartu Ketua.
--}}
@php
    $inisial = collect(explode(' ', trim($p->nama ?? '')))
        ->filter()
        ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
        ->take(2)
        ->join('');
    $besar = $besar ?? false;
@endphp
<a href="{{ route('struktur.show', $p) }}" aria-label="Lihat profil {{ $p->nama }} — {{ $p->jabatan }}"
   class="{{ $besar ? 'w-full max-w-sm p-8 ring-2 ring-amber-300' : 'p-6' }} group/card bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 hover:border-emerald-300 transition-all duration-200 flex flex-col items-center text-center">
    @if ($p->foto)
        <img src="{{ $p->foto_url }}" alt="Foto {{ $p->nama }}" loading="lazy"
             class="{{ $besar ? 'w-28 h-28' : 'w-20 h-20' }} rounded-full object-cover border-2 {{ $besar ? 'border-amber-300' : 'border-emerald-100' }} bg-slate-50">
    @else
        <span class="{{ $besar ? 'w-28 h-28 text-3xl' : 'w-20 h-20 text-2xl' }} rounded-full bg-emerald-50 text-emerald-700 inline-flex items-center justify-center font-extrabold shrink-0">
            {{ $inisial ?: 'NU' }}
        </span>
    @endif

    <p class="{{ $besar ? 'text-xl' : 'text-base' }} mt-3 font-bold text-slate-900 leading-snug group-hover/card:text-emerald-800">{{ $p->nama }}</p>
    <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $besar ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100/80 text-emerald-800' }}">
        {{ $p->jabatan }}
    </span>
    @if ($p->label_banom)
        <span class="mt-1.5 text-[11px] font-medium text-slate-400">{{ $p->label_banom }}</span>
    @endif
    <span class="mt-3 inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 opacity-0 group-hover/card:opacity-100 transition-opacity">
        Lihat Profil
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
    </span>
</a>

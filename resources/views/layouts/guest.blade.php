<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NU Banjaranyar') }}</title>

    <!-- Fonts: Inter (teks), Baloo 2 (judul), Amiri (kaligrafi), Inter (via body inline style) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Baloo+2:wght@700;800&family=Inter:wght@400;500;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased text-[#17392D] bg-[#0E2A1E]" style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;">
    <div class="min-h-screen grid lg:grid-cols-12">

        {{-- Panel merek NU (desktop) --}}
        <aside class="relative hidden lg:flex lg:col-span-5 flex-col justify-between overflow-hidden p-10 xl:p-14 text-white"
               style="background: linear-gradient(160deg, #1a6a3f 0%, #123f28 48%, #081f16 100%);">
            <!-- Pola titik halus -->
            <div class="absolute inset-0 opacity-[0.14] pointer-events-none"
                 style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.95) 1px, transparent 0); background-size: 24px 24px;"></div>
            <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-[#8bc14b]/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-[#B49352]/10 blur-3xl pointer-events-none"></div>

            <div class="relative">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#EDE8DD] backdrop-blur-sm">
                    <span class="material-symbols-outlined text-[16px]">id_card</span>
                    KARTANU Digital
                </span>
            </div>

            <div class="relative flex flex-col items-start">
                <img src="{{ asset('images/logo.webp') }}" alt="Logo NU Banjaranyar"
                     class="w-36 xl:w-44 h-auto object-contain drop-shadow-2xl">
                <h1 class="mt-7 text-4xl xl:text-5xl font-extrabold leading-[1.05] tracking-tight" style="font-family: 'Baloo 2', system-ui, sans-serif;">
                    NU Ranting<br>Banjaranyar
                </h1>
                <p class="mt-3 max-w-xs text-sm leading-relaxed text-white/70">
                    Nahdlatul Ulama Banjaranyar — Kecamatan Cilongok, Kabupaten Banyumas.
                </p>
                <div class="mt-8 h-px w-24 bg-gradient-to-r from-[#B49352] to-transparent"></div>
                <p class="mt-6 text-2xl xl:text-3xl text-[#EDE8DD]/90" style="font-family: 'Amiri', serif;">مَنْ أَحَبَّ قَوْمًا حُشِرَ مَعَهُمْ</p>
                <p class="mt-1 text-xs text-white/50">Barangsiapa mencintai suatu kaum, ia akan dibangkitkan bersama mereka.</p>
            </div>

            <div class="relative flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] text-white/45">
                <span>© {{ now()->year }} NU Ranting Banjaranyar</span>
                <span aria-hidden="true">·</span>
                <span>nubanjaranyar@gmail.com</span>
            </div>
        </aside>

        {{-- Panel formulir --}}
        <main class="relative flex flex-col items-center justify-center px-5 py-10 sm:px-8 lg:col-span-7 bg-[#FDFCF7]">
            <div class="absolute inset-0 pointer-events-none"
                 style="background-image: radial-gradient(circle at 1px 1px, rgba(15,41,30,0.06) 1px, transparent 0); background-size: 22px 22px;"></div>

            {{-- Brand ringkas (mobile & tablet) --}}
            <a href="{{ route('landing') }}" class="relative lg:hidden mb-6 flex flex-col items-center text-center group">
                <img src="{{ asset('images/logo.webp') }}" alt="Logo NU Banjaranyar"
                     class="w-20 h-auto object-contain transition-transform duration-300 group-hover:scale-105">
                <span class="mt-2 text-sm font-extrabold text-[#16452F] tracking-tight">NU Ranting Banjaranyar</span>
                <span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#8A8F88]">Cilongok · Banyumas</span>
            </a>

            <div class="relative w-full max-w-md">
                <div class="rounded-3xl border border-[#E4E0D2] bg-white p-6 sm:p-8 shadow-[0_28px_55px_-22px_rgba(15,61,40,0.35)]">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-xs text-[#8A8F88]">
                    <a href="{{ route('landing') }}" class="font-semibold text-[#16452F] hover:underline underline-offset-4">&larr; Kembali ke beranda</a>
                </p>
            </div>
        </main>
    </div>
</body>
</html>

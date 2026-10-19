<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-figtree antialiased bg-slate-50">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            {{-- Sidebar (desktop) --}}
            <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col border-r border-slate-200/80 bg-white lg:flex">
                <div class="flex h-16 shrink-0 items-center border-b border-slate-200/80 px-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <x-application-logo class="h-6 w-auto fill-current" />
                        </span>
                        <span class="leading-tight">
                            <span class="block text-sm font-bold text-slate-800">{{ config('app.name') }}</span>
                            <span class="block text-[11px] font-bold uppercase tracking-widest text-slate-400">Panel Admin</span>
                        </span>
                    </a>
                </div>

                @include('layouts.sidebar-menu')
            </aside>

            {{-- Sidebar (mobile / off-canvas) --}}
            <aside x-cloak
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-slate-200/80 bg-white transition-transform duration-200 lg:hidden">
                <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-200/80 px-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <x-application-logo class="h-5 w-auto fill-current" />
                        </span>
                        <span class="text-sm font-bold leading-tight text-slate-800">
                            {{ config('app.name') }}
                            <span class="block text-[11px] font-bold uppercase tracking-widest text-slate-400">Panel Admin</span>
                        </span>
                    </div>

                    <button type="button" @click="sidebarOpen = false"
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200/80 text-slate-400 transition-all hover:bg-slate-50 hover:text-slate-600 active:scale-95"
                            aria-label="Tutup menu">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @include('layouts.sidebar-menu')
            </aside>

            {{-- Backdrop (mobile) --}}
            <div x-cloak x-show="sidebarOpen" x-transition.opacity
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden"></div>

            {{-- Main content --}}
            <div class="flex min-h-screen flex-col lg:ps-64">
                {{-- Topbar (mobile) --}}
                <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-slate-200/80 bg-white px-4 lg:hidden">
                    <button type="button" @click="sidebarOpen = true"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200/80 bg-white text-slate-500 transition-all hover:bg-slate-50 hover:text-emerald-600 active:scale-95"
                            aria-label="Buka menu">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <x-application-logo class="h-5 w-auto fill-current" />
                        </span>
                        <span class="text-sm font-bold text-slate-800">{{ config('app.name') }}</span>
                    </a>
                </header>

                {{-- Page Heading --}}
                @isset($header)
                    <div class="border-b border-slate-200/80 bg-white">
                        <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                {{-- Page Content --}}
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var storageKey = 'admin-sidebar-scroll';

                document.querySelectorAll('[data-sidebar-nav]').forEach(function (nav, index) {
                    var itemKey = storageKey + ':' + index;
                    var activeItem = nav.querySelector('[aria-current="page"]');

                    if (activeItem) {
                        var navRect = nav.getBoundingClientRect();
                        var itemRect = activeItem.getBoundingClientRect();
                        nav.scrollTop += (itemRect.top - navRect.top) - (navRect.height - itemRect.height) / 2;
                    } else {
                        try {
                            var savedScroll = window.sessionStorage.getItem(itemKey);

                            if (savedScroll !== null) {
                                nav.scrollTop = parseFloat(savedScroll);
                            }
                        } catch (error) {
                            // sessionStorage bisa gagal di mode privat, abaikan.
                        }
                    }

                    nav.addEventListener('scroll', function () {
                        try {
                            window.sessionStorage.setItem(itemKey, String(nav.scrollTop));
                        } catch (error) {
                            // abaikan
                        }
                    }, { passive: true });
                });
            });
        </script>
    </body>
</html>

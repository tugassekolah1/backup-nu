<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
{{-- Alpine.js hanya dari bundle Vite (resources/js/app.js) — JANGAN tambah CDN lagi agar tidak double-init --}}

<!-- NAVBAR PUBLIK -->
<header x-data="{ mobileMenuOpen: false, currentTab: 'main' }" id="main-navbar" class="fixed top-0 inset-x-0 z-50 header-floating font-sans">
    <div class="navbar-wrapper max-w-7xl mx-auto px-4 sm:px-8 pt-4 pb-2">
        <div class="neutral-frosted rounded-2xl md:rounded-full px-5 py-3 shadow-[0_2px_10px_rgba(23,24,22,0.04),0_1px_3px_rgba(23,24,22,0.03)] flex items-center justify-between relative navbar-inner">

            <!-- Brand & Official Badge -->
            <a class="flex items-center gap-3 group shrink-0" href="{{ route('landing') }}">
                <img alt="Logo NU Kecamatan Cilongok" class="w-10 h-10 object-contain drop-shadow-sm group-hover:scale-105 transition-transform duration-300 rounded" src="{{ asset('images/logo.webp') }}"/>
                <div class="flex flex-col">
                    <span class="font-bold text-base sm:text-lg tracking-tight text-[#171816] leading-tight">MWC NU</span>
                    <span class="text-xs font-medium text-[#4F544E] tracking-wide">Cilongok, Banyumas</span>
                </div>
            </a>

            <!-- DESKTOP NAV (Kategori Ringkas dengan Dropdown) -->
            <nav class="hidden lg:flex items-center gap-1 text-sm font-medium text-[#4F544E]">
                <a class="px-4 py-2 rounded-full transition-colors {{ request()->routeIs('landing') ? 'text-[#171816] font-semibold bg-[#EDE8DD]' : 'hover:text-[#171816] hover:bg-[#EDE8DD]/50' }}" href="{{ route('landing') }}">
                    Beranda
                </a>

                <!-- Dropdown Informasi -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @keydown.escape.window="open = false" class="relative">
                    <button :aria-expanded="open" class="px-4 py-2 rounded-full flex items-center gap-1 transition-colors {{ request()->routeIs('agenda.*') || request()->routeIs('berita.*') || request()->routeIs('galeri.*') ? 'text-[#171816] font-semibold bg-[#EDE8DD]' : 'hover:text-[#171816] hover:bg-[#EDE8DD]/50' }}">
                        <span>Informasi</span>
                        <span class="material-symbols-outlined text-sm transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>

                    {{-- Wrapper pt-2 sebagai jembatan hover: tanpa celah mati antara tombol dan panel --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         x-cloak
                         class="absolute top-full left-0 pt-2 bg-transparent">
                        <div class="w-48 neutral-frosted rounded-2xl p-2 shadow-xl border border-[#E5E2D9] flex flex-col gap-1">
                        <a href="{{ route('berita.public') }}" class="px-4 py-2.5 rounded-xl hover:bg-[#EDE8DD]/70 transition-colors text-left flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">newspaper</span>
                            <span>Warta Kabar</span>
                        </a>
                        <a href="{{ route('agenda.public') }}" class="px-4 py-2.5 rounded-xl hover:bg-[#EDE8DD]/70 transition-colors text-left flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">event</span>
                            <span>Agenda</span>
                        </a>
                        <a href="{{ route('galeri.index') }}" class="px-4 py-2.5 rounded-xl hover:bg-[#EDE8DD]/70 transition-colors text-left flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">photo_library</span>
                            <span>Galeri</span>
                        </a>
                        
                        </div>
                    </div>
                </div>

                <a class="px-4 py-2 rounded-full transition-colors {{ request()->routeIs('infaq.*') ? 'text-[#171816] font-semibold bg-[#EDE8DD]' : 'hover:text-[#171816] hover:bg-[#EDE8DD]/50' }}" href="{{ route('infaq.index') }}">
                    Infaq
                </a>

               

                <!-- Dropdown Keanggotaan -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @keydown.escape.window="open = false" class="relative">
                    <button :aria-expanded="open" class="px-4 py-2 rounded-full flex items-center gap-1 transition-colors {{ request()->routeIs('members.*') || request()->routeIs('aspirasi.*') || request()->routeIs('organisasi.*') || request()->routeIs('member.*') ? 'text-[#171816] font-semibold bg-[#EDE8DD]' : 'hover:text-[#171816] hover:bg-[#EDE8DD]/50' }}">
                        <span>Keanggotaan</span>
                        <span class="material-symbols-outlined text-sm transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>

                    {{-- Wrapper pt-2 sebagai jembatan hover: tanpa celah mati antara tombol dan panel --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         x-cloak
                         class="absolute top-full right-0 pt-2 bg-transparent">
                        <div class="w-52 neutral-frosted rounded-2xl p-2 shadow-xl border border-[#E5E2D9] flex flex-col gap-1">
                        <a href="{{ route('members.card') }}" class="px-4 py-2.5 rounded-xl hover:bg-[#EDE8DD]/70 transition-colors text-left flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">id_card</span>
                            <span>Kartu Anggota</span>
                        </a>
                        <a href="{{ route('member.login') }}" class="px-4 py-2.5 rounded-xl hover:bg-[#EDE8DD]/70 transition-colors text-left flex items-center gap-2 {{ request()->routeIs('member.*') ? 'bg-[#EDE8DD] font-semibold' : '' }}">
                            <span class="material-symbols-outlined text-[18px]">login</span>
                            <span>Masuk Anggota</span>
                        </a>
                        <a href="{{ route('organisasi') }}" class="px-4 py-2.5 rounded-xl hover:bg-[#EDE8DD]/70 transition-colors text-left flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">groups</span>
                            <span>Organisasi</span>
                        </a>
                       
                        <a href="{{ route('aspirasi.index') }}" class="px-4 py-2.5 rounded-xl hover:bg-[#EDE8DD]/70 transition-colors text-left flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">chat_bubble</span>
                            <span>Kotak Aspirasi</span>
                        </a>
                        </div>
                    </div>
                </div>
                 <a class="px-4 py-2 rounded-full transition-colors {{ request()->routeIs('kontak') ? 'text-[#171816] font-semibold bg-[#EDE8DD]' : 'hover:text-[#171816] hover:bg-[#EDE8DD]/50' }}" href="{{ route('kontak') }}">
                    Kontak
                </a>
            </nav>

            <!-- Action CTAs & Mobile Toggle -->
            <div class="flex items-center gap-2 sm:gap-3">
                <a class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 min-h-[44px] rounded-[14px] bg-[#16452F] text-white font-medium text-sm hover:bg-[#113725] transition-all active:scale-95 shadow-sm {{ request()->routeIs('members.register-form') ? 'ring-2 ring-[#B49352]' : '' }}" href="{{ route('members.register-form') }}">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    <span>Daftar Anggota</span>
                </a>
                <a class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 min-h-[44px] rounded-[14px] text-[#16452F] font-semibold text-sm border border-[#16452F]/35 hover:bg-[#16452F]/5 transition-all {{ request()->routeIs('member.*') ? 'bg-[#16452F] text-white hover:bg-[#113725]' : '' }}" href="{{ route('member.login') }}">
                    <span class="material-symbols-outlined text-[18px]">login</span>
                    <span>Masuk Anggota</span>
                </a>
                <a class="hidden md:inline-flex items-center gap-1.5 px-4 py-2 min-h-[44px] rounded-[14px] bg-[#FDFCF7] hover:bg-[#EDE8DD]/60 text-[#171816] font-semibold text-xs sm:text-sm border border-[#D5D2C8] transition-all" href="https://wa.me/{{ config('kontak.whatsapp') }}" rel="noopener noreferrer" target="_blank">
                    <span class="material-symbols-outlined text-[#4F544E] text-[18px]">chat</span>
                    <span>Hubungi Kami</span>
                </a>

                <!-- Mobile Hamburger Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen; if(!mobileMenuOpen) currentTab = 'main'" 
                        class="lg:hidden p-2.5 min-w-[44px] min-h-[44px] rounded-xl bg-[#EDE8DD]/50 hover:bg-[#EDE8DD]/80 text-[#171816] focus:outline-none transition-colors" 
                        aria-label="Buka menu navigasi">
                    <span class="material-symbols-outlined block text-2xl" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
                </button>
            </div>
        </div>

        <!-- MOBILE FULLSCREEN OVERLAY MENU (STYLE APPLE) -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             x-cloak
             class="fixed inset-0 top-[72px] z-40 bg-[#FDFCF7]/98 neutral-frosted bg px-6 py-6 flex flex-col justify-between overflow-y-auto lg:hidden">
            
            <!-- LAYER 1: MENU UTAMA -->
            <div x-show="currentTab === 'main'"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 -translate-x-6"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="flex flex-col gap-1.5 text-lg font-semibold text-[#171816]">
                
                <a class="px-5 py-4 min-h-[52px] rounded-2xl flex items-center justify-between transition-all hover:bg-[#EDE8DD]/50" href="{{ route('landing') }}">
                    <span>Beranda</span>
                </a>

                <!-- Trigger Submenu 1: Informasi -->
                <button @click="currentTab = 'informasi'" class="w-full px-5 py-4 min-h-[52px] rounded-2xl flex items-center justify-between text-left transition-all hover:bg-[#EDE8DD]/50">
                    <span>Informasi & Berita</span>
                    <span class="material-symbols-outlined text-[#4F544E]">chevron_right</span>
                </button>

                <a class="px-5 py-4 min-h-[52px] rounded-2xl flex items-center justify-between transition-all hover:bg-[#EDE8DD]/50" href="{{ route('infaq.index') }}">
                    <span>Infaq</span>
                </a>

                
                <!-- Trigger Submenu 2: Keanggotaan -->
                <button @click="currentTab = 'keanggotaan'" class="w-full px-5 py-4 min-h-[52px] rounded-2xl flex items-center justify-between text-left transition-all hover:bg-[#EDE8DD]/50">
                    <span>Keanggotaan & Layanan</span>
                    <span class="material-symbols-outlined text-[#4F544E]">chevron_right</span>
                </button>
            </div>
            <a class="px-5 py-4 min-h-[52px] rounded-2xl flex items-center justify-between transition-all hover:bg-[#EDE8DD]/50" href="{{ route('kontak') }}">
                    <span>Kontak</span>
                </a>


            <!-- LAYER 2: SUBMENU INFORMASI -->
            <div x-show="currentTab === 'informasi'"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-x-6"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="flex flex-col gap-2">
                
                <!-- Tombol Kembali Ala Apple -->
                <button @click="currentTab = 'main'" class="flex items-center gap-1.5 px-3 py-2 text-[#4F544E] text-base font-semibold mb-2 hover:text-[#171816]">
                    <span class="material-symbols-outlined">chevron_left</span>
                    <span>Kembali</span>
                </button>

                <div class="px-4 pb-2 text-xs font-bold uppercase tracking-wider text-[#8A8F88]">Informasi & Berita</div>

                <a class="px-5 py-3.5 rounded-2xl flex items-center gap-3 transition-all hover:bg-[#EDE8DD]/50 text-base font-medium" href="{{ route('berita.public') }}">
                    <span class="material-symbols-outlined text-[#4F544E]">newspaper</span>
                    <span>Warta Kabar</span>
                </a>
                <a class="px-5 py-3.5 rounded-2xl flex items-center gap-3 transition-all hover:bg-[#EDE8DD]/50 text-base font-medium" href="{{ route('agenda.public') }}">
                    <span class="material-symbols-outlined text-[#4F544E]">event</span>
                    <span>Agenda Kegiatan</span>
                </a>
                <a class="px-5 py-3.5 rounded-2xl flex items-center gap-3 transition-all hover:bg-[#EDE8DD]/50 text-base font-medium" href="{{ route('galeri.index') }}">
                    <span class="material-symbols-outlined text-[#4F544E]">photo_library</span>
                    <span>Galeri Dokumentasi</span>
                </a>
            </div>

            <!-- LAYER 2: SUBMENU KEANGGOTAAN -->
            <div x-show="currentTab === 'keanggotaan'"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-x-6"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="flex flex-col gap-2">
                
                <!-- Tombol Kembali Ala Apple -->
                <button @click="currentTab = 'main'" class="flex items-center gap-1.5 px-3 py-2 text-[#4F544E] text-base font-semibold mb-2 hover:text-[#171816]">
                    <span class="material-symbols-outlined">chevron_left</span>
                    <span>Kembali</span>
                </button>

                <div class="px-4 pb-2 text-xs font-bold uppercase tracking-wider text-[#8A8F88]">Keanggotaan & Layanan</div>

                <a class="px-5 py-3.5 rounded-2xl flex items-center gap-3 transition-all hover:bg-[#EDE8DD]/50 text-base font-medium" href="{{ route('members.card') }}">
                    <span class="material-symbols-outlined text-[#4F544E]">id_card</span>
                    <span>Cetak Kartu Anggota</span>
                </a>
                <a class="px-5 py-3.5 rounded-2xl flex items-center gap-3 transition-all hover:bg-[#EDE8DD]/50 text-base font-medium" href="{{ route('member.login') }}">
                    <span class="material-symbols-outlined text-[#4F544E]">login</span>
                    <span>Masuk / Ubah Data Diri</span>
                </a>
                <a class="px-5 py-3.5 rounded-2xl flex items-center gap-3 transition-all hover:bg-[#EDE8DD]/50 text-base font-medium" href="{{ route('organisasi') }}">
                    <span class="material-symbols-outlined text-[#4F544E]">account_tree</span>
                    <span>Organisasi &amp; Struktur Pengurus</span>
                </a>
                <a class="px-5 py-3.5 rounded-2xl flex items-center gap-3 transition-all hover:bg-[#EDE8DD]/50 text-base font-medium" href="{{ route('aspirasi.index') }}">
                    <span class="material-symbols-outlined text-[#4F544E]">chat_bubble</span>
                    <span>Kotak Aspirasi</span>
                </a>
            </div>

            <!-- Bottom Action CTAs -->
            <div class="pt-6 mt-6 border-t border-[#E5E2D9] flex flex-col gap-3">
                <a class="flex items-center justify-center gap-2 px-5 py-4 min-h-[52px] rounded-2xl bg-[#16452F] text-white font-semibold text-base shadow-sm active:scale-95 transition-all" href="{{ route('members.register-form') }}">
                    <span class="material-symbols-outlined text-[20px]">how_to_reg</span>
                    <span>Daftar Anggota</span>
                </a>
                <a class="flex items-center justify-center gap-2 px-5 py-4 min-h-[52px] rounded-2xl bg-[#FDFCF7] text-[#16452F] font-semibold text-base border border-[#16452F]/40 active:scale-95 transition-all" href="{{ route('member.login') }}">
                    <span class="material-symbols-outlined text-[20px]">login</span>
                    <span>Masuk Anggota</span>
                </a>
                <a class="flex items-center justify-center gap-2 px-4 py-3.5 min-h-[52px] rounded-2xl bg-[#FDFCF7] text-[#171816] font-semibold text-base border border-[#D5D2C8] active:scale-95 transition-all" href="https://wa.me/{{ config('kontak.whatsapp') }}" rel="noopener noreferrer" target="_blank">
                    <span class="material-symbols-outlined text-[#16452F] text-[20px]">chat</span>
                    <span>Hubungi Kami (WhatsApp)</span>
                </a>
            </div>
        </div>

        @if(isset($slot) && $slot->isNotEmpty())
            <div class="mt-2">
                {{ $slot }}
            </div>
        @endif
    </div>
</header>

<style>
    [x-cloak] { display: none !important; }

    .neutral-frosted {
        background: rgba(247, 245, 239, 0.88);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(215, 210, 200, 0.7);
    }

    #main-navbar .navbar-wrapper,
    #main-navbar .navbar-inner {
        transition: max-width 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                    padding 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                    border-radius 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                    margin 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.3s ease;
    }

    /* STATE 1: FLOATING */
    #main-navbar.header-floating .navbar-wrapper {
        max-width: 90rem;
        padding-left: 1rem;
        padding-right: 1rem;
        padding-top: 1rem;
        padding-bottom: 0.5rem;
    }

    /* STATE 2: STICKY FULL WIDTH */
    #main-navbar.header-sticky-full .navbar-wrapper {
        max-width: 100%;
        padding-left: 0;
        padding-right: 0;
        padding-top: 0;
        padding-bottom: 0;
    }

    #main-navbar.header-sticky-full .navbar-inner {
        border-radius: 0 !important;
        max-width: 100%;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        box-shadow: 0 8px 24px rgba(23, 24, 22, 0.10);
    }

    @media (min-width: 640px) {
        #main-navbar.header-sticky-full .navbar-inner {
            padding-left: 2rem;
            padding-right: 2rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const header = document.getElementById('main-navbar');
        if (!header) return;

        let lastScrollY = window.scrollY;
        let ticking = false;
        const TOP_THRESHOLD = 12;

        function updateNavbar() {
            const currentScrollY = window.scrollY;

            if (currentScrollY <= TOP_THRESHOLD) {
                header.classList.remove('header-sticky-full');
                header.classList.add('header-floating');
            } else if (currentScrollY > lastScrollY) {
                header.classList.remove('header-floating');
                header.classList.add('header-sticky-full');
            } else {
                header.classList.remove('header-sticky-full');
                header.classList.add('header-floating');
            }

            lastScrollY = currentScrollY;
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(updateNavbar);
                ticking = true;
            }
        }, { passive: true });
    });
</script>
@props(['kontak' => []])

<footer class="w-full bg-[#141815] text-white pt-16 pb-12 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 pb-12 border-b border-white/10">
            <!-- Col 1: Identity & Address -->
            <div class="lg:col-span-5 flex flex-col">
                <div class="flex items-center gap-3 mb-4">
                    <img alt="Emblem NU" class="w-10 h-10 object-contain brightness-0 invert opacity-90" src="https://lh3.googleusercontent.com/aida/AEtjO1VVjAZeFl8x9UC1ygLyGJQ7s08PMheg8thGTpLjMGEDNy6dxehyc8qxHnJ3TWq0onXXPh6_HPNWDese-jycKUQ2eb98-bnO7YW_VY0GaCIEySrmJqvz-GHn0s7CMUqoutQaae-CsDK4XqFr1CTpqkfXyJQS3h0oeeJcHzC-gIHlGibUdsPPEn0o-vdxP46pPspwdFoEMDPWgm6H4UW6yedGPXqeoPVeyU2SVeokYJCsQ1wcEIIhfseMkPXD"/>
                    <div>
                        <span class="font-extrabold text-lg tracking-tight block leading-none text-white">NU BANJARANYAR</span>
                        <span class="text-xs text-white/60 font-medium">Kecamatan Cilongok, Banyumas</span>
                    </div>
                </div>
                <p class="text-sm text-white/70 leading-relaxed max-w-sm mb-6">
                    Pengurus Ranting Nahdlatul Ulama Desa Banjaranyar, MWC NU Kecamatan Cilongok, PCNU Kabupaten Banyumas. Merawat akidah Ahlussunnah wal Jama'ah an-Nahdliyah.
                </p>
                <div class="flex items-start gap-2.5 text-xs text-white/60 leading-relaxed">
                    <span class="material-symbols-outlined text-[#B49352] text-[18px] shrink-0">location_on</span>
                    <span>{{ $kontak['alamat'] ?? 'Gedung Sekretariat PRNU, Jl. Raya Cilongok No. 12, Banjaranyar, Banyumas, Jawa Tengah 53162' }}</span>
                </div>
            </div>

            <!-- Col 2: Quick Links -->
            <div class="lg:col-span-3 flex flex-col">
                <span class="text-xs font-bold tracking-wider text-[#B49352] uppercase mb-4">Tautan Navigasi</span>
                <ul class="space-y-2.5 text-sm text-white/70">
                    <li><a class="hover:text-white transition-colors" href="{{ route('landing') }}">Beranda Ranting</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('agenda.public') }}">Jadwal Agenda & Majelis</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('landing') }}#berita">Warta & Kabar Terkini</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('berita.public') }}">Semua Warta Kabar</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('organisasi') }}">Organisasi &amp; Struktur Pengurus</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('members.register-form') }}">Pendaftaran KARTANU</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('aspirasi.index') }}">Kotak Aspirasi Warga</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('members.card') }}">Kartu Anggota</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('member.login') }}">Masuk Anggota</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('kontak') }}">Kontak & Alamat Sekretariat</a></li>
                </ul>
            </div>

            <!-- Col 3: Banom & Lembaga -->
            <div class="lg:col-span-4 flex flex-col">
                <span class="text-xs font-bold tracking-wider text-[#B49352] uppercase mb-4">Badan Otonom & Lembaga</span>
                <div class="grid grid-cols-2 gap-2.5 text-sm text-white/70">
                    <a class="hover:text-white transition-colors" href="{{ route('organisasi.show', 'muslimat') }}">• Muslimat NU</a>
                    <a class="hover:text-white transition-colors" href="{{ route('organisasi.show', 'gp-ansor') }}">• GP Ansor</a>
                    <a class="hover:text-white transition-colors" href="{{ route('organisasi.show', 'fatayat') }}">• Fatayat NU</a>
                    <a class="hover:text-white transition-colors" href="{{ route('organisasi.show', ['slug' => 'gp-ansor', 'tab' => 'struktur']) }}">• Banser Satkoryon</a>
                    <a class="hover:text-white transition-colors" href="{{ route('organisasi.show', 'ipnu') }}">• IPNU &amp; IPPNU</a>
                    <a class="hover:text-white transition-colors" href="{{ route('infaq.index') }}">• LAZISNU UPZIS</a>
                </div>
                <div class="mt-6 pt-4 border-t border-white/10 text-xs text-white/60">
                    <p><strong class="text-white">Email:</strong> {{ $kontak['email'] ?? 'sekretariat@nubanjaranyar.or.id' }}</p>
                    <p class="mt-1"><strong class="text-white">Jam Khidmat:</strong> {{ $kontak['jam_layanan'] ?? 'Setiap Hari Ahad & Rabu (08.00 - 16.00 WIB)' }}</p>
                </div>
            </div>
        </div>

        <!-- Copyright & Calligraphy -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-white/60">
            <p>© {{ now()->year }} Nahdlatul Ulama Banjaranyar, Cilongok, Banyumas. Khidmat untuk Umat & Bangsa.</p>
            <p class="text-lg text-[#B49352] tracking-wide" style="font-family: 'Amiri', serif;">مَنْ أَحَبَّ قَوْمًا حُشِرَ مَعَهُمْ</p>
        </div>
    </div>
</footer>

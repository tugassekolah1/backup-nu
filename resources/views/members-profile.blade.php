<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <x-public-head title="Data Diri Saya — NU Banjaranyar" />
</head>

<body class="bg-[#F5F7F4] text-[#17392D] antialiased selection:bg-[#E7ECE4] selection:text-[#16452F]">
    <x-navbar />

    <main>
        <!-- HERO SINGKAT -->
        <header class="relative overflow-hidden bg-[#16452F] text-white pt-32 sm:pt-36 pb-10">
            <div class="absolute -top-16 -right-10 w-72 h-72 rounded-full bg-[#8bc14b]/10 blur-3xl pointer-events-none"></div>
            <div class="max-w-6xl mx-auto px-5 relative z-10 flex items-start justify-between gap-4">
                <div>
                    <nav aria-label="Navigasi balik" class="mb-4 text-sm text-white/70">
                        <a class="hover:text-white hover:underline underline-offset-4" href="{{ route('landing') }}">Beranda</a>
                        <span aria-hidden="true" class="mx-1.5">›</span>
                        <span class="text-white font-semibold">Data Diri Saya</span>
                    </nav>
                    <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight tracking-tight">Data Diri Saya</h1>
                    <p class="mt-2 max-w-2xl text-white/80">Perubahan di halaman ini langsung tampil pada kartu tanda anggota Anda — termasuk nama, alamat, organisasi, dan foto.</p>
                </div>

                <!-- Tombol keluar ala sosmed, selalu terlihat di header -->
                <form method="POST" action="{{ route('member.logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                            title="Keluar dari akun anggota"
                            class="inline-flex items-center gap-2 rounded-2xl border border-white/25 bg-white/10 px-3.5 py-2.5 sm:px-4 text-sm font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <div class="mx-auto max-w-6xl px-5 py-10 grid gap-8 lg:grid-cols-12">
            <!-- FORM DATA DIRI -->
            <section class="lg:col-span-8">
                <div class="rounded-3xl border border-[#DCE4DE] bg-white p-6 sm:p-8 shadow-[0_8px_24px_rgba(23,24,22,0.05)]">

                    @if (session('success'))
                        <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800 flex items-start gap-2" role="status">
                            <span class="material-symbols-outlined text-green-600 text-[20px]">check_circle</span>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                            <p class="font-bold flex items-center gap-2"><span class="material-symbols-outlined text-[20px]">error</span> Periksa lagi isian berikut:</p>
                            <ul class="mt-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @csrf
                        @method('PATCH')

                        <!-- NIK (tidak dapat diubah) -->
                        <div class="sm:col-span-2">
                            <label for="nik" class="text-xs font-bold uppercase tracking-wider text-[#4F544E]">NIK</label>
                            <input id="nik" type="text" value="{{ $member->nik }}" disabled
                                   class="mt-1.5 w-full rounded-xl border border-[#D5D2C8] bg-[#EDE8DD]/40 px-4 py-3 text-[#6B7268] cursor-not-allowed font-mono tracking-wider">
                            <p class="mt-1.5 text-xs text-[#8A8F88]">NIK sebagai identitas login &amp; isi QR pada kartu tidak dapat diubah. Hubungi admin bila ada kesalahan.</p>
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="sm:col-span-2">
                            <label for="full_name" class="text-xs font-bold uppercase tracking-wider text-[#4F544E]">Nama Lengkap <span class="text-red-600">*</span></label>
                            <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $member->full_name) }}" required maxlength="255" autocomplete="name"
                                   class="mt-1.5 w-full rounded-xl border border-[#D5D2C8] bg-[#FDFCF7] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#16452F]/30 focus:border-[#16452F]">
                        </div>

                        <!-- No. HP -->
                        <div>
                            <label for="phone" class="text-xs font-bold uppercase tracking-wider text-[#4F544E]">No. WhatsApp / HP <span class="text-red-600">*</span></label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone', $member->phone) }}" required maxlength="20" inputmode="numeric" autocomplete="tel"
                                   class="mt-1.5 w-full rounded-xl border border-[#D5D2C8] bg-[#FDFCF7] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#16452F]/30 focus:border-[#16452F]">
                            <p class="mt-1.5 text-xs text-[#8A8F88]">Dipakai memverifikasi saat buka Kartu Anggota &amp; setel ulang kata sandi.</p>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label for="gender" class="text-xs font-bold uppercase tracking-wider text-[#4F544E]">Jenis Kelamin <span class="text-red-600">*</span></label>
                            <select id="gender" name="gender" required
                                    class="mt-1.5 w-full rounded-xl border border-[#D5D2C8] bg-[#FDFCF7] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#16452F]/30 focus:border-[#16452F]">
                                <option value="L" @selected(old('gender', $member->gender) === 'L')>Laki-laki</option>
                                <option value="P" @selected(old('gender', $member->gender) === 'P')>Perempuan</option>
                            </select>
                        </div>

                        <!-- Organisasi -->
                        <div class="sm:col-span-2">
                            <label for="organisasi" class="text-xs font-bold uppercase tracking-wider text-[#4F544E]">Organisasi / Banom</label>
                            <select id="organisasi" name="organisasi"
                                    class="mt-1.5 w-full rounded-xl border border-[#D5D2C8] bg-[#FDFCF7] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#16452F]/30 focus:border-[#16452F]">
                                @include('partials.organisasi-options', ['selected' => old('organisasi', $member->organisasi)])
                            </select>
                        </div>

                        <!-- Alamat -->
                        <div class="sm:col-span-2">
                            <label for="address" class="text-xs font-bold uppercase tracking-wider text-[#4F544E]">Alamat Lengkap <span class="text-red-600">*</span></label>
                            <textarea id="address" name="address" required rows="3" autocomplete="street-address"
                                      placeholder="Jalan, RT/RW, Desa/Kelurahan, Kecamatan..."
                                      class="mt-1.5 w-full rounded-xl border border-[#D5D2C8] bg-[#FDFCF7] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#16452F]/30 focus:border-[#16452F]">{{ old('address', $member->address) }}</textarea>
                        </div>

                        <!-- Foto -->
                        <div class="sm:col-span-2">
                            <label for="photo" class="text-xs font-bold uppercase tracking-wider text-[#4F544E]">Foto Diri (JPG/PNG, maks 2 MB)</label>
                            <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-4">
                                @if ($member->photo)
                                    <img src="{{ $member->photo_url }}" alt="Foto {{ $member->full_name }}"
                                         class="h-28 w-24 rounded-2xl object-cover border border-[#D5D2C8] shadow-sm">
                                @else
                                    <div class="h-28 w-24 rounded-2xl border border-dashed border-[#C9C4B4] flex flex-col items-center justify-center text-[#8A8F88] bg-[#FDFCF7]">
                                        <span class="material-symbols-outlined text-3xl">account_circle</span>
                                        <span class="text-[10px] font-semibold mt-1">Belum ada foto</span>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <input id="photo" name="photo" type="file" accept="image/jpeg,image/png"
                                           class="block w-full text-sm text-[#4F544E] file:mr-3 file:rounded-xl file:border-0 file:bg-[#16452F] file:px-4 file:py-2.5 file:text-white file:font-semibold hover:file:bg-[#113725] file:cursor-pointer"
                                           onchange="previewProfilePhoto(event)">
                                    <p class="mt-1.5 text-xs text-[#8A8F88]">Kosongkan bila tidak ingin mengganti foto. Foto ini yang tercetak pada kartu anggota Anda.</p>
                                </div>
                            </div>
                        </div>

                        <div class="sm:col-span-2 pt-2 border-t border-[#E5E2D9] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <p class="text-xs text-[#8A8F88]">Nomor kartu anggota tetap dikelola oleh admin.</p>
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#16452F] px-6 py-3 text-white font-semibold hover:bg-[#113725] transition-colors active:scale-[0.98]">
                                <span class="material-symbols-outlined text-[20px]">save</span>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- PANEL SAMPING -->
            <aside class="lg:col-span-4 flex flex-col gap-6">
                <!-- Ringkasan keanggotaan -->
                <div class="rounded-3xl border border-[#DCE4DE] bg-white p-6 shadow-[0_8px_24px_rgba(23,24,22,0.05)]">
                    <div class="flex items-center gap-4">
                        @if ($member->photo)
                            <img src="{{ $member->photo_url }}" alt="Foto {{ $member->full_name }}" class="h-20 w-16 rounded-xl object-cover border border-[#D5D2C8]">
                        @else
                            <div class="h-20 w-16 rounded-xl border border-[#D5D2C8] bg-[#FDFCF7] flex items-center justify-center">
                                <span class="material-symbols-outlined text-4xl text-[#B6B1A2]">account_circle</span>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-lg font-extrabold leading-snug truncate">{{ $member->full_name }}</p>
                            <p class="text-xs text-[#8A8F88] font-mono">{{ $member->nik }}</p>
                            @if ($member->member_card_no)
                                <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-[#EDE8DD] px-2.5 py-0.5 text-[11px] font-bold text-[#16452F]">{{ $member->member_card_no }}</span>
                            @endif
                        </div>
                    </div>

                    @if ($member->organisasi)
                        <div class="mt-4 rounded-2xl bg-[#F5F7F4] px-4 py-3 text-sm">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-[#8A8F88]">Organisasi</p>
                            <p class="mt-0.5 font-semibold">{{ $member->organisasi_label }}</p>
                        </div>
                    @endif

                    <a href="{{ route('members.card', ['nik' => $member->nik, 'phone' => $member->phone]) }}"
                       class="mt-4 flex items-center justify-center gap-2 rounded-2xl border border-[#16452F] px-4 py-3 text-sm font-bold text-[#16452F] hover:bg-[#16452F] hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-[18px]">id_card</span>
                        Lihat Kartu Anggota Saya
                    </a>
                </div>

                <!-- Sesi -->
                <div class="rounded-3xl border border-[#DCE4DE] bg-white p-6 shadow-[0_8px_24px_rgba(23,24,22,0.05)]">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-[#4F544E]">Sesi &amp; Keamanan</h2>
                    <p class="mt-2 text-sm text-[#4F544E] leading-relaxed">Kata sandi hanya bisa diubah oleh Anda sendiri. Bila lupa, gunakan fitur <a class="font-semibold text-[#16452F] underline underline-offset-2" href="{{ route('member.password.request') }}">lupa kata sandi</a> dengan NIK dan nomor HP terdaftar.</p>
                    <form method="POST" action="{{ route('member.logout') }}" class="mt-4">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl border border-[#D5D2C8] bg-[#FDFCF7] px-4 py-3 text-sm font-semibold text-[#B44141] hover:bg-red-50 hover:border-red-200 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">logout</span>
                            Keluar
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </main>

    <x-footer />

    <script>
        // Pratinjau foto yang dipilih sebelum diunggah.
        function previewProfilePhoto(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            const url = URL.createObjectURL(file);
            let preview = document.getElementById('profilePhotoPreview');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'profilePhotoPreview';
                preview.alt = 'Pratinjau foto';
                preview.className = 'mt-3 h-28 w-24 rounded-2xl object-cover border border-[#16452F]/40 shadow';
                event.target.parentElement.appendChild(preview);
            }
            preview.src = url;
        }
    </script>
</body>
</html>

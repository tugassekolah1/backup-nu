<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-xl font-extrabold text-[#16452F]">Lupa Kata Sandi?</h1>
        <p class="mt-1 text-sm text-gray-600">
            Masukkan NIK dan nomor HP yang Anda gunakan saat mendaftar, lalu tentukan kata sandi baru.
        </p>
    </div>

    <form method="POST" action="{{ route('member.password.update') }}">
        @csrf

        <!-- NIK -->
        <div>
            <x-input-label for="nik" :value="__('NIK')" />
            <x-text-input id="nik" class="block mt-1 w-full" type="text" name="nik" :value="old('nik')"
                inputmode="numeric" maxlength="16" pattern="[0-9]{16}" required autofocus autocomplete="username"
                placeholder="16 digit NIK sesuai KTP" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
        </div>

        <!-- Nomor HP -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('No. WhatsApp / HP yang terdaftar')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')"
                inputmode="numeric" required autocomplete="tel" placeholder="Contoh: 081234567890" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Kata Sandi Baru -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi Baru')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter.</p>
        </div>

        <!-- Konfirmasi Kata Sandi Baru -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Ulangi Kata Sandi Baru')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation"
                            required autocomplete="new-password" />
        </div>

        <div class="flex items-center justify-between mt-4 gap-3">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('member.login') }}">
                Kembali ke halaman masuk
            </a>

            <x-primary-button class="ms-3">
                Setel Ulang
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

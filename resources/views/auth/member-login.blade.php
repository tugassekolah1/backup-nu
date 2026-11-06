<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h1 class="text-xl font-extrabold text-[#16452F]">Masuk Anggota</h1>
        <p class="mt-1 text-sm text-gray-600">Gunakan NIK dan kata sandi keanggotaan Anda.</p>
    </div>

    <form method="POST" action="{{ route('member.login') }}">
        @csrf

        <!-- NIK -->
        <div>
            <x-input-label for="nik" :value="__('NIK')" />
            <x-text-input id="nik" class="block mt-1 w-full" type="text" name="nik" :value="old('nik')"
                inputmode="numeric" maxlength="16" pattern="[0-9]{16}" required autofocus autocomplete="username"
                placeholder="16 digit NIK sesuai KTP" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4 gap-3">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('member.password.request') }}">
                Lupa kata sandi?
            </a>

            <x-primary-button class="ms-3">
                Masuk
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-4 border-t border-[#E4E0D2] text-center text-sm text-gray-600">
        Belum punya akun?
        <a class="font-semibold text-[#16452F] hover:underline" href="{{ route('members.register-form') }}">Daftar sebagai anggota</a>
    </div>

    <div class="mt-3 text-center text-xs text-gray-500">
        Admin sekretariat? <a class="underline hover:text-gray-700" href="{{ route('login') }}">Masuk sebagai Admin</a>
    </div>
</x-guest-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Anggota Baru
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="nik" value="NIK" />
                        <x-text-input id="nik" name="nik" type="text" maxlength="16"
                                      class="mt-1 block w-full" value="{{ old('nik') }}" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="full_name" value="Nama Lengkap" />
                        <x-text-input id="full_name" name="full_name" type="text"
                                      class="mt-1 block w-full" value="{{ old('full_name') }}" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="phone" value="No. HP" />
                        <x-text-input id="phone" name="phone" type="text"
                                      class="mt-1 block w-full" value="{{ old('phone') }}" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="gender" value="Jenis Kelamin" />
                        <select id="gender" name="gender" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="organisasi" value="Organisasi / Banom (opsional)" />
                        <select id="organisasi" name="organisasi"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @include('partials.organisasi-options', ['selected' => old('organisasi')])
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Banom yang diikuti anggota, mis. Fatayat, Ansor, IPNU. Boleh dikosongkan.</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="address" value="Alamat" />
                        <textarea id="address" name="address" rows="3" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('address') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="photo" value="Foto Anggota (Opsional)" />
                        <input id="photo" name="photo" type="file" accept="image/jpeg,image/png"
                               class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:text-sm file:font-semibold hover:file:bg-emerald-100 cursor-pointer"
                               onchange="previewPhoto(this)">
                        <p class="text-xs text-slate-500 mt-1">
                            Format JPG/PNG, maksimal 2 MB. Foto akan tampil pada kartu tanda anggota.
                        </p>
                        @error('photo')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                        <img id="photoPreview" src="" alt="Preview Foto"
                             class="hidden mt-3 w-24 h-32 object-cover rounded-lg border border-slate-200 bg-slate-50">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700  px-5 py-2 rounded-lg">
                            Simpan
                        </button>
                        <a href="{{ route('members.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 px-5 py-2 rounded-lg">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function previewPhoto(input) {
            const preview = document.getElementById('photoPreview');
            if (input.files && input.files[0]) {
                preview.src = URL.createObjectURL(input.files[0]);
                preview.classList.remove('hidden');
            } else {
                preview.src = '';
                preview.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>

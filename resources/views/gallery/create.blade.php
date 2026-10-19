<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Foto Galeri
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                        <div class="font-semibold mb-1">Terjadi kesalahan pada inputan:</div>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="judul" value="Judul Foto" />
                        <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full"
                                      value="{{ old('judul') }}" required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="organisasi" value="Organisasi / Banom" />
                        <select name="organisasi" id="organisasi"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">— Tanpa organisasi (kegiatan umum) —</option>
                            @foreach ($opsiOrganisasi as $grup)
                                <optgroup label="{{ $grup['label'] }}">
                                    @foreach ($grup['opsi'] as $opsi)
                                        <option value="{{ $opsi['slug'] }}" @selected(old('organisasi') === $opsi['slug'])>
                                            {{ $opsi['singkatan'] }} — {{ $opsi['nama'] }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Menentukan organisasi tempat foto ditampilkan pada filter halaman Galeri publik.
                        </p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="deskripsi" value="Deskripsi / Keterangan" />
                        <textarea name="deskripsi" id="deskripsi" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="urutan" value="Urutan Tampilan" />
                        <x-text-input id="urutan" name="urutan" type="number" class="mt-1 block w-32"
                                      value="{{ old('urutan', 0) }}" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="foto" value="File Foto" />
                        <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg,image/webp" required
                               class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" />
                    </div>

                    <div class="mt-6 flex gap-2">
                        <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg">
                            Simpan Foto
                        </button>
                        <a href="{{ route('gallery.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 px-5 py-2 rounded-lg">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

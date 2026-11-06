<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Data Pengurus
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="mb-4 flex items-center gap-2 text-sm text-slate-600">
                    <span class="font-semibold">Nama:</span>
                    <span class="px-2 py-1 bg-slate-100 rounded-lg font-medium">{{ $pengurus->nama }}</span>
                </div>

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

                <form action="{{ route('pengurus.update', $pengurus->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="nama" value="Nama Lengkap" />
                        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full"
                                      value="{{ old('nama', $pengurus->nama) }}" required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="banom" value="Pilih Organisasi / Banom" />
                        <select name="banom" id="banom" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @foreach ($banomOptions as $kode => $opsi)
                                <option value="{{ $kode }}" data-label="{{ $opsi['label'] }}" @selected(old('banom', $pengurus->banom) === $kode)>{{ $opsi['name'] }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Label tampilan (<strong>{{ old('label_banom', $pengurus->label_banom) }}</strong>) diperbarui otomatis mengikuti pilihan ini.</p>
                    </div>

                    <x-pengurus.field-jabatan :jabatan-map="$jabatanMap" :nilai="old('jabatan', $pengurus->jabatan)" />

                    <div class="mb-4">
                        <x-input-label for="urutan" value="Urutan Posisi" />
                        <x-text-input id="urutan" name="urutan" type="number" min="1" step="1" class="mt-1 block w-32"
                                      value="{{ old('urutan', $pengurus->urutan) }}" placeholder="otomatis" />
                        <p class="text-xs text-gray-500 mt-1">Angka lebih kecil tampil lebih awal. <strong>Kosongkan</strong> untuk mengisi nomor urut otomatis (angka berikutnya).</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="foto" value="Foto Pengurus" />
                        @if ($pengurus->foto)
                            <div class="mt-2 mb-3 flex items-center gap-3">
                                <img src="{{ $pengurus->foto_url }}" alt="{{ $pengurus->nama }}" class="h-20 w-20 rounded-lg object-cover border border-slate-200" />
                                <span class="text-xs text-gray-500">Foto saat ini terpasang. Upload foto baru jika ingin mengganti.</span>
                            </div>
                        @endif
                        <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" />
                    </div>

                    <div class="mt-6 flex gap-2">
                        <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('pengurus.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 px-5 py-2 rounded-lg">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

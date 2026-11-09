@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-4">
    <x-input-label for="judul" value="Judul Berita" />
    <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full"
                  value="{{ old('judul', $berita->judul ?? '') }}" required autofocus />
</div>

<div class="mb-4">
    <x-input-label for="slug" value="Slug (URL)" />
    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full"
                  value="{{ old('slug', $berita->slug ?? '') }}"
                  placeholder="otomatis-dari-judul" />
    <p class="text-xs text-gray-500 mt-1">
        Terisi otomatis dari judul, boleh diubah. Kosongkan agar dibuat otomatis.
    </p>
</div>

<div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="jenis" value="Jenis Berita" />
        <select id="jenis" name="jenis" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @foreach (\App\Models\Berita::JENIS as $jenis)
                <option value="{{ $jenis }}"
                        {{ old('jenis', $berita->jenis ?? 'Berita') === $jenis ? 'selected' : '' }}>
                    {{ $jenis }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="tanggal_terbit" value="Tanggal Terbit (opsional)" />
        <x-text-input id="tanggal_terbit" name="tanggal_terbit" type="date" class="mt-1 block w-full"
                      max="{{ now()->toDateString() }}"
                      value="{{ old('tanggal_terbit', isset($berita) && $berita->tanggal_terbit ? $berita->tanggal_terbit->format('Y-m-d') : '') }}" />
        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak diisi. Tanggal tidak boleh setelah hari ini.</p>
    </div>
</div>

<div class="mb-4">
    <x-input-label for="isi" value="Isi Berita" />
    <textarea id="isi" name="isi" rows="8"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              required>{{ old('isi', $berita->isi ?? '') }}</textarea>
</div>

<div class="mb-4">
    <x-input-label for="gambar" value="Gambar (opsional)" />
    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-500 mb-2">Pilihan 1: Upload file gambar</p>
            <input id="gambar" name="gambar" type="file" accept="image/*"
                   class="mt-1 block w-full text-sm text-gray-600" />
        </div>

        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Atau</span>
            </div>
        </div>

        <div>
            <p class="text-xs text-gray-500 mb-2">Pilihan 2: Gunakan URL gambar dari internet</p>
            <x-text-input id="gambar_url" name="gambar_url" type="url" class="mt-1 block w-full"
                          value="{{ old('gambar_url', $berita->gambar_url ?? '') }}"
                          placeholder="https://example.com/gambar.jpg" />
        </div>
    </div>

    @isset($berita)
        @if ($berita->gambar_utama)
            <div class="mt-3">
                <p class="text-xs text-gray-500 mb-2">Gambar saat ini:</p>
                <img src="{{ $berita->gambar_utama }}" class="w-32 rounded shadow-sm border border-gray-200">
            </div>
        @endif
    @endisset
</div>

<div class="mb-4 flex items-center gap-2">
    <input type="checkbox" id="status" name="status" value="1"
           {{ old('status', $berita->status ?? true) ? 'checked' : '' }}
           class="rounded border-gray-300 text-blue-600 shadow-sm">
    <x-input-label for="status" value="Publish sekarang" />
</div>

<script>
    (function () {
        const judul = document.getElementById('judul');
        const slug = document.getElementById('slug');
        if (!judul || !slug) return;

        let manualEdit = false;
        slug.addEventListener('input', function () {
            manualEdit = true;
        });

        function slugify(value) {
            return value
                .toLowerCase()
                .trim()
                .replace(/['’]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        function autoFill() {
            if (!manualEdit || !slug.value) {
                slug.value = slugify(judul.value);
            }
        }

        judul.addEventListener('input', autoFill);

        if (!slug.value) {
            slug.value = slugify(judul.value);
        }
    })();
</script>

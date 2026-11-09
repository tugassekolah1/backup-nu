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
    <x-input-label for="arah" value="Arah Kas" />
    <select id="arah" name="arah" required
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        @foreach (\App\Models\Infaq::ARAH as $item)
            <option value="{{ $item }}"
                    {{ old('arah', $infaq->arah ?? 'masuk') === $item ? 'selected' : '' }}>
                {{ $item === 'masuk' ? 'Kas Masuk (Donasi diterima)' : 'Kas Keluar (Penyaluran / Belanja)' }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <x-input-label for="nominal" value="Nominal (Rp)" />
    <x-text-input id="nominal" name="nominal" type="number" min="1" step="1" class="mt-1 block w-full"
                  value="{{ old('nominal', $infaq->nominal ?? '') }}" placeholder="Contoh: 50000" required autofocus />
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="mb-4">
        <x-input-label for="nama_donatur" value="Nama Donatur" />
        <x-text-input id="nama_donatur" name="nama_donatur" type="text" class="mt-1 block w-full"
                      value="{{ old('nama_donatur', $infaq->nama_donatur ?? '') }}" placeholder="Hamba Allah jika kosong" />
    </div>

    <div class="mb-4">
        <x-input-label for="no_hp" value="No. HP (opsional)" />
        <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full"
                      value="{{ old('no_hp', $infaq->no_hp ?? '') }}" placeholder="08xxxxxxxxxx" />
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="mb-4">
        <x-input-label for="metode_pembayaran" value="Metode Pembayaran" />
        <select id="metode_pembayaran" name="metode_pembayaran" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @foreach (\App\Models\Infaq::METODE as $metode)
                <option value="{{ $metode }}"
                        {{ old('metode_pembayaran', $infaq->metode_pembayaran ?? 'tunai') === $metode ? 'selected' : '' }}>
                    {{ str_replace('_', ' ', ucwords($metode, '_')) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <x-input-label for="status" value="Status" />
        <select id="status" name="status" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @foreach (\App\Models\Infaq::STATUSES as $item)
                <option value="{{ $item }}"
                        {{ old('status', $infaq->status ?? 'pending') === $item ? 'selected' : '' }}>
                    {{ ucfirst($item) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <x-input-label for="paid_at" value="Tanggal Bayar" />
        <x-text-input id="paid_at" name="paid_at" type="date" class="mt-1 block w-full"
                      max="{{ now()->toDateString() }}"
                      value="{{ old('paid_at', isset($infaq) && $infaq->paid_at ? $infaq->paid_at->format('Y-m-d') : '') }}"
                      onchange="if(this.value && this.value > this.max) this.value = this.max;" />
        <p class="text-xs text-gray-500 mt-1">Diisi otomatis saat status Lunas.</p>
    </div>
</div>

<div class="mb-4">
    <x-input-label for="catatan" value="Keperluan / Catatan" />
    <textarea id="catatan" name="catatan" rows="3"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              placeholder="Kas masuk: pesan/doa donatur. Kas keluar: untuk apa dana dipakai, contoh: Santunan 10 anak yatim Dusun Krajan">{{ old('catatan', $infaq->catatan ?? '') }}</textarea>
</div>

<div id="keluar-fields" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="mb-4">
        <x-input-label for="kategori" value="Kategori Penggunaan (khusus kas keluar)" />
        <select id="kategori" name="kategori"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="">— Pilih kategori —</option>
            @foreach (\App\Models\Infaq::KATEGORI_KELUAR as $kat)
                <option value="{{ $kat }}"
                        {{ old('kategori', $infaq->kategori ?? '') === $kat ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_', ' ', $kat)) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <x-input-label for="penanggung_jawab" value="Penanggung Jawab" />
        <x-text-input id="penanggung_jawab" name="penanggung_jawab" type="text" class="mt-1 block w-full"
                      value="{{ old('penanggung_jawab', $infaq->penanggung_jawab ?? '') }}" placeholder="Contoh: Bendahara LAZISNU" />
    </div>
</div>

<div class="mb-4">
    <x-input-label for="bukti" value="Bukti / Kwitansi (foto, maks 2MB)" />
    <input id="bukti" name="bukti" type="file" accept="image/*"
           class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" />
    @if (!empty($infaq?->bukti_path))
        <div class="mt-2 flex items-center gap-3">
            <img src="{{ $infaq->bukti_url }}" alt="Bukti kas" class="h-20 w-20 rounded-lg object-cover border border-slate-200" />
            <a href="{{ $infaq->bukti_url }}" target="_blank" rel="noopener" class="text-xs font-bold text-blue-600 hover:underline">Lihat bukti</a>
        </div>
    @endif
</div>

<script>
    (function () {
        var arah = document.getElementById('arah');
        var keluarFields = document.getElementById('keluar-fields');
        if (!arah || !keluarFields) return;
        function toggle() {
            keluarFields.style.display = arah.value === 'keluar' ? '' : 'none';
        }
        arah.addEventListener('change', toggle);
        toggle();
    })();
</script>

@php($arah = 'masuk')
<input type="hidden" name="arah" value="masuk">

@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-4 p-4 rounded-xl border border-emerald-200 bg-emerald-50/70">
    <p class="text-sm font-bold text-emerald-900">Form Pemasukan (Kas Masuk)</p>
    <p class="text-xs text-emerald-700 mt-1">Catatan internal/pengurus. Data ini tidak ditampilkan sebagai daftar donatur publik.</p>
</div>

<div class="mb-4">
    <x-input-label for="nominal" value="Nominal Masuk (Rp)" />
    <x-text-input id="nominal" name="nominal" type="text" inputmode="numeric" class="mt-1 block w-full"
                  value="{{ old('nominal') !== null ? number_format((int) preg_replace('/\D/', '', (string) old('nominal')), 0, ',', '.') : (isset($infaq->nominal) ? number_format((int) $infaq->nominal, 0, ',', '.') : '') }}" placeholder="Contoh: 50.000" required autofocus
                  oninput="let v=this.value.replace(/[^0-9]/g,''); this.value=v ? v.replace(/\B(?=(\d{3})+(?!\d))/g,'.') : '';"
                  autocomplete="off" />
</div>
<script>
(function () {
    var nominal = document.getElementById('nominal');
    var form = nominal ? nominal.closest('form') : null;
    if (!nominal || !form) return;
    form.addEventListener('submit', function () {
        nominal.value = nominal.value.replace(/\./g, '');
    });
})();
</script>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="mb-4">
        <x-input-label for="nama_donatur" value="Sumber / Keterangan Nama (opsional)" />
        <x-text-input id="nama_donatur" name="nama_donatur" type="text" class="mt-1 block w-full"
                      value="{{ old('nama_donatur', $infaq->nama_donatur ?? '') }}" placeholder="Kosongkan jika tidak diisi" />
    </div>

    <div class="mb-4">
        <x-input-label for="no_hp" value="No. HP (opsional)" />
        <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full"
                      value="{{ old('no_hp', $infaq->no_hp ?? '') }}" placeholder="08xxxxxxxxxx" />
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="mb-4">
        <x-input-label for="metode_pembayaran" value="Metode" />
        <select id="metode_pembayaran" name="metode_pembayaran" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
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
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
            @foreach (\App\Models\Infaq::STATUSES as $item)
                <option value="{{ $item }}"
                        {{ old('status', $infaq->status ?? 'pending') === $item ? 'selected' : '' }}>
                    {{ ucfirst($item) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <x-input-label for="paid_at" value="Tanggal" />
        <x-text-input id="paid_at" name="paid_at" type="date" class="mt-1 block w-full"
                      max="{{ now()->toDateString() }}"
                      value="{{ old('paid_at', isset($infaq) && $infaq->paid_at ? $infaq->paid_at->format('Y-m-d') : '') }}"
                      onchange="if(this.value && this.value > this.max) this.value = this.max;" />
        <p class="text-xs text-gray-500 mt-1">Maksimal hari ini. Diisi otomatis saat status Lunas.</p>
    </div>
</div>

<div class="mb-4">
    <x-input-label for="catatan" value="Catatan Internal (opsional)" />
    <textarea id="catatan" name="catatan" rows="3"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
              placeholder="Catatan internal pengurus">{{ old('catatan', $infaq->catatan ?? '') }}</textarea>
</div>

<div class="mb-4">
    <x-input-label for="bukti" value="Bukti / Dokumen (foto, maks 2MB, opsional)" />
    <input id="bukti" name="bukti" type="file" accept="image/*"
           class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" />
    @if (!empty($infaq?->bukti_path))
        <div class="mt-2 flex items-center gap-3">
            <img src="{{ $infaq->bukti_url }}" alt="Bukti" class="h-20 w-20 rounded-lg object-cover border border-slate-200" />
            <a href="{{ $infaq->bukti_url }}" target="_blank" rel="noopener" class="text-xs font-bold text-blue-600 hover:underline">Lihat bukti</a>
        </div>
    @endif
</div>

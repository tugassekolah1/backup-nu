@php($arah = 'keluar')
<input type="hidden" name="arah" value="keluar">

@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php($saldoKasValue = (float) ($saldoKas ?? 0))
<div class="mb-4 p-4 rounded-xl border border-rose-200 bg-rose-50/70">
    <p class="text-sm font-bold text-rose-900">Form Pengeluaran (Kas Keluar)</p>
    <p class="text-xs text-rose-700 mt-1">Catat penyaluran/penggunaan dana dengan jelas. <span class="font-semibold">Semua kolom wajib diisi.</span></p>
    <p class="text-xs font-semibold text-slate-700 mt-2">Saldo kas tersedia: Rp {{ number_format($saldoKasValue, 0, ',', '.') }}</p>
</div>

<div class="mb-4">
    <x-input-label for="nominal" value="Nominal Keluar (Rp)" />
    <x-text-input id="nominal" name="nominal" type="text" inputmode="numeric" class="mt-1 block w-full"
                  value="{{ old('nominal') !== null ? number_format((int) preg_replace('/\D/', '', (string) old('nominal')), 0, ',', '.') : (isset($infaq->nominal) ? number_format((int) $infaq->nominal, 0, ',', '.') : '') }}" placeholder="Contoh: 50.000" required autofocus
                  oninput="let v=this.value.replace(/[^0-9]/g,''); this.value=v ? v.replace(/\B(?=(\d{3})+(?!\d))/g,'.') : '';"
                  autocomplete="off" />
    <p id="saldoWarning" class="mt-2 text-xs font-semibold text-amber-700 hidden"></p>
</div>

<script>
(function () {
    var saldo = {{ json_encode($saldoKasValue) }};
    var nominal = document.getElementById('nominal');
    var warning = document.getElementById('saldoWarning');
    var form = nominal ? nominal.closest('form') : null;
    if (!nominal || !form) return;

    function rawNominal() {
        return parseFloat((nominal.value || '0').replace(/\./g, '')) || 0;
    }

    function overBalance() {
        var value = rawNominal();
        return value > 0 && value > saldo;
    }

    function renderWarning() {
        if (!warning) return;
        if (overBalance()) {
            var value = rawNominal();
            var selisih = value - saldo;
            warning.textContent = 'Nominal melebihi saldo kas (selisih Rp ' + selisih.toLocaleString('id-ID') + '). Pengeluaran melebihi saldo tidak dapat disimpan.';
            warning.classList.remove('hidden');
        } else {
            warning.textContent = '';
            warning.classList.add('hidden');
        }
    }

    nominal.addEventListener('input', renderWarning);
    renderWarning();

    form.addEventListener('submit', function (e) {
        if (overBalance()) {
            var value = rawNominal();
            var selisih = value - saldo;
            e.preventDefault();
            if (warning) {
                warning.textContent = 'Nominal pengeluaran (Rp ' + value.toLocaleString('id-ID') + ') melebihi saldo kas tersedia (Rp ' + saldo.toLocaleString('id-ID') + '), selisih Rp ' + selisih.toLocaleString('id-ID') + '). Kurangi nominal atau catat pemasukan terlebih dahulu.';
                warning.classList.remove('hidden');
            }
            nominal.focus();
            alert('Nominal pengeluaran melebihi saldo kas tersedia. Pengeluaran tidak boleh membuat saldo minus.');
            return;
        }
        nominal.value = (nominal.value || '').replace(/\./g, '');
    });
})();
</script>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="mb-4">
        <x-input-label for="kategori" value="Kategori Penggunaan" />
        <select id="kategori" name="kategori" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-rose-500 focus:border-rose-500">
            <option value="" disabled {{ old('kategori', $infaq->kategori ?? '') ? '' : 'selected' }}>— Pilih kategori —</option>
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
                      value="{{ old('penanggung_jawab', $infaq->penanggung_jawab ?? '') }}" placeholder="Contoh: Bendahara LAZISNU" required />
    </div>
</div>

<div class="mb-4">
    <x-input-label for="catatan" value="Keperluan / Keterangan" />
    <textarea id="catatan" name="catatan" rows="3" required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-rose-500 focus:border-rose-500"
              placeholder="Contoh: Santunan 10 anak yatim Dusun Krajan">{{ old('catatan', $infaq->catatan ?? '') }}</textarea>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="mb-4">
        <x-input-label for="metode_pembayaran" value="Metode" />
        <select id="metode_pembayaran" name="metode_pembayaran" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-rose-500 focus:border-rose-500">
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
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-rose-500 focus:border-rose-500">
            @foreach (\App\Models\Infaq::STATUSES as $item)
                <option value="{{ $item }}"
                        {{ old('status', $infaq->status ?? 'lunas') === $item ? 'selected' : '' }}>
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
                      onchange="if(this.value && this.value > this.max) this.value = this.max;" required />
        <p class="text-xs text-gray-500 mt-1">Maksimal hari ini (wajib).</p>
    </div>
</div>

<div class="mb-4">
    <x-input-label for="bukti" value="Bukti / Kwitansi (foto, maks 2MB)" />
    <input id="bukti" name="bukti" type="file" accept="image/*"
           @if (empty($infaq?->bukti_path)) required @endif
           class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" />
    @if (!empty($infaq?->bukti_path))
        <div class="mt-2 flex items-center gap-3">
            <img src="{{ $infaq->bukti_url }}" alt="Bukti kas" class="h-20 w-20 rounded-lg object-cover border border-slate-200" />
            <a href="{{ $infaq->bukti_url }}" target="_blank" rel="noopener" class="text-xs font-bold text-blue-600 hover:underline">Lihat bukti</a>
            <span class="text-xs text-gray-500">Bukti sudah ada (unggah file baru hanya jika ingin mengganti).</span>
        </div>
    @endif
</div>

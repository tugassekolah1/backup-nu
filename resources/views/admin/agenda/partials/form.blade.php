@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!-- CDN CSS & JS Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
{{-- Leaflet (mode gratis, tanpa API key) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<div class="mb-4">
    <x-input-label for="title" value="Judul Agenda" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                  value="{{ old('title', $agenda->title ?? '') }}" required autofocus />
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div>
        <x-input-label for="event_date" value="Tanggal" />
        <x-text-input id="event_date" name="event_date" type="date" class="mt-1 block w-full"
                      min="{{ now()->toDateString() }}"
                      value="{{ old('event_date', isset($agenda) ? $agenda->event_date->format('Y-m-d') : '') }}" required />
        <p id="past-date-hint" class="mt-1.5 hidden items-start gap-1.5 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2" role="status">
            <svg class="w-4 h-4 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <span>Tanggal tidak boleh sebelum hari ini. Pilih hari ini atau tanggal mendatang.</span>
        </p>
    </div>
    <div>
    <x-input-label for="event_time" value="Waktu " />
    <x-text-input id="event_time" name="event_time" type="text" class="mt-1 block w-full bg-white"
                  placeholder="Pilih jam..."
                  value="{{ old('event_time', isset($agenda->event_time) ? \Carbon\Carbon::parse($agenda->event_time)->format('H:i') : '') }}" />
</div>

<script>
    flatpickr("#event_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true // <--- Memaksa format 24 Jam
    });
</script>
</div>

<div class="mb-4">
    <x-input-label for="category" value="Kategori" />
    <x-text-input id="category" name="category" type="text" class="mt-1 block w-full"
                  placeholder="Contoh: Pengajian, Rapat, Ziarah"
                  value="{{ old('category', $agenda->category ?? 'Kegiatan') }}" required />
</div>

{{-- ===== Lokasi + Google Maps picker ===== --}}
<div class="mb-4 rounded-xl border border-gray-200 bg-slate-50/60 p-4">
    <x-input-label for="location" value="Lokasi" />
    <x-text-input id="location" name="location" type="text" class="mt-1 block w-full"
                  placeholder="Contoh: Masjid Jami' Kecamatan Cilongok"
                  value="{{ old('location', $agenda->location ?? '') }}" />

    <div class="mt-3 flex flex-col sm:flex-row gap-2">
        <input id="maps-search" type="text" placeholder="Cari tempat… mis. Masjid Kecamatan Cilongok"
               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2" />
        <button type="button" id="btn-maps-search"
                class="px-4 py-2 text-sm font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 active:scale-95 transition">
            Cari
        </button>
        <button type="button" id="btn-my-location"
                class="px-4 py-2 text-sm font-semibold rounded-lg bg-white border border-gray-300 hover:bg-gray-100 active:scale-95 transition">
            Lokasi saya
        </button>
    </div>
    <div id="maps-results" class="mt-2 hidden divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white text-sm overflow-hidden"></div>

    <div id="maps-picker" class="mt-3 h-64 w-full rounded-lg border border-gray-300 overflow-hidden z-0"></div>
    <p class="mt-1.5 text-xs text-gray-500">
        Klik peta atau geser pin untuk memilih titik lokasi. Koordinat tersimpan otomatis dan halaman publik akan menampilkan tombol “Buka di Google Maps”.
        @if(config('services.google.maps_key'))
            Mode: Google Maps API.
        @else
            Mode: peta gratis (OpenStreetMap). Isi <code>GOOGLE_MAPS_API_KEY</code> di <code>.env</code> untuk mengaktifkan autocomplete Google Places.
        @endif
    </p>

    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <x-input-label for="latitude" value="Latitude" />
            <x-text-input id="latitude" name="latitude" type="text" inputmode="decimal" class="mt-1 block w-full font-mono text-sm"
                          placeholder="-7.3805000"
                          value="{{ old('latitude', $agenda->latitude ?? '') }}" />
        </div>
        <div>
            <x-input-label for="longitude" value="Longitude" />
            <x-text-input id="longitude" name="longitude" type="text" inputmode="decimal" class="mt-1 block w-full font-mono text-sm"
                          placeholder="109.1240000"
                          value="{{ old('longitude', $agenda->longitude ?? '') }}" />
        </div>
    </div>

    <div class="mt-3">
        <x-input-label for="maps_url" value="Link Google Maps (opsional — tempel share link bila ada)" />
        <x-text-input id="maps_url" name="maps_url" type="url" class="mt-1 block w-full font-mono text-sm"
                      placeholder="https://maps.google.com/?q=… atau https://goo.gl/maps/…"
                      value="{{ old('maps_url', $agenda->maps_url ?? '') }}" />
        <a id="maps-preview-link" href="#" target="_blank" rel="noopener"
           class="mt-2 hidden items-center gap-1.5 text-sm font-semibold text-blue-700 hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Pratinjau: buka di Google Maps</span>
        </a>
    </div>
</div>

<div class="mb-4">
    <x-input-label for="description" value="Keterangan (opsional)" />
    <textarea id="description" name="description" rows="3"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $agenda->description ?? '') }}</textarea>
</div>

<script>
    // Validasi tanggal agenda: tidak boleh sebelum hari ini (min = hari ini).
    (function () {
        const dateInput = document.getElementById('event_date');
        const hint = document.getElementById('past-date-hint');

        function formatId(value) {
            const date = new Date(value + 'T00:00:00');
            if (isNaN(date)) return value;
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        }

        function isPast(value) {
            if (!value) return false;
            const picked = new Date(value + 'T00:00:00');
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            return picked < today;
        }

        function syncHint() {
            if (!dateInput || !hint) return;
            const show = isPast(dateInput.value);
            hint.classList.toggle('hidden', !show);
            hint.classList.toggle('flex', show);
        }

        if (dateInput && hint) {
            dateInput.addEventListener('change', syncHint);
            syncHint();
        }

        const form = dateInput ? dateInput.closest('form') : null;
        if (form) {
            form.addEventListener('submit', function (event) {
                if (!isPast(dateInput.value)) return;
                event.preventDefault();
                syncHint();
                window.alert(
                    'Tanggal acara ' + formatId(dateInput.value) + ' sudah lewat.\n\n' +
                    'Agenda hanya boleh dibuat untuk hari ini atau tanggal mendatang.'
                );
                dateInput.focus();
                dateInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }
    })();
</script>

<script>
(function () {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const locInput = document.getElementById('location');
    const urlInput = document.getElementById('maps_url');
    const searchInput = document.getElementById('maps-search');
    const resultsBox = document.getElementById('maps-results');
    const previewLink = document.getElementById('maps-preview-link');
    const googleKey = @json(config('services.google.maps_key'));

    // Default: Kecamatan Cilongok, Banyumas
    const DEFAULT_LAT = -7.3812;
    const DEFAULT_LNG = 109.1239;

    function num(v, fallback) {
        const n = parseFloat(String(v ?? '').replace(',', '.'));
        return Number.isFinite(n) ? n : fallback;
    }

    function currentLatLng() {
        return {
            lat: num(latInput.value, DEFAULT_LAT),
            lng: num(lngInput.value, DEFAULT_LNG),
        };
    }

    function syncPreview() {
        let href = (urlInput.value || '').trim();
        if (!href) {
            const lat = latInput.value.trim(), lng = lngInput.value.trim();
            if (lat && lng) href = 'https://www.google.com/maps/search/?api=1&query=' + lat + ',' + lng;
            else if (locInput.value.trim()) href = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(locInput.value.trim());
        }
        if (href) {
            previewLink.href = href;
            previewLink.classList.remove('hidden');
            previewLink.classList.add('inline-flex');
        } else {
            previewLink.classList.add('hidden');
            previewLink.classList.remove('inline-flex');
        }
    }

    function setLatLng(lat, lng, opts = {}) {
        latInput.value = Number(lat).toFixed(7);
        lngInput.value = Number(lng).toFixed(7);
        if (opts.clearUrl) urlInput.value = '';
        syncPreview();
    }

    [latInput, lngInput, locInput, urlInput].forEach(el => el && el.addEventListener('input', syncPreview));

    // ---------- Mode Google Maps (jika ada API key) ----------
    if (googleKey) {
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(googleKey) + '&libraries=places&callback=initAgendaMapsPicker';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);

        window.initAgendaMapsPicker = function () {
            const pos = currentLatLng();
            const map = new google.maps.Map(document.getElementById('maps-picker'), {
                center: pos, zoom: 15,
            });
            const marker = new google.maps.Marker({ position: pos, map, draggable: true });

            const autocomplete = new google.maps.places.Autocomplete(locInput, { componentRestrictions: { country: 'id' } });
            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (place.geometry && place.geometry.location) {
                    const lat = place.geometry.location.lat(), lng = place.geometry.location.lng();
                    map.setCenter({ lat, lng });
                    map.setZoom(17);
                    marker.setPosition({ lat, lng });
                    setLatLng(lat, lng, { clearUrl: true });
                    if (place.name && !locInput.value) locInput.value = place.name;
                    syncPreview();
                }
            });

            marker.addListener('dragend', () => {
                const p = marker.getPosition();
                setLatLng(p.lat(), p.lng(), { clearUrl: true });
            });
            map.addListener('click', (e) => {
                marker.setPosition(e.latLng);
                setLatLng(e.latLng.lat(), e.latLng.lng(), { clearUrl: true });
            });

            wireButtons({
                onPick(lat, lng, label) {
                    map.setCenter({ lat, lng });
                    map.setZoom(17);
                    marker.setPosition({ lat, lng });
                    setLatLng(lat, lng, { clearUrl: true });
                    if (label && !locInput.value.trim()) { locInput.value = label; syncPreview(); }
                },
                geocodeSearch(query, cb) {
                    const geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ address: query, componentRestrictions: { country: 'ID' } }, (results, status) => {
                        if (status === 'OK' && results.length) {
                            cb(results.slice(0, 5).map(r => ({
                                label: r.formatted_address,
                                lat: r.geometry.location.lat(),
                                lng: r.geometry.location.lng(),
                            })));
                        } else cb([]);
                    });
                }
            });
        };
        syncPreview();
        return;
    }

    // ---------- Mode gratis: Leaflet + Nominatim ----------
    const pos = currentLatLng();
    const map = L.map('maps-picker').setView([pos.lat, pos.lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);
    const marker = L.marker([pos.lat, pos.lng], { draggable: true }).addTo(map);

    marker.on('dragend', () => {
        const p = marker.getLatLng();
        map.panTo(p);
        setLatLng(p.lat, p.lng, { clearUrl: true });
        reverseFill(p.lat, p.lng);
    });
    map.on('click', (e) => {
        marker.setLatLng(e.latlng);
        setLatLng(e.latlng.lat, e.latlng.lng, { clearUrl: true });
        reverseFill(e.latlng.lat, e.latlng.lng);
    });
    [latInput, lngInput].forEach(el => el.addEventListener('change', () => {
        const p = currentLatLng();
        marker.setLatLng([p.lat, p.lng]);
        map.panTo([p.lat, p.lng]);
    }));

    async function nominatimSearch(query) {
        const url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=5&countrycodes=id'
            + '&viewbox=108.7,-7.7,109.7,-7.1&bounded=0&q=' + encodeURIComponent(query);
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return [];
        const data = await res.json();
        return data.map(d => ({ label: d.display_name, lat: parseFloat(d.lat), lng: parseFloat(d.lon) }));
    }

    async function reverseFill(lat, lng) {
        // Isi nama lokasi otomatis hanya bila kolom lokasi masih kosong
        if (locInput.value.trim()) return;
        try {
            const res = await fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (data && data.display_name) {
                locInput.value = data.display_name.split(',').slice(0, 3).join(',').trim();
                syncPreview();
            }
        } catch (e) { /* abaikan */ }
    }

    function renderResults(items, onPick) {
        resultsBox.innerHTML = '';
        if (!items.length) {
            resultsBox.innerHTML = '<div class="px-3 py-2.5 text-gray-500">Tidak ditemukan. Coba kata kunci lain atau klik peta manual.</div>';
        } else {
            items.forEach(item => {
                const b = document.createElement('button');
                b.type = 'button';
                b.className = 'w-full text-left px-3 py-2.5 hover:bg-slate-50 transition';
                b.textContent = item.label;
                b.addEventListener('click', () => {
                    onPick(item.lat, item.lng, item.label);
                    resultsBox.classList.add('hidden');
                });
                resultsBox.appendChild(b);
            });
        }
        resultsBox.classList.remove('hidden');
    }

    function wireButtons(impl) {
        const btnSearch = document.getElementById('btn-maps-search');
        const btnMine = document.getElementById('btn-my-location');

        async function doSearch() {
            const q = (searchInput.value || locInput.value || '').trim();
            if (!q) { searchInput.focus(); return; }
            btnSearch.disabled = true;
            btnSearch.textContent = 'Mencari…';
            try {
                let items;
                if (impl && impl.geocodeSearch) {
                    items = await new Promise(resolve => impl.geocodeSearch(q, resolve));
                } else {
                    items = await nominatimSearch(q);
                }
                const pick = (impl && impl.onPick) || function (lat, lng, label) {
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 17);
                    setLatLng(lat, lng, { clearUrl: true });
                    if (label) {
                        locInput.value = label.split(',').slice(0, 3).join(',').trim();
                        syncPreview();
                    }
                };
                renderResults(items, pick);
            } finally {
                btnSearch.disabled = false;
                btnSearch.textContent = 'Cari';
            }
        }

        btnSearch.addEventListener('click', doSearch);
        searchInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); doSearch(); } });

        btnMine.addEventListener('click', () => {
            if (!navigator.geolocation) { alert('Perangkat tidak mendukung geolokasi.'); return; }
            btnMine.disabled = true;
            btnMine.textContent = 'Mendeteksi…';
            navigator.geolocation.getCurrentPosition((posGeo) => {
                const lat = posGeo.coords.latitude, lng = posGeo.coords.longitude;
                if (impl && impl.onPick) impl.onPick(lat, lng, null);
                else {
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 17);
                    setLatLng(lat, lng, { clearUrl: true });
                    reverseFill(lat, lng);
                }
                btnMine.disabled = false;
                btnMine.textContent = 'Lokasi saya';
            }, () => {
                alert('Gagal mendeteksi lokasi. Pastikan izin lokasi diizinkan.');
                btnMine.disabled = false;
                btnMine.textContent = 'Lokasi saya';
            }, { enableHighAccuracy: true, timeout: 10000 });
        });
    }

    wireButtons(null);
    setTimeout(() => map.invalidateSize(), 300);
    syncPreview();
})();
</script>

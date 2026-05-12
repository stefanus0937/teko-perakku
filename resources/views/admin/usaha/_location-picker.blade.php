{{--
    Leaflet Location Picker (reusable: admin create-usaha + edit-usaha + UMKM edit-profile)
    ======================================================================================

    Pemakaian:
        @include('admin.usaha._location-picker', [
            'lat' => $usaha->latitude ?? null,
            'lng' => $usaha->longitude ?? null,
        ])

    Behavior (sinkron dua arah):
      - User klik di peta            → marker pindah, dua input lat/lng terisi otomatis
      - User drag marker             → coords ikut update
      - User ketik di input lat/lng  → marker pindah ke koordinat baru, peta re-center
      - Tombol "Hapus Lokasi"        → marker hilang, kedua input dikosongkan
      - Saat form submit, input lat & lng (name="latitude"/"longitude") yang dikirim ke server

    Center default kalau belum ada koordinat: Kotagede, Yogyakarta.

    Dependency: Leaflet 1.9.4 dari unpkg (CDN). @once memastikan asset hanya di-load 1x.
--}}
@php
    $lat = $lat ?? null;
    $lng = $lng ?? null;
    $hasCoords = is_numeric($lat) && is_numeric($lng);
@endphp

@once
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="">
    <style>
        .location-picker {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 12px;
        }
        .location-picker__map {
            width: 100%;
            height: 280px;
            background: #f4f4f5;
        }
        .location-picker__map .leaflet-container {
            width: 100%;
            height: 100%;
            font-family: inherit;
            cursor: crosshair;
        }
        .location-picker__footer {
            padding: 12px 14px;
            border-top: 1px solid #f3f4f6;
            display: flex;
            justify-content: flex-end;
        }
        .location-picker__clear {
            background: transparent;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 12.5px;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            transition: background .15s, border-color .15s, color .15s;
        }
        .location-picker__clear:hover:not(:disabled) {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .location-picker__clear:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .location-picker__inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 8px;
        }
        .location-picker__inputs label {
            font-size: 12.5px;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 4px;
            display: block;
        }
        .location-picker__hint {
            font-size: 12.5px;
            color: #9ca3af;
            margin: 6px 0 0;
        }
        .location-picker__input.is-invalid {
            border-color: #f87171 !important;
            box-shadow: 0 0 0 2px rgba(248, 113, 113, 0.15);
        }
        @media (max-width: 575.98px) {
            .location-picker__inputs { grid-template-columns: 1fr; }
        }
    </style>
@endonce

<div class="form-group">
    <label>Lokasi Usaha</label>
    <div class="location-picker">
        <div class="location-picker__map"
             id="usaha-location-picker"
             data-initial-lat="{{ $hasCoords ? $lat : '' }}"
             data-initial-lng="{{ $hasCoords ? $lng : '' }}"></div>
        <div class="location-picker__footer">
            <button type="button"
                    class="location-picker__clear"
                    id="usaha-location-clear"
                    {{ $hasCoords ? '' : 'disabled' }}>
                Hapus Lokasi
            </button>
        </div>
    </div>

    <div class="location-picker__inputs">
        <div>
            <label for="usaha-location-lat">Latitude</label>
            <input type="text"
                   inputmode="decimal"
                   class="form-input location-picker__input"
                   name="latitude"
                   id="usaha-location-lat"
                   value="{{ old('latitude',  $hasCoords ? $lat : '') }}"
                   placeholder="Contoh: -7.8275">
        </div>
        <div>
            <label for="usaha-location-lng">Longitude</label>
            <input type="text"
                   inputmode="decimal"
                   class="form-input location-picker__input"
                   name="longitude"
                   id="usaha-location-lng"
                   value="{{ old('longitude', $hasCoords ? $lng : '') }}"
                   placeholder="Contoh: 110.3990">
        </div>
    </div>

    <p class="location-picker__hint">
        Klik di peta untuk menempatkan marker (atau drag marker untuk menggesernya).
        Anda juga bisa mengetik koordinat langsung di kolom Latitude/Longitude.
    </p>
</div>

@once
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('usaha-location-picker');
    if (!el || typeof L === 'undefined') return;

    var latInput = document.getElementById('usaha-location-lat');
    var lngInput = document.getElementById('usaha-location-lng');
    var clearBtn = document.getElementById('usaha-location-clear');

    // Center default: Kotagede, Yogyakarta
    var DEFAULT_CENTER = [-7.8275, 110.3990];
    var DEFAULT_ZOOM   = 14;

    var initialLat = parseFloat(el.dataset.initialLat);
    var initialLng = parseFloat(el.dataset.initialLng);
    var hasInitial = isFinite(initialLat) && isFinite(initialLng);

    var map = L.map(el, {
        center: hasInitial ? [initialLat, initialLng] : DEFAULT_CENTER,
        zoom:   hasInitial ? 16 : DEFAULT_ZOOM,
        scrollWheelZoom: true
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var marker = null;
    // Flag agar update marker → update input tidak men-trigger ulang
    // event 'input' yang lalu update marker lagi (infinite loop).
    var suspendInputEvents = false;

    function isValidLat(v) { return isFinite(v) && v >= -90  && v <=  90; }
    function isValidLng(v) { return isFinite(v) && v >= -180 && v <= 180; }

    function setValidity(input, ok) {
        input.classList.toggle('is-invalid', !ok);
    }

    function placeMarker(lat, lng, opts) {
        opts = opts || {};
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function () {
                var ll = marker.getLatLng();
                syncInputsFromMarker(ll.lat, ll.lng);
            });
        }
        if (opts.recenter) {
            map.setView([lat, lng], Math.max(map.getZoom(), 15));
        }
        clearBtn.disabled = false;
    }

    function syncInputsFromMarker(lat, lng) {
        suspendInputEvents = true;
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        setValidity(latInput, true);
        setValidity(lngInput, true);
        suspendInputEvents = false;
    }

    function syncMarkerFromInputs(opts) {
        if (suspendInputEvents) return;
        var lat = parseFloat(latInput.value);
        var lng = parseFloat(lngInput.value);
        var latOk = isValidLat(lat);
        var lngOk = isValidLng(lng);

        setValidity(latInput, latInput.value === '' || latOk);
        setValidity(lngInput, lngInput.value === '' || lngOk);

        // Kalau kedua input kosong, anggap user mau clear
        if (latInput.value === '' && lngInput.value === '') {
            if (marker) { map.removeLayer(marker); marker = null; }
            clearBtn.disabled = true;
            return;
        }

        if (latOk && lngOk) {
            placeMarker(lat, lng, opts || { recenter: true });
        }
    }

    function clearAll() {
        suspendInputEvents = true;
        if (marker) { map.removeLayer(marker); marker = null; }
        latInput.value = '';
        lngInput.value = '';
        setValidity(latInput, true);
        setValidity(lngInput, true);
        clearBtn.disabled = true;
        suspendInputEvents = false;
    }

    // ── Wire-up ─────────────────────────────────────
    if (hasInitial) {
        placeMarker(initialLat, initialLng);
    }

    map.on('click', function (e) {
        placeMarker(e.latlng.lat, e.latlng.lng);
        syncInputsFromMarker(e.latlng.lat, e.latlng.lng);
    });

    // Input bisa diketik (input event) atau di-paste (change event).
    // Debounce ringan: re-center hanya kalau user berhenti mengetik ~300ms.
    var inputDebounce = null;
    function onCoordInput() {
        if (inputDebounce) clearTimeout(inputDebounce);
        inputDebounce = setTimeout(function () {
            syncMarkerFromInputs({ recenter: true });
        }, 300);
    }
    latInput.addEventListener('input',  onCoordInput);
    lngInput.addEventListener('input',  onCoordInput);
    latInput.addEventListener('change', function () { syncMarkerFromInputs({ recenter: true }); });
    lngInput.addEventListener('change', function () { syncMarkerFromInputs({ recenter: true }); });

    clearBtn.addEventListener('click', clearAll);

    // Leaflet salah hitung ukuran kalau container hidden saat init
    // (mis. tab/accordion). Force redraw setelah layout settle.
    setTimeout(function () { map.invalidateSize(); }, 200);
});
</script>
@endonce

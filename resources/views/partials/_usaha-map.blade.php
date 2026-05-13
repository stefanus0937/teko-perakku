{{--
    Reusable Multi-Marker Usaha Map (Leaflet)
    =========================================
    Render peta dengan semua marker usaha. Marker untuk usaha yang sedang
    dibuka di-highlight beda (warna primary), marker lain pakai warna netral.
    Tiap marker punya popup card: foto + nama + deskripsi singkat + tombol
    "Kunjungi Toko".

    Pemakaian:
        @include('partials._usaha-map', [
            'usahas'         => $usahasWithLocation, // collection: id, nama_usaha, latitude, longitude, foto_usaha, deskripsi_usaha, user.username
            'currentUsahaId' => $usaha->id ?? null,  // opsional — id usaha yang di-highlight
            'mapId'          => 'usaha-map-home',    // opsional — wajib unik kalau ada >1 map di halaman
            'height'         => '420px',             // opsional
            'title'          => 'Peta Toko Perak',   // opsional — kalau diisi, ditampilkan sebagai card heading
            'subtitle'       => '...',               // opsional
        ])

    Asumsi:
      - Setiap usaha sudah punya latitude+longitude valid (filter di controller).
      - Setiap usaha sudah eager-loaded relasi `user` (untuk @username).
      - Dependency: Leaflet 1.9.4 dari unpkg.
--}}
@php
    $mapId          = $mapId ?? 'usaha-map-'.uniqid();
    $currentUsahaId = $currentUsahaId ?? null;
    $height         = $height ?? '420px';
    $title          = $title ?? null;
    $subtitle       = $subtitle ?? null;

    /*
    |--------------------------------------------------------------------------
    | Marker Manual Admin / Kantor
    |--------------------------------------------------------------------------
    */
    $adminMarker = [
        'id'        => 'admin-office',
        'name'      => 'Kantor TekoPerakku',
        'username'  => 'Admin',
        'lat'       => -7.8177408,
        'lng'       => 110.3961321,
        'photo'     => asset('assets/images/logo.png'),
        'desc'      => 'Pusat informasi & administrasi TekoPerakku.',
        'detailUrl' => route('guest-about'),
        'isCurrent' => false,
        'isAdmin'   => true,
    ];

    $usahaPayload = collect($usahas ?? [])
        ->filter(fn ($u) => is_numeric($u->latitude) && is_numeric($u->longitude))
        ->map(function ($u) use ($currentUsahaId) {

            $foto = $u->foto_usaha
                ? asset('storage/'.$u->foto_usaha)
                : asset('assets/images/kategori-default.jpg');

            $desc = $u->deskripsi_usaha
                ? \Illuminate\Support\Str::limit($u->deskripsi_usaha, 90)
                : 'Kerajinan perak asli Kotagede.';

            return [
                'id'        => $u->id,
                'name'      => $u->nama_usaha,
                'username'  => optional($u->user)->username,
                'lat'       => (float) $u->latitude,
                'lng'       => (float) $u->longitude,
                'photo'     => $foto,
                'desc'      => $desc,
                'detailUrl' => route('guest-detail-usaha', $u->id),
                'isCurrent' => $currentUsahaId !== null && (int) $u->id === (int) $currentUsahaId,
                'isAdmin'   => false,
            ];
        })

        // Tambahkan marker admin ke collection
        ->push($adminMarker)

        ->values();
@endphp

@once
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="">
    <style>
        /* ── Card shell ───────────────────────────────────── */
        .usaha-map-explore {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
        }
        .usaha-map-explore__header {
            padding: 18px 22px 14px;
        }
        .usaha-map-explore__title {
            margin: 0 0 4px;
            font-size: 18px;
            font-weight: 700;
            color: #18181b;
        }
        .usaha-map-explore__subtitle {
            margin: 0;
            font-size: 13.5px;
            color: #6b7280;
        }
        .usaha-map-explore__canvas {
            width: 100%;
            background: #f4f4f5;
        }
        .usaha-map-explore__canvas .leaflet-container {
            width: 100%;
            height: 100%;
            font-family: inherit;
        }
        .usaha-map-explore__empty {
            padding: 40px 22px;
            text-align: center;
            color: #9ca3af;
            font-size: 14px;
        }
        .usaha-map-explore__empty i {
            font-size: 32px;
            color: #d4d4d8;
            margin-bottom: 10px;
            display: block;
        }

        /* ── Custom marker (pin shape, DivIcon-based) ─────── */
        .usaha-pin {
            width: 24px;
            height: 24px;
            position: relative;
        }
        .usaha-pin__body {
            position: absolute;
            top: 0;
            left: 0;
            width: 24px;
            height: 24px;
            background: #ffffff;
            border: 2px solid #71717a;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            box-shadow: 0 3px 8px rgba(0,0,0,0.18);
        }
        .usaha-pin__icon {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            color: #71717a;
            font-size: 10px;
            pointer-events: none;
        }
        .usaha-pin--current .usaha-pin__body {
            background: #980808;
            border-color: #ffffff;
            box-shadow: 0 4px 12px rgba(152, 8, 8, 0.45);
        }
        .usaha-pin--current .usaha-pin__icon { color: #ffffff; }
        .usaha-pin--current::after {
            /* halo pulse halus utk current */
            content: '';
            position: absolute;
            top: -4px; left: -4px;
            width: 40px; height: 40px;
            border-radius: 50%;
            background: rgba(152, 8, 8, 0.20);
            animation: usaha-pin-pulse 1.8s ease-out infinite;
            z-index: -1;
        }


        @keyframes usaha-pin-pulse {
            0%   { transform: scale(0.7); opacity: 0.8; }
            100% { transform: scale(1.4); opacity: 0;   }
        }

        /* ── Popup card ───────────────────────────────────── */
        .leaflet-popup-content-wrapper:has(.usaha-popup) {
            padding: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        .leaflet-popup-content:has(.usaha-popup) {
            margin: 0;
            width: 240px !important;
        }
        .usaha-popup {
            font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
        }
        .usaha-popup__photo {
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: #f4f4f5;
        }
        .usaha-popup__photo img {
            width: 100%; height: 100%;
            object-fit: cover; display: block;
        }
        .usaha-popup__body {
            padding: 12px 14px 14px;
        }
        .usaha-popup__name {
            margin: 0 0 2px;
            font-size: 14.5px;
            font-weight: 700;
            color: #18181b;
            line-height: 1.3;
        }
        .usaha-popup__handle {
            font-size: 12px;
            color: #6b7280;
            margin: 0 0 8px;
            display: block;
        }
        .usaha-popup__desc {
            margin: 0 0 12px;
            font-size: 12.5px;
            line-height: 1.45;
            color: #4b5563;
        }
        .usaha-popup__cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 8px 12px;
            border-radius: 8px;
            background: #980808;
            color: #ffffff !important;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            transition: background .15s;
        }
        .usaha-popup__cta:hover { background: #7a0606; color: #ffffff !important; }
        .usaha-popup__badge {
            display: inline-block;
            margin-left: 6px;
            padding: 2px 8px;
            font-size: 10.5px;
            font-weight: 700;
            border-radius: 999px;
            background: rgba(152, 8, 8, 0.10);
            color: #980808;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            vertical-align: middle;
        }

        /* Responsive: peta lebih pendek di mobile */
        @media (max-width: 575.98px) {
            .usaha-map-explore__header { padding: 14px 16px 10px; }
            .usaha-map-explore__title { font-size: 16px; }
        }

        /* ── Dark mode ────────────────────────────────────── */
        body.dark-mode .usaha-map-explore {
            background: var(--tp-bg-surface, #1f2937);
            border-color: var(--tp-border, #374151);
            box-shadow: 0 4px 18px rgba(0,0,0,0.35);
        }
        body.dark-mode .usaha-map-explore__title    { color: var(--tp-text-strong, #f9fafb); }
        body.dark-mode .usaha-map-explore__subtitle { color: var(--tp-text-muted, #cbd5e1); }
        body.dark-mode .usaha-map-explore__canvas,
        body.dark-mode .usaha-map-explore__canvas .leaflet-container { background: #111827; }
        body.dark-mode .leaflet-popup-content-wrapper,
        body.dark-mode .leaflet-popup-tip { background: #111827; color: #f9fafb; }
        body.dark-mode .usaha-popup__name { color: #f9fafb; }
        body.dark-mode .usaha-popup__handle,
        body.dark-mode .usaha-popup__desc { color: #cbd5e1; }
        body.dark-mode .usaha-pin__body {
            background: #1f2937;
            border-color: #9ca3af;
        }
        body.dark-mode .usaha-pin__icon  { color: #d1d5db; }
        body.dark-mode .usaha-pin--current .usaha-pin__body { background: #b91c1c; border-color: #f9fafb; }
        body.dark-mode .usaha-pin--current .usaha-pin__icon  { color: #ffffff; }

        .usaha-pin--admin .usaha-pin__body {
            background: #eb2525;
            border-color: #ffffff;
            box-shadow: 0 4px 14px rgba(235, 37, 37, 0.4);
        }

        .usaha-pin--admin .usaha-pin__icon {
            color: #ffffff;
        }

        .usaha-pin--admin {
            width: 42px;
            height: 42px;
        }

        .usaha-pin--admin .usaha-pin__body {
            width: 42px;
            height: 42px;
            background: #eb2525;
            border-color: #ffffff;
            box-shadow: 0 4px 14px rgba(235, 37, 37, 0.4);
        }

        .usaha-pin--admin .usaha-pin__icon {
            color: #ffffff;
            font-size: 18px;
        }
    </style>
@endonce

<div class="usaha-map-explore">
    @if ($title || $subtitle)
        <div class="usaha-map-explore__header">
            @if ($title)    <h3 class="usaha-map-explore__title">{{ $title }}</h3> @endif
            @if ($subtitle) <p class="usaha-map-explore__subtitle">{{ $subtitle }}</p> @endif
        </div>
    @endif

    @if ($usahaPayload->isEmpty())
        <div class="usaha-map-explore__empty">
            <i class="fa-solid fa-map-location-dot"></i>
            Belum ada toko yang mendaftarkan lokasinya.
        </div>
    @else
        <div class="usaha-map-explore__canvas"
             id="{{ $mapId }}"
             style="height: {{ $height }};"
             data-payload-id="{{ $mapId }}-data"></div>
        {{-- Payload markers — JSON aman, tidak ada interpolasi langsung di JS string --}}
        <script type="application/json" id="{{ $mapId }}-data">{!! $usahaPayload->toJson(JSON_UNESCAPED_SLASHES) !!}</script>
    @endif
</div>

@once
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script>
window.TPUsahaMap = window.TPUsahaMap || (function () {
    'use strict';

    function escapeHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function buildPin(isCurrent, isAdmin) {

    // Marker admin / kantor
    if (isAdmin) {
        return L.divIcon({
            className: '',
            html:
                '<div class="usaha-pin usaha-pin--admin">'
                + '<span class="usaha-pin__body"></span>'
                + '<i class="usaha-pin__icon fa-solid fa-landmark"></i>'
                + '</div>',
            iconSize: [42, 42],
            iconAnchor: [21, 42],
            popupAnchor: [0, -38]
        });
    }

    // Marker toko biasa
    return L.divIcon({
        className: '',
        html:
            '<div class="usaha-pin ' + (isCurrent ? 'usaha-pin--current' : '') + '">'
            + '<span class="usaha-pin__body"></span>'
            + '<i class="usaha-pin__icon fa-solid fa-store"></i>'
            + '</div>',
        iconSize: [24, 24],
        iconAnchor: [12, 24],
        popupAnchor:[0, -22]
    });
}
    function buildPopupHtml(u) {
        var badge = u.isCurrent ? '<span class="usaha-popup__badge">Toko ini</span>' : '';
        var handle = u.username ? '@' + escapeHtml(u.username) : '';
        return '<div class="usaha-popup">'
            +   '<div class="usaha-popup__photo">'
            +     '<img src="' + escapeHtml(u.photo) + '" alt="' + escapeHtml(u.name) + '" '
            +          'onerror="this.onerror=null;this.src=\'' + escapeHtml(u.photo) + '\';">'
            +   '</div>'
            +   '<div class="usaha-popup__body">'
            +     '<h4 class="usaha-popup__name">' + escapeHtml(u.name) + badge + '</h4>'
            +     (handle ? '<span class="usaha-popup__handle">' + handle + '</span>' : '')
            +     '<p class="usaha-popup__desc">' + escapeHtml(u.desc) + '</p>'
            +     (u.isCurrent
                    ? ''
                    : '<a href="' + escapeHtml(u.detailUrl) + '" class="usaha-popup__cta">'
                        + '<i class="fa-solid fa-arrow-right"></i> Kunjungi Toko</a>')
            +   '</div>'
            + '</div>';
    }

    function init(mapId) {
        var el = document.getElementById(mapId);
        if (!el || typeof L === 'undefined') return;
        var data = document.getElementById(el.dataset.payloadId);
        if (!data) return;
        var usahas;
        try { usahas = JSON.parse(data.textContent || '[]'); }
        catch (e) { return; }
        if (!Array.isArray(usahas) || usahas.length === 0) return;

        // Compute initial bounds: gabung semua marker
        var latlngs = usahas.map(function (u) { return [u.lat, u.lng]; });
        var current = usahas.filter(function (u) { return u.isCurrent; })[0];

        var map = L.map(el, {
            scrollWheelZoom: false  // jangan culik scroll halaman
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // Render markers — current marker disimpan dulu agar bisa di-openPopup
        var currentMarker = null;
        const adminIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
            iconSize: [55, 55],
            iconAnchor: [21, 55],
            popupAnchor: [0, -40]
        });
        usahas.forEach(function (u) {
            var m = L.marker([u.lat, u.lng], {
                icon: buildPin(u.isCurrent, u.isAdmin)
            })
                .addTo(map)
                .bindPopup(buildPopupHtml(u), {
                    closeButton: true,
                    maxWidth: 260,
                    minWidth: 240
                });
            if (u.isCurrent) currentMarker = m;
        });

        // View: kalau ada current, center ke dia tapi tetap fit-bounds halus.
        // Kalau tidak, fit ke semua marker.
        var admin = usahas.find(function(u) {
            return u.isAdmin;
        });

        if (admin) {
            map.setView([admin.lat, admin.lng], 15);
        } else if (latlngs.length === 1) {
            map.setView(latlngs[0], 15);
        } else {
            map.fitBounds(latlngs, {
                padding: [30, 30],
                maxZoom: 16
            });
        }
        if (currentMarker) currentMarker.openPopup();

        // Aktifkan scroll-zoom hanya saat user klik peta (UX standar)
        map.on('click',    function () { map.scrollWheelZoom.enable(); });
        map.on('mouseout', function () { map.scrollWheelZoom.disable(); });

        // Force redraw jika container baru visible setelah init
        setTimeout(function () { map.invalidateSize(); }, 200);
    }

    // Auto-init semua peta yang ada di halaman
    function autoInit() {
        document.querySelectorAll('.usaha-map-explore__canvas[id]').forEach(function (el) {
            init(el.id);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoInit);
    } else {
        autoInit();
    }

    return { init: init };
}());
</script>
@endonce

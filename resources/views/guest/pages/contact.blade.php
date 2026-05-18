@extends('guest.layouts.main')
@section('title', 'Kontak Kami')

@push('styles')
{{-- Leaflet CSS untuk peta kantor di section "Hubungi Kami" --}}
<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="">
<style>
    .contact-page { color: #18181b; }
    .contact-hero {
        padding: 60px 0 30px;
        text-align: center;
    }
    .contact-hero__title {
        font-size: 36px;
        font-weight: 700;
        margin: 0 0 14px;
        color: #18181b;
        letter-spacing: -0.5px;
    }
    .contact-hero__lede {
        max-width: 720px;
        margin: 0 auto;
        font-size: 15px;
        line-height: 1.75;
        color: #4b5563;
    }

    .contact-section {
        padding: 30px 0 60px;
    }

    .contact-map-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
        position: relative;
    }
    .contact-map-card__canvas {
        width: 100%;
        aspect-ratio: 4 / 3;
        background: #f4f4f5;
    }
    .contact-map-card__canvas .leaflet-container {
        width: 100%;
        height: 100%;
        font-family: inherit;
    }
    .contact-map-card__caption {
        padding: 14px 18px;
        font-size: 13.5px;
        color: #6b7280;
        border-top: 1px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .contact-map-card__caption a {
        color: #980808;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
    }
    .contact-map-card__caption a:hover { text-decoration: underline; }

    /* Marker pin custom — match style dari _usaha-map.blade.php */
    .contact-pin {
        width: 32px;
        height: 32px;
        position: relative;
    }
    .contact-pin__body {
        position: absolute;
        top: 0; left: 0;
        width: 32px; height: 32px;
        background: #980808;
        border: 2px solid #ffffff;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        box-shadow: 0 4px 12px rgba(152, 8, 8, 0.45);
    }
    .contact-pin__icon {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        color: #ffffff;
        font-size: 13px;
        pointer-events: none;
    }
    .contact-pin::after {
        content: '';
        position: absolute;
        top: -4px; left: -4px;
        width: 40px; height: 40px;
        border-radius: 50%;
        background: rgba(152, 8, 8, 0.20);
        animation: contact-pin-pulse 1.8s ease-out infinite;
        z-index: -1;
    }
    @keyframes contact-pin-pulse {
        0%   { transform: scale(0.7); opacity: 0.8; }
        100% { transform: scale(1.4); opacity: 0;   }
    }

    /* Popup (untuk konsistensi dengan _usaha-map) */
    .leaflet-popup-content-wrapper:has(.contact-popup) {
        border-radius: 10px;
        box-shadow: 0 8px 22px rgba(0,0,0,0.12);
    }
    .contact-popup {
        font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
        min-width: 180px;
    }
    .contact-popup strong {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: #18181b;
        margin-bottom: 3px;
    }
    .contact-popup span {
        font-size: 12.5px;
        color: #6b7280;
        line-height: 1.45;
    }

    /* ── Info card ──────────────────────────────────── */
    .contact-info {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 10px 0;
    }
    .contact-info__title {
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 24px;
        color: #18181b;
        letter-spacing: -0.5px;
    }
    .contact-info__list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .contact-info__row {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        font-size: 14.5px;
        color: #4b5563;
        line-height: 1.55;
    }
    .contact-info__row strong {
        color: #18181b;
        font-weight: 600;
        margin-right: 4px;
    }
    .contact-info__row a {
        color: inherit;
        text-decoration: none;
        transition: color .15s;
    }
    .contact-info__row a:hover { color: #980808; }
    .contact-info__icon {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(152, 8, 8, 0.08);
        color: #980808;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        margin-top: 1px;
    }
    .contact-info__socials {
        display: flex;
        gap: 10px;
        margin-top: 8px;
    }
    .contact-info__social {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        color: #4b5563;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        transition: background .15s, color .15s, border-color .15s, transform .15s;
        text-decoration: none;
    }
    .contact-info__social:hover {
        background: #980808;
        border-color: #980808;
        color: #ffffff;
        transform: translateY(-2px);
    }

    /* ── Form section ───────────────────────────────── */
    .contact-form-section {
        padding: 30px 0 70px;
    }
    .contact-form-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 18px;
        padding: 40px;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.06);
    }
    .contact-form-card__head {
        margin-bottom: 28px;
    }
    .contact-form-card__head h2 {
        font-size: 26px;
        font-weight: 700;
        margin: 0 0 8px;
        color: #18181b;
    }
    .contact-form-card__head p {
        font-size: 14px;
        line-height: 1.6;
        color: #6b7280;
        margin: 0;
    }
    .contact-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px 22px;
    }
    .contact-form-grid > .full { grid-column: 1 / -1; }

    .contact-form-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .contact-form-field label {
        font-size: 13px;
        font-weight: 500;
        color: #4b5563;
    }
    .contact-form-field input,
    .contact-form-field textarea {
        width: 100%;
        padding: 12px 16px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        font-size: 14px;
        color: #18181b;
        font-family: inherit;
        outline: none;
        transition: border-color .15s, background .15s, box-shadow .15s;
    }
    .contact-form-field textarea {
        resize: vertical;
        min-height: 140px;
        line-height: 1.5;
    }
    .contact-form-field input:focus,
    .contact-form-field textarea:focus {
        border-color: #980808;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(152, 8, 8, 0.10);
    }
    .contact-form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }
    .contact-form-submit {
        background: #980808;
        color: #ffffff;
        border: none;
        padding: 12px 28px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background .15s, box-shadow .15s, transform .15s;
    }
    .contact-form-submit:hover {
        background: #7f0606;
        box-shadow: 0 6px 16px rgba(152, 8, 8, 0.30);
        transform: translateY(-1px);
    }
    .contact-form-submit:disabled {
        opacity: 0.65; cursor: not-allowed; transform: none;
    }
    .contact-form-success {
        display: none;
        margin-top: 18px;
        padding: 14px 16px;
        border-radius: 10px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
        font-size: 14px;
        font-weight: 400;
    }
    .contact-form-success.show { display: flex; align-items: center; gap: 10px; }

    /* ── Extras: jam operasional + FAQ singkat ─────── */
    .contact-extras {
        padding: 0 0 70px;
    }
    .extras-grid {
        display: grid;
        grid-template-columns: 1fr 1.6fr;
        gap: 22px;
    }
    .extras-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 26px 28px;
    }
    .extras-card h3 {
        font-size: 17px;
        font-weight: 700;
        margin: 0 0 14px;
        color: #18181b;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .extras-card h3 i {
        color: #980808;
        font-size: 16px;
    }
    .hours-row {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        color: #4b5563;
        padding: 7px 0;
        border-bottom: 1px dashed #f3f4f6;
    }
    .hours-row:last-child { border-bottom: 0; }
    .hours-row span:last-child { color: #18181b; font-weight: 500; }

    .faq-item {
        border-bottom: 1px solid #f3f4f6;
        padding: 14px 0;
    }
    .faq-item:last-child { border-bottom: 0; }
    .faq-item summary {
        cursor: pointer;
        font-size: 14.5px;
        font-weight: 500;
        color: #18181b;
        list-style: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .faq-item summary::-webkit-details-marker { display: none; }
    .faq-item summary::after {
        content: '\f078';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 11px;
        color: #980808;
        transition: transform .2s;
    }
    .faq-item[open] summary::after { transform: rotate(180deg); }
    .faq-item p {
        margin: 10px 0 0;
        font-size: 13.5px;
        line-height: 1.65;
        color: #4b5563;
    }

    /* ── Responsive ─────────────────────────────────── */
    @media (max-width: 991.98px) {
        .contact-hero__title       { font-size: 30px; }
        .contact-info__title       { font-size: 26px; }
        .extras-grid               { grid-template-columns: 1fr; }
    }
    @media (max-width: 767.98px) {
        .contact-hero              { padding: 40px 0 20px; }
        .contact-hero__title       { font-size: 26px; }
        .contact-info              { padding-top: 30px; }
        .contact-form-card         { padding: 28px 22px; }
        .contact-form-grid         { grid-template-columns: 1fr; }
        .contact-form-actions      { justify-content: stretch; }
        .contact-form-submit       { width: 100%; justify-content: center; }
    }

    /* ── Dark mode ──────────────────────────────────── */
    body.dark-mode .contact-page,
    body.dark-mode .contact-hero__title,
    body.dark-mode .contact-info__title,
    body.dark-mode .contact-info__row strong,
    body.dark-mode .contact-form-card__head h2,
    body.dark-mode .extras-card h3,
    body.dark-mode .faq-item summary,
    body.dark-mode .hours-row span:last-child {
        color: var(--tp-text-strong, #f9fafb);
    }
    body.dark-mode .contact-hero__lede,
    body.dark-mode .contact-info__row,
    body.dark-mode .contact-form-card__head p,
    body.dark-mode .contact-map-card__caption,
    body.dark-mode .hours-row,
    body.dark-mode .faq-item p {
        color: var(--tp-text-muted, #cbd5e1);
    }
    body.dark-mode .contact-map-card,
    body.dark-mode .contact-form-card,
    body.dark-mode .extras-card,
    body.dark-mode .contact-info__social {
        background: var(--tp-bg-surface, #1f2937);
        border-color: var(--tp-border, #374151);
    }
    body.dark-mode .contact-map-card__canvas,
    body.dark-mode .contact-map-card__canvas .leaflet-container {
        background: #111827;
    }
    body.dark-mode .leaflet-popup-content-wrapper,
    body.dark-mode .leaflet-popup-tip { background: #111827; color: #f9fafb; }
    body.dark-mode .contact-popup strong { color: #f9fafb; }
    body.dark-mode .contact-popup span   { color: #cbd5e1; }
    body.dark-mode .contact-form-field input,
    body.dark-mode .contact-form-field textarea {
        background: #111827;
        border-color: #374151;
        color: #f9fafb;
    }
    body.dark-mode .contact-form-field input:focus,
    body.dark-mode .contact-form-field textarea:focus {
        background: #0f172a;
        border-color: #f87171;
        box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.15);
    }
    body.dark-mode .contact-info__social { color: #cbd5e1; }
    body.dark-mode .contact-form-success {
        background: rgba(16, 185, 129, 0.10);
        border-color: rgba(16, 185, 129, 0.35);
        color: #6ee7b7;
    }
</style>
@endpush

@section('content')
<div class="contact-page guest-content-start">

    {{-- ── Hero ── --}}
    <section class="contact-hero">
        <div class="container">
            <h1 class="contact-hero__title">Halo, ada yang bisa kami bantu?</h1>
            <p class="contact-hero__lede">
                Tim TekoPerakku selalu terbuka untuk pengrajin, calon mitra, maupun pembeli.
                Kirim pesan, mampir ke kantor kami di Kotagede, atau sapa kami lewat media sosial —
                pilih cara yang paling nyaman buat Anda.
            </p>
        </div>
    </section>

    {{-- ── Hubungi Kami: peta placeholder + info ── --}}
    <section class="contact-section">
        <div class="container">
            <div class="row g-4 align-items-stretch">

                <div class="col-lg-6">
                    {{-- Leaflet map — lokasi kantor TekoPerakku, Kotagede --}}
                    <div class="contact-map-card" aria-label="Peta lokasi kantor TekoPerakku">
                        <div class="contact-map-card__canvas" id="contact-office-map"></div>
                        <div class="contact-map-card__caption">
                            <span>
                                <i class="fa-solid fa-location-dot" style="color:#980808;"></i>
                                Kemantren Kotagede, Yogyakarta
                            </span>
                            <a href="https://maps.app.goo.gl/4n6BZVu9xEQDqBun7"
                               target="_blank" rel="noopener">
                                <i class="fa-solid fa-up-right-from-square"></i>
                                Buka di Google Maps
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="contact-info">
                        <h2 class="contact-info__title">Hubungi Kami</h2>
                        <div class="contact-info__list">

                            <div class="contact-info__row">
                                <span class="contact-info__icon"><i class="fa-regular fa-building"></i></span>
                                <span><strong>Kantor:</strong> Kemantren Kotagede, Yogyakarta</span>
                            </div>
                            <div class="contact-info__row">
                                <span class="contact-info__icon"><i class="fa-solid fa-location-dot"></i></span>
                                <span>Jl. Nyi Wiji Adisara 39, Kel. Prenggan, Kem. Kotagede, Kota Yogyakarta</span>
                            </div>
                            <div class="contact-info__row">
                                <span class="contact-info__icon"><i class="fa-regular fa-envelope"></i></span>
                                <span><strong>Email:</strong>
                                    <a href="mailto:kg@jogjakota.go.id">kg@jogjakota.go.id</a>
                                </span>
                            </div>
                            <div class="contact-info__row">
                                <span class="contact-info__icon"><i class="fa-solid fa-phone"></i></span>
                                <span><strong>Telepon:</strong>
                                    <a href="tel:+62274375790">(0274) 375.790</a>
                                </span>
                            </div>
                            <div class="contact-info__row">
                                <span class="contact-info__icon"><i class="fa-regular fa-clock"></i></span>
                                <span><strong>Jam operasional:</strong> Senin – Jumat, 08.00 – 16.00 WIB</span>
                            </div>

                            <div class="contact-info__row" style="margin-top: 4px;">
                                <span class="contact-info__icon"><i class="fa-solid fa-share-nodes"></i></span>
                                <span>
                                    <strong>Sosial media:</strong>
                                    <div class="contact-info__socials">
                                        <a href="https://web.facebook.com/people/Kemantren-Kotagede/pfbid0vbSxN13HcQ5xwfYhn4Qm3wfbFVcGr6QPzh6mYxuRzJwdkZXy41TRf8AqbXVKo4Ttl/"
                                           target="_blank" rel="noopener"
                                           class="contact-info__social" aria-label="Facebook">
                                            <i class="fa-brands fa-facebook-f"></i>
                                        </a>
                                        <a href="https://www.instagram.com/kemantrenkg/"
                                           target="_blank" rel="noopener"
                                           class="contact-info__social" aria-label="Instagram">
                                            <i class="fa-brands fa-instagram"></i>
                                        </a>
                                        <a href="https://kotagedekec.jogjakota.go.id/"
                                           target="_blank" rel="noopener"
                                           class="contact-info__social" aria-label="Website">
                                            <i class="fa-solid fa-globe"></i>
                                        </a>
                                    </div>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ── Form section ── --}}
    <section class="contact-form-section">
        <div class="container">
            <div class="contact-form-card">
                <div class="contact-form-card__head">
                    <h2>Kirim Pesan</h2>
                    <p>Isi form di bawah — kami akan membalas paling lambat 1×24 jam pada hari kerja.</p>
                </div>

                {{-- Tanpa controller dulu: form pakai mailto: agar langsung berfungsi.
                     Saat email backend siap, ganti action ke route('contact.send') dan hapus mailto handler. --}}
                <form id="contact-form"
                      action="mailto:kg@jogjakota.go.id"
                      method="POST"
                      enctype="text/plain">
                    <div class="contact-form-grid">
                        <div class="contact-form-field">
                            <label for="contact-nama" class="label-required">Nama</label>
                            <input type="text" id="contact-nama" name="nama"
                                   placeholder="Nama lengkap Anda" required>
                        </div>
                        <div class="contact-form-field">
                            <label for="contact-email" class="label-required">Email</label>
                            <input type="email" id="contact-email" name="email"
                                   placeholder="nama@contoh.com" required>
                        </div>
                        <div class="contact-form-field full">
                            <label for="contact-subjek" class="label-required">Subjek</label>
                            <input type="text" id="contact-subjek" name="subjek"
                                   placeholder="Tentang apa pesan Anda?" required>
                        </div>
                        <div class="contact-form-field full">
                            <label for="contact-pesan" class="label-required">Pesan</label>
                            <textarea id="contact-pesan" name="pesan"
                                      placeholder="Tuliskan pesan Anda di sini…" required></textarea>
                        </div>
                    </div>

                    <div class="contact-form-actions">
                        <button type="submit" class="contact-form-submit" id="contact-submit-btn">
                            <i class="fa-regular fa-paper-plane"></i>
                            <span>Kirim Pesan</span>
                        </button>
                    </div>

                    <div class="contact-form-success" id="contact-form-success">
                        <i class="fa-solid fa-circle-check"></i>
                        Terima kasih! Pesan Anda akan dibuka di aplikasi email default Anda.
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- ── Extras: jam operasional + FAQ singkat ── --}}
    <section class="contact-extras">
        <div class="container">
            <div class="extras-grid">

                <div class="extras-card">
                    <h3><i class="fa-regular fa-clock"></i> Jam Layanan</h3>
                    <div class="hours-row"><span>Senin – Kamis</span><span>08.00 – 16.00</span></div>
                    <div class="hours-row"><span>Jumat</span><span>08.00 – 16.30</span></div>
                    <div class="hours-row"><span>Sabtu</span><span>Tutup</span></div>
                    <div class="hours-row"><span>Minggu &amp; Libur Nasional</span><span>Tutup</span></div>
                </div>

                <div class="extras-card">
                    <h3><i class="fa-regular fa-circle-question"></i> Pertanyaan yang Sering Ditanyakan</h3>

                    <details class="faq-item">
                        <summary>Bagaimana cara menjadi mitra UMKM di TekoPerakku?</summary>
                        <p>Anda dapat mendaftar lewat halaman pendaftaran atau menghubungi kami via
                           email/telepon. Tim kami akan memandu proses verifikasi toko Anda.</p>
                    </details>
                    <details class="faq-item">
                        <summary>Apakah pembeli bisa langsung chat dengan pengrajin?</summary>
                        <p>Ya. Setiap halaman toko menyediakan tombol “Hubungi Penjual” untuk chat
                           langsung dengan pemilik usaha setelah login.</p>
                    </details>
                    <details class="faq-item">
                        <summary>Berapa lama balasan dari tim TekoPerakku?</summary>
                        <p>Pesan yang masuk melalui form atau email biasanya dibalas dalam
                           1×24 jam pada hari kerja.</p>
                    </details>
                    <details class="faq-item">
                        <summary>Apakah saya bisa berkunjung langsung ke kantor?</summary>
                        <p>Tentu. Anda bisa mampir ke Kemantren Kotagede pada jam layanan di atas.
                           Disarankan membuat janji terlebih dahulu via email atau telepon.</p>
                    </details>
                </div>

            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script>
(function () {
    'use strict';

    // ── 1. Leaflet map: lokasi kantor TekoPerakku, Kotagede ──
    var mapEl = document.getElementById('contact-office-map');
    if (mapEl && typeof L !== 'undefined') {
        var OFFICE = {
            lat:  -7.8177408,
            lng:  110.3961321,
            name: 'Kantor TekoPerakku',
            addr: 'Kemantren Kotagede, Yogyakarta'
        };

        var map = L.map(mapEl, {
            center: [OFFICE.lat, OFFICE.lng],
            zoom: 16,
            scrollWheelZoom: false,  // jangan culik scroll halaman
            zoomControl: true
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var pinIcon = L.divIcon({
            className: '',
            html: '<div class="contact-pin">'
                + '<span class="contact-pin__body"></span>'
                + '<i class="contact-pin__icon fa-solid fa-store"></i>'
                + '</div>',
            iconSize:   [32, 32],
            iconAnchor: [16, 32],
            popupAnchor:[0, -30]
        });

        L.marker([OFFICE.lat, OFFICE.lng], { icon: pinIcon })
            .addTo(map)
            .bindPopup(
                '<div class="contact-popup">'
                + '<strong>' + OFFICE.name + '</strong>'
                + '<span>' + OFFICE.addr + '</span>'
                + '</div>',
                { closeButton: false }
            )
            .openPopup();

        // Aktifkan scroll-zoom hanya setelah user klik peta
        map.on('click',    function () { map.scrollWheelZoom.enable(); });
        map.on('mouseout', function () { map.scrollWheelZoom.disable(); });

        // Force redraw kalau container baru visible setelah init
        setTimeout(function () { map.invalidateSize(); }, 200);
    }

    // ── 2. Form submit feedback (mailto:) ──
    var form    = document.getElementById('contact-form');
    var btn     = document.getElementById('contact-submit-btn');
    var success = document.getElementById('contact-form-success');
    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.querySelector('span').textContent = 'Membuka aplikasi email…';
            success.classList.add('show');
            setTimeout(function () {
                btn.disabled = false;
                btn.querySelector('span').textContent = 'Kirim Pesan';
            }, 4000);
        });
    }
}());
</script>
@endpush

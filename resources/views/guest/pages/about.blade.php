@extends('guest.layouts.main')
@section('title', 'Tentang Kami')

@push('styles')
<style>
    /* ============================================================
       About Page (Tentang Kami) — scoped ke .about-page
       Hanya halaman ini yang kena style ini.
       ============================================================ */
    .about-page {
        color: #18181b;
    }

    /* ── Hero ───────────────────────────────────────── */
    .about-hero {
        padding: 70px 0 50px;
        text-align: center;
    }
    .about-hero__title {
        font-size: 38px;
        font-weight: 800;
        margin: 0 0 28px;
        color: #18181b;
        letter-spacing: -0.5px;
    }
    .about-hero__lede {
        max-width: 900px;
        margin: 0 auto;
        font-size: 15px;
        line-height: 1.85;
        color: #4b5563;
        text-align: justify;
    }
    .about-hero__lede strong { color: #18181b; font-weight: 700; }
    .about-hero__lede em     { color: #980808; font-style: italic; font-weight: 600; }

    /* ── Split (image + text) ────────────────────────── */
    .about-split {
        padding: 30px 0 60px;
    }
    .about-split__img {
        width: 100%;
        height: 100%;
        min-height: 320px;
        max-height: 380px;
        object-fit: cover;
        border-radius: 14px;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.10);
        display: block;
    }
    .about-split__text {
        font-size: 15px;
        line-height: 1.85;
        color: #4b5563;
        text-align: justify;
        margin: 0;
    }

    /* ── Reusable section heading ───────────────────── */
    .about-section {
        padding: 60px 0;
    }
    .about-section__head {
        text-align: center;
        margin-bottom: 40px;
    }
    .about-section__head h2 {
        font-size: 32px;
        font-weight: 800;
        margin: 0 0 12px;
        color: #18181b;
        letter-spacing: -0.5px;
    }
    .about-section__head p {
        max-width: 680px;
        margin: 0 auto;
        font-size: 14.5px;
        line-height: 1.7;
        color: #6b7280;
    }

    /* ── Visi & Misi cards ──────────────────────────── */
    .vision-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }
    .vision-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 32px 28px;
        transition: transform .2s, box-shadow .2s, border-color .2s;
    }
    .vision-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        border-color: #f3c5c5;
    }
    .vision-card__icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: rgba(152, 8, 8, 0.08);
        color: #980808;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 18px;
    }
    .vision-card__title {
        font-size: 19px;
        font-weight: 700;
        color: #18181b;
        margin: 0 0 10px;
    }
    .vision-card__text {
        font-size: 14px;
        line-height: 1.7;
        color: #4b5563;
        margin: 0;
    }
    .vision-card__list {
        margin: 0;
        padding-left: 0;
        list-style: none;
    }
    .vision-card__list li {
        font-size: 14px;
        line-height: 1.6;
        color: #4b5563;
        padding: 6px 0 6px 26px;
        position: relative;
    }
    .vision-card__list li::before {
        content: '\f00c'; /* check icon */
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        color: #980808;
        position: absolute;
        left: 0; top: 7px;
        font-size: 12px;
    }

    /* ── Feature trio (Dukungan / Marketplace / Pelestarian) ── */
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }
    .feature-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 30px 26px;
        text-align: left;
        transition: transform .2s, box-shadow .2s, border-color .2s;
    }
    .feature-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        border-color: #f3c5c5;
    }
    .feature-card__icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(152, 8, 8, 0.08);
        color: #980808;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 16px;
    }
    .feature-card h3 {
        font-size: 17px;
        font-weight: 700;
        margin: 0 0 8px;
        color: #18181b;
    }
    .feature-card p {
        font-size: 13.5px;
        line-height: 1.65;
        color: #4b5563;
        margin: 0;
    }

    /* ── Video section ──────────────────────────────── */
    .about-video {
        background: #fafafa;
        padding: 70px 0;
    }
    .about-video__wrap {
        max-width: 920px;
        margin: 0 auto;
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
    }
    /* Bootstrap 5 sudah punya .ratio — kita pakai itu untuk responsive 16:9.
       Tapi tetap tambah fallback supaya kalau Bootstrap belum load, video tetap rapi. */
    .about-video__ratio {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        background: #000;
    }
    .about-video__ratio iframe {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 0;
        display: block;
    }

    /* ── Responsive ─────────────────────────────────── */
    @media (max-width: 991.98px) {
        .about-hero__title         { font-size: 32px; }
        .about-section__head h2    { font-size: 26px; }
        .vision-grid,
        .feature-grid              { grid-template-columns: 1fr; }
        .about-split .row > div    { margin-bottom: 24px; }
    }
    @media (max-width: 575.98px) {
        .about-hero                { padding: 50px 0 30px; }
        .about-hero__title         { font-size: 26px; }
        .about-hero__lede,
        .about-split__text         { font-size: 14px; line-height: 1.75; }
        .about-section             { padding: 40px 0; }
        .about-video               { padding: 50px 0; }
    }

    /* ── Dark mode ──────────────────────────────────── */
    body.dark-mode .about-page,
    body.dark-mode .about-hero__title,
    body.dark-mode .about-section__head h2,
    body.dark-mode .vision-card__title,
    body.dark-mode .feature-card h3 {
        color: var(--tp-text-strong, #f9fafb);
    }
    body.dark-mode .about-hero__lede,
    body.dark-mode .about-split__text,
    body.dark-mode .about-section__head p,
    body.dark-mode .vision-card__text,
    body.dark-mode .vision-card__list li,
    body.dark-mode .feature-card p {
        color: var(--tp-text-muted, #cbd5e1);
    }
    body.dark-mode .about-hero__lede strong { color: var(--tp-text-strong, #f9fafb); }
    body.dark-mode .vision-card,
    body.dark-mode .feature-card {
        background: var(--tp-bg-surface, #1f2937);
        border-color: var(--tp-border, #374151);
    }
    body.dark-mode .vision-card:hover,
    body.dark-mode .feature-card:hover {
        border-color: #7f1d1d;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
    }
    body.dark-mode .about-video {
        background: #111827;
    }
    body.dark-mode .about-video__wrap {
        background: var(--tp-bg-surface, #1f2937);
        border-color: var(--tp-border, #374151);
    }
    body.dark-mode .about-split__img {
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.5);
    }
</style>
@endpush

@section('content')
<div class="about-page guest-content-start">

    {{-- ── Hero: Siapa Kami? ── --}}
    <section class="about-hero">
        <div class="container">
            <h1 class="about-hero__title">Siapa Kami?</h1>
            <p class="about-hero__lede">
                <strong>Kotagede</strong> merupakan sebuah kawasan bersejarah di Yogyakarta yang dahulu menjadi
                pusat Kerajaan Mataram Islam pada abad ke-16 hingga ke-17. Selain dikenal melalui peninggalan
                arsitektur tradisional Jawa dan jejak sejarah kerajaan, Kotagede juga masyhur sebagai sentra
                kerajinan perak. Tradisi mengolah perak di wilayah ini berawal dari masa kejayaan Mataram, ketika
                para pengrajin menghasilkan perhiasan dan benda-benda bernilai seni tinggi untuk kalangan
                bangsawan dan masyarakat luas. Dari masa ke masa, keterampilan mengukir perak diwariskan secara
                turun-temurun, sehingga Kotagede memperoleh julukan sebagai <em>“Kota Perak”</em> dan menjadi
                ikon kesenian logam mulia di Yogyakarta.
            </p>
        </div>
    </section>

    {{-- ── Split: foto Kotagede + paragraf lanjutan ── --}}
    <section class="about-split">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <img src="{{ asset('assets/images/Kota_Gede_Jogjakarta.jpg') }}"
                         alt="Sentra Kerajinan Perak Kotagede"
                         class="about-split__img"
                         onerror="this.onerror=null;this.src='{{ asset('assets/images/kerajinan-perak-kota-ged.png') }}';">
                </div>
                <div class="col-lg-6">
                    <p class="about-split__text">
                        Hingga saat ini, kerajinan perak di Kotagede tetap bertahan dan berkembang sebagai bagian
                        penting dari industri kreatif dan pariwisata budaya. Para pengrajin lokal masih
                        memproduksi perhiasan, aksesoris, perlengkapan rumah tangga, hingga karya seni dekoratif
                        dengan teknik tradisional yang dipadukan dengan desain modern. Wisatawan yang datang ke
                        Yogyakarta kerap diajak berkunjung ke bengkel-bengkel perak di Kotagede untuk menyaksikan
                        langsung proses pembuatan dan membeli hasil karya pengrajin. Dengan dukungan festival,
                        pameran, serta pemasaran digital, kerajinan perak Kotagede kini tidak hanya menjadi
                        kebanggaan lokal, tetapi juga memiliki daya saing di pasar nasional maupun internasional.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Visi & Misi ── --}}
    <section class="about-section">
        <div class="container">
            <div class="about-section__head">
                <h2>Visi &amp; Misi</h2>
                <p>Komitmen kami untuk mengangkat warisan kerajinan perak Kotagede ke panggung yang lebih luas.</p>
            </div>
            <div class="vision-grid">
                <article class="vision-card">
                    <div class="vision-card__icon"><i class="fa-solid fa-eye"></i></div>
                    <h3 class="vision-card__title">Visi</h3>
                    <p class="vision-card__text">
                        Menjadi platform digital terpercaya yang menghubungkan pengrajin perak Kotagede dengan
                        pasar nasional maupun internasional, sekaligus menjaga warisan budaya agar tetap hidup
                        di tengah perkembangan zaman.
                    </p>
                </article>
                <article class="vision-card">
                    <div class="vision-card__icon"><i class="fa-solid fa-flag"></i></div>
                    <h3 class="vision-card__title">Misi</h3>
                    <ul class="vision-card__list">
                        <li>Memberdayakan UMKM dan pengrajin lokal lewat etalase digital yang mudah digunakan.</li>
                        <li>Memperluas akses pasar agar karya pengrajin sampai ke lebih banyak pembeli.</li>
                        <li>Melestarikan teknik tradisional ukir, filigree, tatahan, dan cor perak khas Kotagede.</li>
                        <li>Membangun komunitas pengrajin yang saling mendukung dan berkembang bersama.</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    {{-- ── Tiga pilar (Dukungan UMKM / Marketplace & Komunitas / Pelestarian) ── --}}
    <section class="about-section" style="padding-top: 20px;">
        <div class="container">
            <div class="about-section__head">
                <h2>Apa yang Kami Bangun</h2>
                <p>Tiga fokus utama TekoPerakku — memberdayakan pengrajin, menumbuhkan komunitas,
                   dan menjaga warisan tetap relevan.</p>
            </div>
            <div class="feature-grid">
                <article class="feature-card">
                    <div class="feature-card__icon"><i class="fa-solid fa-hand-holding-heart"></i></div>
                    <h3>Dukungan untuk Pengrajin</h3>
                    <p>
                        Kami menyediakan etalase digital, panduan pemasaran sederhana, dan jalur komunikasi
                        langsung dengan pembeli. Tujuannya satu — agar pengrajin bisa fokus berkarya tanpa
                        kerumitan jualan online.
                    </p>
                </article>
                <article class="feature-card">
                    <div class="feature-card__icon"><i class="fa-solid fa-store"></i></div>
                    <h3>Marketplace &amp; Komunitas</h3>
                    <p>
                        Pembeli bisa menjelajah toko-toko perak Kotagede dalam satu tempat, melihat karya dari
                        beragam teknik dan gaya, lalu langsung terhubung dengan pengrajinnya. Sebuah ekosistem
                        yang saling menghidupkan.
                    </p>
                </article>
                <article class="feature-card">
                    <div class="feature-card__icon"><i class="fa-solid fa-gem"></i></div>
                    <h3>Pelestarian Kerajinan</h3>
                    <p>
                        Setiap karya yang tampil di sini membawa cerita teknik turun-temurun — ukir, filigree,
                        tatahan, hingga cor. Dengan menampilkannya secara modern, kami membantu menjaga tradisi
                        tetap dikenal generasi baru.
                    </p>
                </article>
            </div>
        </div>
    </section>

    {{-- ── Video section ── --}}
    <section class="about-video">
        <div class="container">
            <div class="about-section__head">
                <h2>Kerajinan Perak Kotagede</h2>
                <p>Sebuah perubahan besar dimulai dari satu ketikan aspirasi yang jujur.
                   Jadilah bagian dari komunitas yang kritis, cerdas, dan solutif.</p>
            </div>
            <div class="about-video__wrap">
                <div class="about-video__ratio">
                    <iframe
                        src="https://www.youtube.com/embed/3j4Rf2ojD5Q"
                        title="Video Kerajinan Perak Kotagede"
                        loading="lazy"
                        allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

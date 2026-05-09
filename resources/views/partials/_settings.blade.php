{{--
    Global Settings (Dark Mode + Font Size + Language)
    ==================================================
    Include partial ini di SETIAP layout root, sebelum </body>:

        @include('partials._settings')

    Kenapa partial ini?
    - Memastikan setting dari halaman /pengaturan terpakai di SEMUA halaman
      (sebelumnya logic ini hanya ada di layouts/user.blade.php & layouts/umkm.blade.php,
      tidak di guest/layouts/main.blade.php → guest pages tidak ikut tema gelap, dll.)
    - Single source of truth: kamus terjemahan & class CSS dark-mode dipusatkan di sini.

    Persistensi: localStorage (key: darkMode, fontSize, language, emailNotif).
    Persistence ini sudah cukup untuk web statis multi-halaman karena localStorage:
      - bertahan setelah reload
      - bertahan setelah navigasi antar-halaman
      - per-browser (tidak per-user) — sesuai pattern yang sudah dipakai pengaturan.blade.php
--}}

@once
{{-- ─────────────────────────────────────────────────────
     1. THEME CSS (centralized) + minimal scaling rules
        - theme-dark.css adalah single source of truth untuk dark mode
        - tokens (CSS variables) + komponen-spesifik override sudah di sana
     ───────────────────────────────────────────────────── --}}
<link rel="stylesheet" href="{{ asset('assets/css/theme-dark.css') }}" id="tp-theme-css">

<style id="tp-global-settings-css">
/* Font scaling — class .font-* (legacy) + .tp-font-* (baru) sudah di theme-dark.css.
   Block ini cuma fallback halus untuk halaman yg belum termuat theme-dark.css */
html.tp-font-small  { font-size: 14px; }
html.tp-font-medium { font-size: 16px; }
html.tp-font-large  { font-size: 18px; }
body.font-small  { font-size: 0.92em; }
body.font-medium { font-size: 1em; }
body.font-large  { font-size: 1.10em; }
</style>
@endonce

{{-- ─────────────────────────────────────────────────────
     2. SCRIPT EARLY-APPLY (di akhir partial — partial ini di-include di bawah body
        agar tidak block render, tapi dieksekusi SEBELUM konten interaktif lain)
     ───────────────────────────────────────────────────── --}}
@once
<script id="tp-global-settings-js">
(function () {
    'use strict';

    /**
     * Single source of truth untuk semua settings.
     * Membaca dari localStorage; bila belum ada, pakai default.
     */
    var SETTINGS = {
        darkMode:  localStorage.getItem('darkMode') === 'true',
        fontSize:  localStorage.getItem('fontSize') || 'medium',
        language:  localStorage.getItem('language') || 'id',
        emailNotif: localStorage.getItem('emailNotif') !== 'false'
    };

    // ── Apply ke <html> dan <body> ─────────────────────
    function applyVisualSettings() {
        var html = document.documentElement;
        var body = document.body;
        if (!body) return;

        // Dark mode
        body.classList.toggle('dark-mode', SETTINGS.darkMode);

        // Font size — apply di <html> & <body> sekaligus untuk kompatibilitas
        html.classList.remove('tp-font-small', 'tp-font-medium', 'tp-font-large');
        html.classList.add('tp-font-' + SETTINGS.fontSize);

        body.classList.remove('font-small', 'font-medium', 'font-large');
        body.classList.add('font-' + SETTINGS.fontSize);
    }

    // Jalankan secepatnya. Body mungkin belum ada bila partial diinclude di <head>,
    // jadi kita coba dua kali.
    if (document.body) {
        applyVisualSettings();
    } else {
        document.addEventListener('DOMContentLoaded', applyVisualSettings, { once: true });
    }

    // ── Translation dictionary ─────────────────────────
    var TRANSLATIONS = {
        en: {
            // Header & nav
            "Beranda": "Home",
            "Katalog": "Catalog",
            "Kategori": "Categories",
            "Tentang Kami": "About Us",
            "Kontak": "Contact",
            "Profil": "Profile",
            "Profil Saya": "My Profile",
            "Panel Akun": "Account Panel",
            "Chat": "Chat",
            "Pesan": "Messages",
            "Favorit": "Favorites",
            "Pengaturan": "Settings",
            "Logout": "Logout",
            "Login": "Login",
            "Daftar": "Register",
            "Cari Produk": "Search Products",
            "Cari produk atau kategori...": "Search products or categories...",

            // Pengaturan page
            "Ukuran Font": "Font Size",
            "Mode Gelap": "Dark Mode",
            "Notifikasi Email": "Email Notifications",
            "Bahasa": "Language",
            "Hapus Akun": "Delete Account",
            "Kecil": "Small",
            "Sedang": "Medium",
            "Besar": "Large",
            "Indonesia": "Indonesian",
            "English": "English",
            "Apakah anda yakin untuk menghapus akun?": "Are you sure you want to delete your account?",
            "Tidak": "No",
            "Iya": "Yes",

            // Common UI
            "Belum ada review": "No reviews yet",
            "Review": "Review",
            "Reviews": "Reviews",
            "Belum ada kategori": "No categories yet",
            "Produk tidak ditemukan.": "Products not found.",
            "Produk Terbaru Kami!": "Our Newest Products!",
            "Temukan Produk Terfavoritmu!": "Find your favorite product!",

            // Footer
            "Kategori": "Categories",
            "Informasi Kami": "Our Information",
            "Sosial Media": "Social Media",
            "Beranda": "Home",
            "Kontak Kami": "Contact Us"
        }
    };

    // ── Translation engine ─────────────────────────────
    function translatePage(lang) {
        if (lang === 'id' || !TRANSLATIONS[lang]) return;
        if (!document.body) return;

        var dict = TRANSLATIONS[lang];

        // 1. Walk text nodes — translate exact-match trims
        var walk = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
            acceptNode: function (node) {
                // Skip script/style/textarea content
                var p = node.parentNode;
                if (!p) return NodeFilter.FILTER_REJECT;
                var tag = p.nodeName;
                if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'TEXTAREA') {
                    return NodeFilter.FILTER_REJECT;
                }
                // Skip nodes yang sudah ditandai
                if (p.hasAttribute && p.hasAttribute('data-tp-no-translate')) {
                    return NodeFilter.FILTER_REJECT;
                }
                return node.nodeValue.trim() ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
            }
        });

        var node;
        while ((node = walk.nextNode())) {
            var trimmed = node.nodeValue.trim();
            if (dict[trimmed]) {
                node.nodeValue = node.nodeValue.replace(trimmed, dict[trimmed]);
            }
        }

        // 2. Translate placeholder, title, alt
        ['placeholder', 'title', 'alt', 'aria-label'].forEach(function (attr) {
            document.querySelectorAll('[' + attr + ']').forEach(function (el) {
                var v = el.getAttribute(attr);
                if (v && dict[v.trim()]) {
                    el.setAttribute(attr, dict[v.trim()]);
                }
            });
        });
    }

    // Jalankan setelah DOM ready (translation perlu konten utuh)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            translatePage(SETTINGS.language);
        }, { once: true });
    } else {
        translatePage(SETTINGS.language);
    }

    // ── Listen perubahan dari tab/halaman lain ────────
    // Saat user ubah setting di /pengaturan, tab lain akan otomatis update.
    window.addEventListener('storage', function (e) {
        if (!e.key) return;
        if (e.key === 'darkMode' || e.key === 'fontSize') {
            // Re-read dan apply tanpa reload
            SETTINGS.darkMode = localStorage.getItem('darkMode') === 'true';
            SETTINGS.fontSize = localStorage.getItem('fontSize') || 'medium';
            applyVisualSettings();
        } else if (e.key === 'language') {
            // Bahasa butuh reload untuk konsistensi (translation searah)
            window.location.reload();
        }
    });

    // ── Expose API singkat (opsional, untuk halaman pengaturan) ──
    window.TPSettings = {
        get: function (key) { return SETTINGS[key]; },
        set: function (key, value) {
            SETTINGS[key] = value;
            // Boolean serialization sesuai konvensi yang sudah ada di pengaturan.blade.php
            if (typeof value === 'boolean') {
                localStorage.setItem(key, value ? 'true' : 'false');
            } else {
                localStorage.setItem(key, value);
            }
            applyVisualSettings();
        },
        reapply: applyVisualSettings,
        translate: translatePage
    };
}());
</script>
@endonce

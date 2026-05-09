{{--
    Unified Header Component
    Digunakan oleh: layouts/user.blade.php, layouts/umkm.blade.php, guest/layouts/main.blade.php
    Variabel $kategoris di-inject otomatis oleh App\Providers\ViewServiceProvider
--}}
@php
    // Penentuan logo target & active state — kompatibel guest dan auth
    $logoRoute = Route::has('guest-index') ? route('guest-index') : url('/');

    $isHome    = Route::is('guest-index');
    $isKatalog = Route::is('guest-katalog') || Route::is('guest-products');
    $isAbout   = Route::is('guest-about');
    $isContact = Route::is('guest-contact');

    $userRole = auth()->check() ? auth()->user()->role : null;
@endphp

<header class="main-header">
    <div class="header-top">
        <a href="{{ $logoRoute }}" class="logo">TekoPerakku</a>

        <form action="{{ route('guest-katalog') }}" method="GET" class="search-container" role="search">
            <i class="fas fa-search search-icon"></i>
            <input type="search" name="search" class="search-input"
                   placeholder="Cari Produk" value="{{ request('search') }}" autocomplete="off">
        </form>

        <div class="header-actions">
            @auth
                @if($userRole === 'user')
                    <a href="{{ route('favorit') }}" class="action-link" aria-label="Favorit"><i class="far fa-heart"></i></a>
                @endif
                <a href="{{ route('chats.index') }}" class="action-link" aria-label="Pesan"><i class="far fa-comment-dots"></i></a>

                <div class="user-profile-trigger" id="profileTrigger" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar-circle">
                        <img src="{{ auth()->user()->foto
                                        ? asset('storage/'.auth()->user()->foto)
                                        : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->nama ?? auth()->user()->username).'&background=e4e4e7&color=71717a' }}"
                             alt="">
                    </div>
                    <span class="user-name">{{ auth()->user()->nama ?? auth()->user()->username }}</span>
                    <i class="fas fa-chevron-down profile-chevron" aria-hidden="true"></i>

                    <div class="profile-dropdown" id="profileDropdown" role="menu">
                        @php
                            $panelRoute = 'user.profile';
                            if ($userRole === 'umkm')      $panelRoute = 'umkm.profile';
                            elseif (in_array($userRole, ['admin_utama','admin_wilayah'])) $panelRoute = 'admin.dashboard';
                        @endphp
                        <a href="{{ Route::has($panelRoute) ? route($panelRoute) : '#' }}" class="dropdown-item-link">
                            <i class="fas fa-th-large"></i> Panel Akun
                        </a>
                        @if(Route::has('pengaturan'))
                            <a href="{{ route('pengaturan') }}" class="dropdown-item-link">
                                <i class="fas fa-cog"></i> Pengaturan
                            </a>
                        @endif
                        <hr class="dropdown-divider-line">
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item-link dropdown-item-danger">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('loginForm') }}" class="action-link login-btn" aria-label="Login">
                    <i class="far fa-user"></i>
                    <span class="login-btn-text">Login</span>
                </a>
            @endauth

            <button type="button" class="hamburger-btn" id="hamburgerBtn" aria-label="Buka menu" aria-controls="navMenu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <nav class="nav-menu" id="navMenu" aria-label="Navigasi utama">
        <a href="{{ route('guest-index') }}"
           class="nav-link {{ $isHome ? 'is-active' : '' }}">Beranda</a>

        <a href="{{ Route::has('guest-katalog') ? route('guest-katalog') : route('guest-products') }}"
           class="nav-link {{ $isKatalog ? 'is-active' : '' }}">Katalog</a>

        <div class="category-dropdown-wrapper" id="categoryWrap">
            <a href="#" class="nav-link nav-link-toggle" id="categoryToggle"
               role="button" aria-haspopup="true" aria-expanded="false">
                Kategori <i class="fas fa-chevron-down nav-chevron" aria-hidden="true"></i>
            </a>
            <div class="category-dropdown" id="categoryDropdown" role="menu">
                <div class="category-dropdown-grid">
                    @foreach(($categoryTypeLabels ?? []) as $type => $label)
                        <div class="category-dropdown-column">
                            <h4>{{ $label }}</h4>
                            @forelse(($categoryGroups[$type] ?? collect()) as $kategori)
                                <a href="{{ route('guest-katalog', ['kategori' => $kategori->slug]) }}"
                                   class="category-item {{ request('kategori') === $kategori->slug ? 'is-active' : '' }}">
                                    {{ $kategori->nama_kategori_produk }}
                                </a>
                            @empty
                                <span class="category-item category-empty">Belum ada kategori</span>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <a href="{{ Route::has('guest-about') ? route('guest-about') : '#' }}"
           class="nav-link {{ $isAbout ? 'is-active' : '' }}">Tentang Kami</a>

        <a href="{{ Route::has('guest-contact') ? route('guest-contact') : '#' }}"
           class="nav-link {{ $isContact ? 'is-active' : '' }}">Kontak</a>
    </nav>
</header>

<style>
/* =========================================================
   UNIFIED HEADER STYLES
   - Visual identik header user/umkm yang sudah ada
   - + animasi hover underline-slide dari main.blade.php
   - + fix responsif kategori (no overflow di mobile)
   ========================================================= */

.main-header {
    --header-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    border-bottom: 1px solid #e4e4e7;
    padding: 20px 80px;
    background: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    /* Pastikan header selalu DI ATAS konten halaman.
       Project ini punya .filter-group-dropdown .dropdown-menu dengan z-index 1000,
       jadi header butuh z-index lebih tinggi agar dropdown filter halaman tidak
       menembus area header saat scroll/buka. */
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1100;
    box-shadow: var(--header-shadow);
    /* Cegah konten halaman "bocor" ke header karena negative margin / transform */
    isolation: isolate;
}

:root {
    --header-height: 142px;
}

html {
    scroll-padding-top: calc(var(--header-height) + 24px);
}

body {
    padding-top: var(--header-height);
}

.header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 25px;
    gap: 20px;
}

/* ── Logo ─────────────────────────────────────────────── */
.main-header .logo {
    font-size: 26px;
    font-weight: 700;
    color: #000;
    text-decoration: none;
    letter-spacing: -0.5px;
    flex-shrink: 0;
    transition: color 0.3s ease;
}

.main-header .logo:hover {
    color: #980808;
}

/* ── Search ───────────────────────────────────────────── */
.main-header .search-container {
    flex: 1;
    max-width: 500px;
    margin: 0 40px;
    position: relative;
}

.main-header .search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #a1a1aa;
    pointer-events: none;
}

.main-header .search-input {
    width: 100%;
    padding: 12px 20px 12px 45px;
    border-radius: 8px;
    border: 1px solid #e4e4e7;
    font-size: 14px;
    outline: none;
    background: #fff;
    color: #18181b;
    font-family: inherit;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.main-header .search-input::placeholder { color: #a1a1aa; }

.main-header .search-input:focus {
    border-color: #980808;
    box-shadow: 0 0 0 3px rgba(152,8,8,0.12);
}

/* ── Action icons & user dropdown ─────────────────────── */
.main-header .header-actions {
    display: flex;
    align-items: center;
    gap: 25px;
    flex-shrink: 0;
}

.main-header .action-link {
    color: #3f3f46;
    text-decoration: none;
    font-size: 20px;
    transition: color 0.2s ease, transform 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.main-header .action-link:hover {
    color: #980808;
    transform: translateY(-1px);
}

.main-header .login-btn .login-btn-text { font-size: 14px; font-weight: 600; }

.main-header .user-profile-trigger {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    text-decoration: none;
    color: #1a1a1a;
    position: relative;
    user-select: none;
    padding: 4px 0;
}

.main-header .avatar-circle {
    width: 32px;
    height: 32px;
    background: #e4e4e7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.main-header .avatar-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.main-header .user-name {
    font-size: 14px;
    font-weight: 600;
    max-width: 140px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.main-header .profile-chevron {
    font-size: 10px;
    color: #a1a1aa;
    transition: transform 0.25s ease;
}

.main-header .user-profile-trigger.is-open .profile-chevron {
    transform: rotate(180deg);
}

.main-header .profile-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    min-width: 180px;
    background: #fff;
    border: 1px solid #e4e4e7;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    padding: 6px;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(6px);
    transition: opacity 0.2s, transform 0.2s, visibility 0.2s;
}

.main-header .profile-dropdown.is-open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.main-header .dropdown-item-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    font-size: 13.5px;
    font-weight: 500;
    color: #18181b;
    text-decoration: none;
    border-radius: 7px;
    transition: background 0.2s, color 0.2s;
    width: 100%;
    border: none;
    background: transparent;
    cursor: pointer;
    text-align: left;
    font-family: inherit;
}

.main-header .dropdown-item-link i {
    width: 16px;
    text-align: center;
    font-size: 13px;
}

.main-header .dropdown-item-link:hover {
    background: #f4f4f5;
    color: #980808;
}

.main-header .dropdown-item-danger { color: #dc2626; }
.main-header .dropdown-item-danger:hover { background: #fef2f2; color: #dc2626; }

.main-header .dropdown-divider-line {
    border: 0;
    border-top: 1px solid #e4e4e7;
    margin: 5px 4px;
}

/* ── Hamburger (mobile only) ──────────────────────────── */
.main-header .hamburger-btn {
    display: none;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 5px;
    width: 38px;
    height: 38px;
    padding: 0;
    background: transparent;
    border: 1.5px solid #e4e4e7;
    border-radius: 8px;
    cursor: pointer;
    transition: border-color 0.2s;
}

.main-header .hamburger-btn:hover { border-color: #980808; }

.main-header .hamburger-btn span {
    display: block;
    width: 18px;
    height: 2px;
    background: #18181b;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.main-header .hamburger-btn.is-open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.main-header .hamburger-btn.is-open span:nth-child(2) { opacity: 0; }
.main-header .hamburger-btn.is-open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* ── Nav menu ─────────────────────────────────────────── */
.main-header .nav-menu {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;       /* FIX BUG: tidak overflow saat ngepas */
    gap: 40px;
    row-gap: 12px;
}

.main-header .nav-link {
    text-decoration: none;
    color: #18181b;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;            /* untuk underline slide */
    display: inline-block;
    padding: 6px 2px;
    transition: color 0.3s ease, transform 0.3s ease;
    background: transparent;
    border: none;
    cursor: pointer;
    font-family: inherit;
}

.main-header .nav-link i {
    margin-left: 5px;
    font-size: 10px;
}

/* ANIMASI HOVER UNDERLINE-SLIDE — diadaptasi dari index-css.css */
.main-header .nav-link::after {
    content: '';
    position: absolute;
    left: 50%;
    bottom: -6px;
    transform: translateX(-50%);
    width: 0;
    height: 3px;
    background-color: #980808;
    border-radius: 2px;
    transition: width 0.3s ease;
}

.main-header .nav-link:hover,
.main-header .nav-link.is-active {
    color: #980808;
}

.main-header .nav-link:hover { transform: translateY(-1px); }

.main-header .nav-link:hover::after,
.main-header .nav-link.is-active::after {
    width: 50px;
}

/* ── Kategori dropdown ────────────────────────────────── */
.main-header .category-dropdown-wrapper {
    position: relative;
}

.main-header .nav-chevron {
    transition: transform 0.25s ease;
}

.main-header .category-dropdown-wrapper.is-open .nav-chevron {
    transform: rotate(180deg);
}

.main-header .category-dropdown {
    position: absolute;
    top: calc(100% + 14px);
    left: 50%;
    transform: translateX(-50%) translateY(6px);
    background: #fff;
    border: 1px solid #e4e4e7;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    min-width: 680px;
    max-height: min(520px, calc(100vh - var(--header-height) - 28px));
    overflow-y: auto;
    padding: 0;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s, transform 0.2s, visibility 0.2s;
}

.main-header .category-dropdown-grid {
    display: grid;
    grid-template-columns: 1fr 1.35fr 1fr;
    gap: 0;
    padding: 34px 28px;
}

.main-header .category-dropdown-column {
    padding: 0 28px;
    border-right: 1px solid #e4e4e7;
}

.main-header .category-dropdown-column:first-child {
    padding-left: 0;
}

.main-header .category-dropdown-column:last-child {
    border-right: 0;
    padding-right: 0;
}

.main-header .category-dropdown-column h4 {
    margin: 0 0 18px;
    color: #18181b;
    font-size: 15px;
    font-weight: 800;
    line-height: 1.3;
}

/* Hover-open untuk desktop, click-toggle untuk semua */
@media (hover: hover) {
    .main-header .category-dropdown-wrapper:hover .category-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }
}

.main-header .category-dropdown.is-open {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
}

/* Hover-area buffer agar dropdown tidak hilang saat mouse pindah */
.main-header .category-dropdown::before {
    content: '';
    position: absolute;
    top: -14px;
    left: 0;
    right: 0;
    height: 14px;
}

.main-header .category-item {
    display: block;
    padding: 10px 12px;
    text-decoration: none;
    color: #18181b;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px;
    transition: background 0.2s, color 0.2s, padding-left 0.2s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.main-header .category-item:hover {
    background: #f4f4f5;
    color: #980808;
    padding-left: 16px;
}

.main-header .category-item.is-active {
    background: rgba(152,8,8,0.08);
    color: #980808;
}

.main-header .category-item.category-empty {
    color: #a1a1aa;
    font-weight: 500;
    cursor: default;
}

.main-header .category-item.category-empty:hover {
    background: transparent;
    padding-left: 14px;
    color: #a1a1aa;
}

/* =========================================================
   RESPONSIF
   ========================================================= */
@media (max-width: 1100px) {
    .main-header { padding: 18px 40px; }
    .main-header .nav-menu { gap: 28px; }
    .main-header .search-container { margin: 0 20px; }
}

@media (max-width: 900px) {
    .main-header .user-name { display: none; }
}

/* Breakpoint utama mobile — hamburger aktif */
@media (max-width: 768px) {
    .main-header { padding: 16px 20px; }
    .header-top  { margin-bottom: 0; gap: 12px; }

    .main-header .search-container { margin: 0; max-width: none; }
    .main-header .header-actions   { gap: 14px; }

    .main-header .hamburger-btn    { display: flex; }

    /* Nav menu jadi panel slide-down — tidak menabrak konten */
    .main-header .nav-menu {
        position: relative;
        flex-direction: column;
        align-items: stretch;
        gap: 0;
        max-height: 0;
        overflow: hidden;
        margin-top: 0;
        transition: max-height 0.35s ease, margin-top 0.35s ease;
        border-top: 0 solid transparent;
    }

    .main-header .nav-menu.is-open {
        max-height: 600px;
        overflow-y: auto;
        margin-top: 16px;
        border-top: 1px solid #e4e4e7;
        padding-top: 12px;
    }

    .main-header .nav-link {
        padding: 12px 8px;
        text-align: left;
        font-size: 14px;
        border-radius: 8px;
        transition: background 0.2s, color 0.2s;
    }

    .main-header .nav-link:hover { background: #f4f4f5; transform: none; }

    /* Underline disesuaikan agar tetap rapi di mobile */
    .main-header .nav-link::after {
        left: 8px;
        transform: none;
        bottom: 4px;
    }
    .main-header .nav-link:hover::after,
    .main-header .nav-link.is-active::after { width: 30px; }

    /* Kategori jadi accordion — buka via click (.is-open) */
    .main-header .category-dropdown-wrapper { width: 100%; }

    .main-header .category-dropdown {
        position: static;
        transform: none;
        opacity: 1;
        visibility: visible;
        max-height: 0;
        min-width: 0;
        width: 100%;
        margin-top: 0;
        padding: 0 8px;
        border: 0;
        box-shadow: none;
        background: transparent;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease, margin-top 0.3s ease;
    }

    .main-header .category-dropdown-grid {
        display: block;
        padding: 0;
    }

    .main-header .category-dropdown-column {
        padding: 12px 0;
        border-right: 0;
        border-bottom: 1px solid #e4e4e7;
    }

    .main-header .category-dropdown-column:first-child,
    .main-header .category-dropdown-column:last-child {
        padding-left: 0;
        padding-right: 0;
    }

    .main-header .category-dropdown-column:last-child {
        border-bottom: 0;
    }

    .main-header .category-dropdown-column h4 {
        font-size: 13px;
        margin: 0 0 8px;
        padding: 0 12px 0 28px;
    }

    .main-header .category-dropdown::before { display: none; }

    .main-header .category-dropdown-wrapper.is-open .category-dropdown {
        max-height: 320px;
        overflow-y: auto;
        padding: 4px 8px 8px;
        margin-top: 4px;
    }

    .main-header .category-item {
        padding: 10px 12px 10px 28px;
        font-size: 13px;
    }
    .main-header .category-item:hover { padding-left: 32px; }

    /* Disable hover-open dropdown di mobile */
    @media (hover: hover) {
        .main-header .category-dropdown-wrapper:hover .category-dropdown {
            opacity: 1;
            visibility: visible;
            transform: none;
        }
    }
}

@media (max-width: 480px) {
    .main-header .logo            { font-size: 22px; }
    .main-header .header-actions  { gap: 10px; }
    .main-header .login-btn-text  { display: none; }
}

/* =========================================================
   DARK MODE (kompat dengan body.dark-mode dari layouts user/umkm)
   ========================================================= */
body.dark-mode .main-header                 { background: #1e1e1e; border-color: #333; }
body.dark-mode .main-header .logo,
body.dark-mode .main-header .user-name      { color: #fff; }
body.dark-mode .main-header .nav-link       { color: #e4e4e7; }
body.dark-mode .main-header .nav-link:hover,
body.dark-mode .main-header .nav-link.is-active { color: #f87171; }
body.dark-mode .main-header .nav-link::after { background: #f87171; }
body.dark-mode .main-header .search-input   { background: #2a2a2a; border-color: #444; color: #fff; }
body.dark-mode .main-header .profile-dropdown,
body.dark-mode .main-header .category-dropdown { background: #1e1e1e; border-color: #333; }
body.dark-mode .main-header .category-dropdown-column { border-color: #333; }
body.dark-mode .main-header .category-dropdown-column h4 { color: #fff; }
body.dark-mode .main-header .dropdown-item-link,
body.dark-mode .main-header .category-item  { color: #e4e4e7; }
body.dark-mode .main-header .dropdown-item-link:hover,
body.dark-mode .main-header .category-item:hover { background: #2a2a2a; }
body.dark-mode .main-header .hamburger-btn span  { background: #e4e4e7; }
</style>

<script>
(function () {
    'use strict';

    var header = document.querySelector('.main-header');
    if (!header) return;

    var syncHeaderHeight = function () {
        document.documentElement.style.setProperty('--header-height', header.offsetHeight + 'px');
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', syncHeaderHeight);
    } else {
        syncHeaderHeight();
    }

    window.addEventListener('load', syncHeaderHeight);
    window.addEventListener('resize', syncHeaderHeight);

    if ('ResizeObserver' in window) {
        new ResizeObserver(syncHeaderHeight).observe(header);
    }

    /* ── Profile dropdown toggle ──────────────────────── */
    var profileTrigger  = header.querySelector('#profileTrigger');
    var profileDropdown = header.querySelector('#profileDropdown');

    if (profileTrigger && profileDropdown) {
        profileTrigger.addEventListener('click', function (e) {
            // klik link/button di dalam dropdown → biarkan navigasi normal
            if (e.target.closest('.dropdown-item-link') || e.target.closest('form')) return;
            e.stopPropagation();
            var open = profileDropdown.classList.toggle('is-open');
            profileTrigger.classList.toggle('is-open', open);
            profileTrigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    /* ── Kategori dropdown toggle (click — penting untuk mobile) ── */
    var categoryWrap   = header.querySelector('#categoryWrap');
    var categoryToggle = header.querySelector('#categoryToggle');
    var categoryMenu   = header.querySelector('#categoryDropdown');

    if (categoryToggle && categoryWrap && categoryMenu) {
        categoryToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var open = categoryWrap.classList.toggle('is-open');
            categoryMenu.classList.toggle('is-open', open);
            categoryToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            window.requestAnimationFrame(syncHeaderHeight);
        });
    }

    /* ── Hamburger / mobile nav ───────────────────────── */
    var hamburger = header.querySelector('#hamburgerBtn');
    var navMenu   = header.querySelector('#navMenu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function () {
            var open = navMenu.classList.toggle('is-open');
            hamburger.classList.toggle('is-open', open);
            hamburger.setAttribute('aria-expanded', open ? 'true' : 'false');
            window.requestAnimationFrame(syncHeaderHeight);
        });
    }

    /* ── Tutup dropdown saat klik di luar ──────────────── */
    document.addEventListener('click', function (e) {
        if (profileTrigger && !profileTrigger.contains(e.target)) {
            profileDropdown && profileDropdown.classList.remove('is-open');
            profileTrigger.classList.remove('is-open');
            profileTrigger.setAttribute('aria-expanded', 'false');
        }
        if (categoryWrap && !categoryWrap.contains(e.target)) {
            categoryWrap.classList.remove('is-open');
            categoryMenu && categoryMenu.classList.remove('is-open');
            categoryToggle && categoryToggle.setAttribute('aria-expanded', 'false');
            window.requestAnimationFrame(syncHeaderHeight);
        }
    });

    /* ── ESC tutup semua ──────────────────────────────── */
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        profileDropdown && profileDropdown.classList.remove('is-open');
        profileTrigger  && profileTrigger.classList.remove('is-open');
        categoryWrap    && categoryWrap.classList.remove('is-open');
        categoryMenu    && categoryMenu.classList.remove('is-open');
        navMenu         && navMenu.classList.remove('is-open');
        hamburger       && hamburger.classList.remove('is-open');
        window.requestAnimationFrame(syncHeaderHeight);
    });
}());
</script>

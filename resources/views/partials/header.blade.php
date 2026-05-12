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

{{-- Header styles → public/assets/css/header.css (loaded @once below). --}}
@once
    <link rel="stylesheet" href="{{ asset('assets/css/header.css') }}">
@endonce

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
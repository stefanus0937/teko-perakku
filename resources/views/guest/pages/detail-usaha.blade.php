@extends('guest.layouts.main')

@section('title', 'Profil Usaha - ' . translate_text($usaha->nama_usaha))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/detail-usaha.css') }}">
    @if ($usaha->hasCoordinates())
        {{-- Leaflet hanya di-load jika ada koordinat valid — hemat bandwidth utk usaha tanpa lokasi --}}
        <link rel="stylesheet"
              href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
              integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
              crossorigin="">
    @endif
@endpush

@section('content')

@include('partials._rating-styles')

@php
    // Auth checks untuk Chat button
    $isOwnerLoggedIn = auth()->check() && auth()->id() === $usaha->user_id;
    $canChat = auth()->check() && !$isOwnerLoggedIn;

    // Handle (@username) auto-generated dari nama
    $usahaHandle = '@' . \Illuminate\Support\Str::slug($usaha->nama_usaha, '');

    // Daftar social link yang ada saja
    $socials = collect([
        ['key' => 'whatsapp',  'url' => $usaha->link_wa_usaha ?? $usaha->telp_usaha, 'icon' => 'fab fa-whatsapp', 'label' => 'WhatsApp', 'is_phone' => true],
        ['key' => 'instagram', 'url' => $usaha->link_instagram_usaha, 'icon' => 'fab fa-instagram', 'label' => 'Instagram'],
        ['key' => 'facebook',  'url' => $usaha->link_facebook_usaha,  'icon' => 'fab fa-facebook-f','label' => 'Facebook'],
        ['key' => 'tiktok',    'url' => $usaha->link_tiktok_usaha,    'icon' => 'fab fa-tiktok',   'label' => 'TikTok'],
        ['key' => 'shopee',    'url' => $usaha->link_shopee_usaha,    'icon' => 'fas fa-shopping-bag', 'label' => 'Shopee'],
        ['key' => 'tokopedia', 'url' => $usaha->link_tokopedia_usaha, 'icon' => 'fas fa-store',    'label' => 'Tokopedia'],
    ])->filter(fn ($s) => !empty($s['url']))->values();

    // Format WhatsApp number
    $waNumber = null;
    if ($usaha->link_wa_usaha || $usaha->telp_usaha) {
        $raw = $usaha->link_wa_usaha ?: $usaha->telp_usaha;
        $waNumber = preg_replace('/[^0-9]/', '', $raw);
        if (str_starts_with($waNumber, '0')) {
            $waNumber = '62' . substr($waNumber, 1);
        }
    }

    // Gallery photos: foto_tempat berisi JSON / comma-separated paths (per migrasi awal)
    $gallery = $usaha->foto_tempat ?? [];
@endphp

{{-- ── Breadcrumb ── --}}
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <nav aria-label="breadcrumb" class="product-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('guest-katalog') }}">Katalog</a></li>
                    @if (isset($previousProduct) && $previousProduct)
                        <li class="breadcrumb-item">
                            <a href="{{ route('guest-singleProduct', $previousProduct->slug) }}">{{ translate_text($previousProduct->nama_produk) }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ translate_text($usaha->nama_usaha) }}</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<section class="detail-usaha-section">
    <div class="container">
        <div class="row">

            {{-- ────────────── Sidebar Kiri: Profil Usaha ────────────── --}}
            <div class="col-lg-4 col-md-12">
                <aside class="usaha-profile-card">

                    {{-- Header: nama + handle --}}
                    <header class="usaha-profile-card__header">
                        <h2 class="usaha-profile-card__name">{{ translate_text($usaha->nama_usaha) }}</h2>
                        <p class="usaha-profile-card__handle">{{ $usahaHandle }}</p>
                    </header>

                    {{-- Foto bulat --}}
                    <div class="usaha-profile-card__photo">
                        <img
                            src="{{ $usaha->foto_usaha ? asset('storage/'.$usaha->foto_usaha) : asset('assets/images/kategori-default.jpg') }}"
                            alt="Foto {{ translate_text($usaha->nama_usaha) }}"
                            onerror="this.onerror=null;this.src='{{ asset('assets/images/kategori-default.jpg') }}';"
                        >
                    </div>

                    {{-- Social media icons (hanya yang ada) --}}
                    @if ($socials->isNotEmpty())
                        <div class="usaha-socials">
                            @foreach ($socials as $s)
                                @php
                                    $href = $s['key'] === 'whatsapp' && $waNumber
                                        ? 'https://wa.me/' . $waNumber
                                        : $s['url'];
                                @endphp
                                <a href="{{ $href }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="usaha-social usaha-social--{{ $s['key'] }}"
                                   title="{{ $s['label'] }}"
                                   aria-label="{{ $s['label'] }}">
                                    <i class="{{ $s['icon'] }}"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Chat Seller button (disabled jika owner) --}}
                    <div class="usaha-chat-wrapper">
                        @if ($isOwnerLoggedIn)
                            <button class="usaha-chat-btn" disabled
                                    title="Anda adalah pemilik toko ini">
                                <span>Hubungi Penjual</span>
                                <i class="fa-regular fa-comment-dots"></i>
                            </button>
                            <p class="usaha-chat-hint">Anda pemilik toko ini.</p>
                        @elseif ($canChat)
                            <a href="{{ route('chats.show', ['user' => $usaha->user_id, 'usaha_id' => $usaha->id]) }}"
                               class="usaha-chat-btn">
                                <span>Hubungi Penjual</span>
                                <i class="fa-regular fa-comment-dots"></i>
                            </a>
                        @else
                            <a href="{{ route('loginForm') }}" class="usaha-chat-btn">
                                <span>Login untuk Chat</span>
                                <i class="fa-regular fa-comment-dots"></i>
                            </a>
                        @endif
                    </div>

                    {{-- Deskripsi --}}
                    @if ($usaha->deskripsi_usaha)
                        <p class="usaha-description">{{ translate_text($usaha->deskripsi_usaha) }}</p>
                    @endif

                    {{-- Spesialisasi (jika ada di data) --}}
                    @if ($usaha->spesialisasi_usaha)
                        <div class="usaha-spec">
                            <p>Spesialis:</p>
                            <span>{{ $usaha->spesialisasi_usaha }}</span>
                        </div>
                    @endif
                </aside>

                {{-- Contact info card --}}
                @if ($usaha->email_usaha || $usaha->telp_usaha || $usaha->link_website_usaha)
                    <aside class="usaha-contact-card">
                        @if ($usaha->email_usaha)
                            <a href="mailto:{{ $usaha->email_usaha }}" class="usaha-contact-item">
                                <span class="usaha-contact-item__icon"><i class="fa-regular fa-envelope"></i></span>
                                <span class="usaha-contact-item__body">
                                    <strong>{{ $usaha->email_usaha }}</strong>
                                    <small>Email</small>
                                </span>
                            </a>
                        @endif
                        @if ($usaha->telp_usaha)
                            <a href="tel:{{ $usaha->telp_usaha }}" class="usaha-contact-item">
                                <span class="usaha-contact-item__icon"><i class="fa-solid fa-phone"></i></span>
                                <span class="usaha-contact-item__body">
                                    <strong>{{ $usaha->telp_usaha }}</strong>
                                    <small>Telepon</small>
                                </span>
                            </a>
                        @endif
                        @if ($usaha->link_website_usaha)
                            <a href="{{ $usaha->link_website_usaha }}" target="_blank" rel="noopener" class="usaha-contact-item">
                                <span class="usaha-contact-item__icon"><i class="fa-solid fa-globe"></i></span>
                                <span class="usaha-contact-item__body">
                                    <strong>{{ \Illuminate\Support\Str::limit(preg_replace('#^https?://#','', $usaha->link_website_usaha), 30) }}</strong>
                                    <small>Website</small>
                                </span>
                            </a>
                        @endif
                    </aside>
                @endif

                {{-- ── Lokasi: 3 state ──
                     1. Punya koordinat → render Leaflet map + popup + tombol "Buka di Google Maps"
                     2. Punya link Google Maps saja → placeholder bergaya dengan CTA
                     3. Tidak ada apa-apa → placeholder "Lokasi belum tersedia" --}}
                <aside class="usaha-map-card">
                    @if ($usaha->hasCoordinates())
                        @php $usahaAlamat = optional($usaha->user)->alamat; @endphp
                        {{-- ── Toko di Sekitar (multi-marker map) ── --}}
                    @if (isset($nearbyUsahas) && $nearbyUsahas->isNotEmpty())
                        <div class="usaha-nearby-section mt-4">
                            @include('partials._usaha-map', [
                                'usahas'         => $nearbyUsahas,
                                'currentUsahaId' => $usaha->id,
                                'mapId'          => 'usaha-map-nearby',
                                'height'         => '440px',
                            ])
                        </div>
                    @endif
                        <div class="usaha-map-card__footer">
                            @if ($usahaAlamat)
                                <p class="usaha-map-card__address">
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $usahaAlamat }}
                                </p>
                            @endif
                            @php
                                $gmapHref = $usaha->link_gmap_usaha
                                    ?: 'https://www.google.com/maps?q=' . $usaha->latitude . ',' . $usaha->longitude;
                            @endphp
                            <a href="{{ $gmapHref }}" target="_blank" rel="noopener"
                               class="usaha-map-card__cta">
                                <i class="fa-solid fa-up-right-from-square"></i>
                                Buka di Google Maps
                            </a>
                        </div>
                    @elseif ($usaha->link_gmap_usaha)
                        <a href="{{ $usaha->link_gmap_usaha }}" target="_blank" rel="noopener" class="usaha-map-card__link">
                            <div class="usaha-map-placeholder">
                                <i class="fa-solid fa-map-location-dot"></i>
                                <span>Buka di Google Maps</span>
                            </div>
                        </a>
                    @else
                        <div class="usaha-map-placeholder usaha-map-placeholder--empty">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <span>Lokasi belum tersedia</span>
                        </div>
                    @endif
                </aside>

                {{-- Gallery thumbnails (foto_tempat) --}}
                @if (!empty($gallery))
                    <div class="usaha-gallery">
                        @php $shown = array_slice($gallery, 0, 4); $more = max(count($gallery) - 4, 0); @endphp
                        @foreach ($shown as $i => $photo)
                            <a href="{{ asset('storage/'.$photo) }}" class="usaha-gallery__item" data-lightbox="usaha-gallery">
                                <img src="{{ asset('storage/'.$photo) }}"
                                     alt="Galeri {{ translate_text($usaha->nama_usaha) }} {{ $i+1 }}"
                                     onerror="this.onerror=null;this.src='{{ asset('assets/images/kategori-default.jpg') }}';">
                                @if ($i === count($shown) - 1 && $more > 0)
                                    <span class="usaha-gallery__more">+{{ $more }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ────────────── Kolom Kanan: Filter + Product Grid ────────────── --}}
            <div class="col-lg-8 col-md-12">
                {{-- Filter (reuse partial dari katalog) --}}
                <div class="usaha-filter-bar">
                    @include('partials._catalog-filters', [
                        'formAction'            => route('guest-detail-usaha', $usaha),
                        'kategoris'             => $kategoris,
                        'selectedKategoriSlugs' => $selectedKategoriSlugs,
                        'categoryGroups'        => $categoryGroups,
                        'categoryTypeLabels'    => $categoryTypeLabels,
                    ])
                </div>

                {{-- Product grid --}}
                <div class="produk-grid">
                    <div class="row">
                        @forelse ($produks as $produk)
                            <div class="col-lg-6 col-md-6 col-sm-6 mb-4">
                                <div class="product-item">
                                    <a href="{{ route('guest-singleProduct', $produk->slug) }}">
                                        <div class="thumb">
                                            <img src="{{ asset('storage/' . optional($produk->fotoProduk->first())->file_foto_produk) }}"
                                                 alt="{{ translate_text($produk->nama_produk) }}"
                                                 onerror="this.onerror=null;this.src='{{ asset('assets/images/produk-default.jpg') }}';">
                                        </div>
                                        <div class="down-content">
                                            <h4>{{ translate_text($produk->nama_produk) }}</h4>
                                            <span class="product-price">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>

                                            @include('partials._rating', [
                                                'reviews'   => $produk->reviews,
                                                'showAvg'   => true,
                                                'showCount' => true,
                                                'size'      => 'sm',
                                            ])

                                            <span class="product-shop" title="{{ translate_text($usaha->nama_usaha) }}">
                                                <i class="fa-regular fa-building"></i>{{ translate_text($usaha->nama_usaha) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center text-muted py-5">Belum ada produk yang sesuai filter dari usaha ini.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if ($produks->hasPages())
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="pagination">
                                    {{ $produks->links() }}
                                </div>
                            </div>
                        </div>
                    @endif   
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

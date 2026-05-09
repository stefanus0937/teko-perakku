@extends('guest.layouts.main')

@section('title', 'Profil Usaha - ' . $usaha->nama_usaha)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/detail-usaha.css') }}">
@endpush

@section('content')

@include('partials._rating-styles')

{{-- Breadcrumb Navigation --}}
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <nav aria-label="breadcrumb" class="product-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('guest-katalog') }}">Katalog</a></li>
                    
                    {{-- Cek apakah ada data produk sebelumnya --}}
                    @if (isset($previousProduct))
                        <li class="breadcrumb-item">
                            <a href="{{ route('guest-singleProduct', $previousProduct->slug) }}">{{ $previousProduct->nama_produk }}</a>
                        </li>
                    @endif

                    <li class="breadcrumb-item active" aria-current="page">{{ $usaha->nama_usaha }}</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<section class="detail-pengrajin-section" style="padding-top: 20px;">
    <div class="container">
        <div class="row">

            {{-- Kolom Kiri: Sidebar Profil Usaha & Daftar Pengrajin --}}
            <div class="col-lg-5 col-md-5">
                <aside class="pengrajin-sidebar">
                    <div class="profil-foto-wrapper">
                        <img src="{{ asset('storage/' . $usaha->foto_usaha) }}" 
                             alt="Logo {{ $usaha->nama_usaha }}" 
                             class="profil-foto"
                             onerror="this.onerror=null;this.src='{{ asset('assets/images/kategori-default.jpg') }}';">
                    </div>
                    <div class="profil-info">
                        <h3 class="nama-pengrajin">{{ $usaha->nama_usaha }}</h3>
                        <p class="spesialisasi">{{ $usaha->deskripsi_usaha ?? 'Kerajinan Perak Kotagede' }}</p>
                    </div>
                    <ul class="kontak-list">
                        <li><i class="fa fa-envelope"></i>{{ $usaha->email_usaha ?? '' }}</li>
                        <li><i class="fa fa-phone"></i>{{ $usaha->telp_usaha ?? '' }}</li>
                    </ul>
                    <div class="alamat-info mb-3">
                        <i class="fa fa-map-marker"></i>
                        <a href="{{ $usaha->link_gmap_usaha }}" target="_blank">{{ $usaha->link_gmap_usaha ?? 'Alamat tidak tersedia.' }}</a>
                    </div>

                    <div class="usaha-social-wrapper mb-4 pt-3" style="border-top: 1px solid #eee;">
                        <div class="social-icons-row d-flex gap-2 flex-wrap">
                            {{-- WhatsApp --}}
                            @if($usaha->link_wa_usaha || $usaha->telp_usaha)
                                @php
                                    $waNumber = $usaha->link_wa_usaha ?: $usaha->telp_usaha;
                                    $waNumber = preg_replace('/[^0-9]/', '', $waNumber);
                                    if (str_starts_with($waNumber, '0')) {
                                        $waNumber = '62' . substr($waNumber, 1);
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="social-icon whatsapp" title="WhatsApp"><i class="fa fa-phone"></i></a>
                            @endif

                            {{-- Instagram --}}
                            @if($usaha->link_instagram_usaha)
                                <a href="{{ $usaha->link_instagram_usaha }}" target="_blank" class="social-icon instagram" title="Instagram"><i class="fa fa-instagram"></i></a>
                            @endif

                            {{-- Facebook --}}
                            @if($usaha->link_facebook_usaha)
                                <a href="{{ $usaha->link_facebook_usaha }}" target="_blank" class="social-icon facebook" title="Facebook"><i class="fa fa-facebook"></i></a>
                            @endif

                            {{-- TikTok --}}
                            @if($usaha->link_tiktok_usaha)
                                <a href="{{ $usaha->link_tiktok_usaha }}" target="_blank" class="social-icon tiktok" title="TikTok"><i class="fa fa-music"></i></a>
                            @endif

                            {{-- Shopee --}}
                            @if($usaha->link_shopee_usaha)
                                <a href="{{ $usaha->link_shopee_usaha }}" target="_blank" class="social-icon shopee" title="Shopee">
                                    <img src="{{ asset('assets/images/shopee-icon.png') }}" alt="Shopee">
                                </a>
                            @endif

                            {{-- Tokopedia --}}
                            @if($usaha->link_tokopedia_usaha)
                                <a href="{{ $usaha->link_tokopedia_usaha }}" target="_blank" class="social-icon tokped" title="Tokopedia">
                                    <img src="{{ asset('assets/images/tokopedia-icon.png') }}" alt="Tokopedia">
                                </a>
                            @endif

                            {{-- Website --}}
                            @if($usaha->link_website_usaha)
                                <a href="{{ $usaha->link_website_usaha }}" target="_blank" class="social-icon website" title="Website"><i class="fa fa-globe"></i></a>
                            @endif

                            {{-- Google Maps --}}
                            @if($usaha->link_gmap_usaha)
                                <a href="{{ $usaha->link_gmap_usaha }}" target="_blank" class="social-icon maps" title="Google Maps"><i class="fa fa-map-marker"></i></a>
                            @endif

                            {{-- Email --}}
                            @if($usaha->email_usaha)
                                <a href="mailto:{{ $usaha->email_usaha }}" class="social-icon email" title="Email"><i class="fa fa-envelope"></i></a>
                            @endif
                        </div>
                    </div>

                    <div class="pengrajin-list-wrapper">
                        <h5 class="list-title">Pengrajin:</h5>
                        @forelse ($usaha->pengerajins as $pengrajin)
                            <div class="pengrajin-list-item">
                                <img src="{{ asset('storage/' . $pengrajin->foto_pengrajin) }}" 
                                     alt="{{ $pengrajin->nama_pengrajin }}" 
                                     class="pengrajin-avatar"
                                     onerror="this.onerror=null;this.src='{{ asset('assets/images/instagram-05.jpg') }}';">
                                <div class="pengrajin-contact">
                                    <span class="nama">{{ $pengrajin->nama_pengerajin }}</span>
                                    <span class="email">{{ $pengrajin->email_pengerajin }}</span>
                                </div>
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', $pengrajin->telp_pengerajin) }}" target="_blank" class="wa-button">
                                    <i class="fa fa-whatsapp"></i>
                                </a>
                            </div>
                        @empty
                            <p class="text-muted">Belum ada data pengrajin untuk usaha ini.</p>
                        @endforelse
                    </div>
                </aside>
            </div>

            {{-- Kolom Kanan: Galeri Produk dari Usaha Ini --}}
            <div class="col-lg-7 col-md-7">
                <div class="produk-grid">
                    <div class="row">
                        @forelse ($produks as $produk)
                            <div class="col-lg-6 col-md-12 col-sm-6 mb-4">
                                <div class="product-item">
                                    <a href="{{ route('guest-singleProduct', $produk->slug) }}">
                                        <div class="thumb">
                                             <img src="{{ asset('storage/' . optional($produk->fotoProduk->first())->file_foto_produk) }}" 
                                                  alt="{{ $produk->nama_produk }}"
                                                  onerror="this.onerror=null;this.src='{{ asset('assets/images/produk-default.jpg') }}';">
                                        </div>
                                        <div class="down-content">
                                            <h4>{{ $produk->nama_produk }}</h4>
                                            <span class="product-price">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>

                                            @include('partials._rating', [
                                                'reviews'   => $produk->reviews,
                                                'showAvg'   => true,
                                                'showCount' => true,
                                                'size'      => 'sm',
                                            ])

                                            {{-- Nama toko (sudah jelas konteks halaman ini, tapi konsisten dgn card lain) --}}
                                            @if(isset($usaha) && $usaha)
                                                <span class="product-shop" title="{{ $usaha->nama_usaha }}">
                                                    <i class="fa-regular fa-building"></i>{{ $usaha->nama_usaha }}
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center">Belum ada produk dari usaha ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

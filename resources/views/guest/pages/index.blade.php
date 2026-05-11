@extends('guest.layouts.main')
@section('title', 'Index')
@section('content')

@include('partials._rating-styles')

<div class="main-banner" id="top">
    <!-- Background dengan gambar asli Anda -->
    <div class="banner-background">
        <img src="{{ asset('assets/images/malioboro2.jpg') }}" alt="Keraton Yogyakarta"
             style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; z-index: -1;">
    </div>

    <!-- Banner Content di sebelah kanan -->
    <div class="banner-content">
        <h1>Perak Asli Kotagede – Warisan Seni dari Jogja</h1>
        <p>
            Karya seni perak dari Kotagede, Yogyakarta yang menggabungkan tradisi, ketelitian, dan keanggunan. Setiap detail menyimpan cerita, setiap ukiran merekam sejarah. Dibuat oleh tangan pengrajin lokal dengan teknik turun-temurun, menghadirkan keindahan otentik dengan kualitas terbaik.
        </p>
        <a href="javascript:void(0);" class="btn btn-danger btn-lg mt-3 scroll-to-produk">Beli Sekarang</a>
    </div>
</div>

<!-- Kategori Produk Section -->
<section class="categories">
    <div class="container">
        <div class="section-heading">
            <h2>Kategori Produk</h2>
            <p class="text-muted">Jelajahi koleksi berdasarkan teknik, bentuk, dan bahan pembuatan</p>
        </div>

        @php
            /*
             * Gambar kategori dikelola dari mapping ini.
             * Simpan file gambar di: public/assets/images/
             * Lalu isi value dengan nama file, contoh: 'cincin' => 'cincin-baru.jpg'.
             * Key di kiri adalah slug kategori dari database.
             */
            $categoryImageMap = [
                'aksesoris-manten' => 'aksesoris-manten.jpg',
                'cincin' => 'wedding-ring.jpg',
                'kalung-liontin' => 'bentuk_kalung.jpg',
                'gelang' => 'bentuk_gelang.jpg',
                'anting' => 'bentuk_anting.jpg',
                'bros' => 'bentuk_bros.jpg',
                'keris' => 'bentuk_keris.jpg',
                'souvenir' => 'souvenir.jpg',
                'tas' => 'kid-02.jpg',
                'ukir' => 'teknik_ukir.jpeg',
                'filigree' => 'teknik_filigree.jpg',
                'tatahan' => 'teknik_tatah.jpg',
                'cor' => 'teknik_cor.jpg',
                'perak' => 'single-product-01.jpg',
                'emas' => 'cincin-akik.jpg',
                'tembaga' => 'explore-image-01.jpg',
                'kuningan' => 'explore-image-02.jpg',
                'perunggu' => 'kategori-default.jpg',
            ];
            $categoryCarouselItems = collect($categoryTypeLabels ?? [])
                ->flatMap(fn ($label, $type) => ($categoryGroups[$type] ?? collect())->map(function ($kategori) use ($label, $type, $categoryTypeDescriptions, $categoryImageMap) {
                    return [
                        'nama' => $kategori->nama_kategori_produk,
                        'slug' => $kategori->slug,
                        'type' => $type,
                        'type_label' => $label,
                        'description' => ($categoryTypeDescriptions ?? [])[$type] ?? 'Pesona Perak',
                        'image' => $categoryImageMap[$kategori->slug] ?? $kategori->slug . '.jpg',
                    ];
                }));
        @endphp

        <div class="category-carousel-shell" data-category-carousel>
            <button type="button" class="category-carousel-nav category-carousel-prev" aria-label="Kategori sebelumnya">
                <i class="fa fa-chevron-left"></i>
            </button>

            <div class="category-carousel-track" tabindex="0">
                @foreach($categoryCarouselItems as $kategori)
                    <article class="category-slide" data-category-type="{{ $kategori['type'] }}">
                        <a href="{{ route('guest-katalog', array_merge(request()->except('page'), ['kategori' => [$kategori['slug']]])) }}"
                           class="category-slide-link">
                            <div class="category-slide-image">
                                <img src="{{ asset('assets/images/' . $kategori['image']) }}"
                                     alt="{{ $kategori['nama'] }}"
                                     onerror="this.onerror=null;this.src='{{ asset('assets/images/kategori-default.jpg') }}';">
                            </div>
                            <div class="category-slide-overlay">
                                <span class="category-slide-group">{{ $kategori['type_label'] }}</span>
                                <h3>{{ $kategori['nama'] }}</h3>
                                <p>{{ \Illuminate\Support\Str::limit($kategori['description'], 34) }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <button type="button" class="category-carousel-nav category-carousel-next" aria-label="Kategori berikutnya">
                <i class="fa fa-chevron-right"></i>
            </button>
        </div>

        <div class="category-carousel-groups" aria-label="Kelompok kategori">
            @foreach(($categoryTypeLabels ?? []) as $type => $label)
                <button type="button" class="category-group-chip" data-category-group="{{ $type }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>
</section>
    <!-- ***** Main Banner Area End ***** -->

    <!-- ***** Produk Area Starts ***** -->
<section class="products">
    <div class="container">
        <div class="section-heading">
            <h2>Produk Terbaru Kami!</h2>
            <span>Temukan Produk Terfavoritmu!</span>
        </div>
    </div>
    <div class="container">
        <div class="row">
            @foreach ($randomProduks as $produk)
                {{-- Menggunakan col-lg-3 untuk 4 item per baris --}}
                <div class="col-lg-3 col-md-6 mb-4">
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

                                {{-- Rating: bintang + jumlah review --}}
                                @include('partials._rating', [
                                    'reviews'   => $produk->reviews,
                                    'showAvg'   => true,
                                    'showCount' => true,
                                    'size'      => 'sm',
                                ])

                                {{-- Nama toko (UMKM yang menjual produk ini) --}}
                                @php $shop = $produk->usaha->first(); @endphp
                                @if($shop)
                                    <span class="product-shop" title="{{ $shop->nama_usaha }}">
                                        <i class="fa-regular fa-building"></i>{{ $shop->nama_usaha }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="col-lg-12">
            <div class="text-center mt-5">
                <a href="{{ route('guest-katalog') }}" class="see-all-button btn">Lihat Semua</a>
            </div>
        </div>
    </div>
</section>
    <!-- ***** Produk Area Ends ***** -->

<!-- ***** Usaha/Toko Section ***** -->
<section class="usaha-section">
    <div class="container">
        <div class="section-heading">
            <h2>Toko & Usaha Perak</h2>
            <span>Kunjungi toko-toko perak terbaik di Kotagede</span>
        </div>

        <div class="row">
    @foreach ($usahas as $usaha)
        <div class="col-lg-6 mb-4">

            <a href="{{ route('guest-detail-usaha', $usaha->id) }}"
               class="usaha-card-link">

                <div class="usaha-card">

                    {{-- Thumbnail --}}
                    <div class="usaha-image">
                        <img src="{{ $usaha->foto_usaha 
                            ? asset('storage/' . $usaha->foto_usaha) 
                            : asset('assets/images/kategori-default.jpg') }}"
                            alt="{{ $usaha->nama_usaha }}"
                            onerror="this.onerror=null;this.src='{{ asset('assets/images/kategori-default.jpg') }}';">
                    </div>

                    {{-- Content --}}
                    <div class="usaha-content">
                        <h4>{{ $usaha->nama_usaha }}</h4>

                        <span class="usaha-username">
                            @ {{ $usaha->user->username }}
                        </span>

                        <p>
                            {{ \Illuminate\Support\Str::limit(
                                $usaha->deskripsi_usaha ??
                                'Temukan berbagai kerajinan perak asli Kotagede dengan kualitas terbaik.',
                                85
                            ) }}
                        </p>
                    </div>

                    {{-- Button --}}
                    <div class="usaha-action">
                        <span class="btn-kunjungi">
                            Kunjungi
                        </span>
                    </div>

                </div>

            </a>

        </div>
    @endforeach
</div>

        {{-- Tombol Lihat Semua --}}
        <div class="col-lg-12">
            <div class="text-center mt-5">
                <a href="{{ route('guest-katalog') }}#related-stores" class="see-all-button btn">Lihat Semua</a>
            </div>
        </div>
    </div>
</section>

<section class="about-us">
    <div class="container">
        <div class="row align-items-center">
            {{-- Gambar akan mengambil 7 kolom di layar besar (lebih besar) --}}
            <div class="col-lg-7 col-md-12">
                <div class="about-image">
                    <img src="{{ asset('assets/images/kerajinan-perak-kota-ged.png') }}" alt="Sentra Kerajinan Perak Kotagede" class="img-fluid">
                </div>
            </div>
            {{-- Kotak teks akan mengambil 5 kolom di layar besar (lebih kecil) --}}
            <div class="col-lg-5 col-md-12">
                <div class="about-content">
                    <h3>TekoPerakku</h3>
                    <p>TekoPerakku menghadirkan kerajinan perak asli Kotagede dengan kualitas terbaik. Setiap karya diproses secara teliti oleh pengrajin berpengalaman untuk menjaga keaslian dan keindahan tradisi. Kami berkomitmen memberikan produk yang elegan, otentik, dan bernilai seni tinggi bagi setiap pelanggan.</p>
                    <a href="{{ route('guest-about') }}" class="btn btn-primary about-btn">Pelajari Lebih Lanjut</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.querySelector('.scroll-to-produk').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector('.products').scrollIntoView({
            behavior: 'smooth'
        });
    });

    document.querySelectorAll('[data-category-carousel]').forEach(function (carousel) {
        const track = carousel.querySelector('.category-carousel-track');
        const prev = carousel.querySelector('.category-carousel-prev');
        const next = carousel.querySelector('.category-carousel-next');

        const scrollByCard = function (direction) {
            const slide = track.querySelector('.category-slide');
            const amount = slide ? slide.getBoundingClientRect().width + 24 : 280;
            track.scrollBy({ left: direction * amount, behavior: 'smooth' });
        };

        prev.addEventListener('click', function () { scrollByCard(-1); });
        next.addEventListener('click', function () { scrollByCard(1); });

        document.querySelectorAll('[data-category-group]').forEach(function (button) {
            button.addEventListener('click', function () {
                const firstMatch = track.querySelector('[data-category-type="' + button.dataset.categoryGroup + '"]');
                if (firstMatch) {
                    firstMatch.scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
                }
            });
        });
    });
</script>
@endpush

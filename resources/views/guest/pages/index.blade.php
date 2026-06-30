@extends('guest.layouts.main')
@section('title', 'Index')
@section('content')

@include('partials._rating-styles')

{{-- Halaman ini adalah homepage/beranda pengunjung, dibuka dari route guest-index yang memanggil PageController@index. --}}
{{-- Data yang dipakai di halaman ini dikirim dari controller: $categoryGroups, $randomProduks, $usahas, dan $usahasWithLocation. --}}
<div class="main-banner" id="top">
    {{-- Hero utama: bagian pertama yang dilihat user, berisi gambar Kotagede sebagai latar halaman beranda. --}}
    <div class="banner-background">
        <img src="{{ asset('assets/images/Kota_Gede_Jogjakarta.jpg') }}" alt="Keraton Yogyakarta"
             style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; z-index: -1;">
    </div>

    {{-- Konten hero di sebelah kanan; tombol "Beli Sekarang" discroll oleh JavaScript ke section Produk Terbaru. --}}
    <div class="banner-content">
        <h1>@translate('Perak Asli Kotagede - Warisan Seni dari Jogja')</h1>
        <p>
            @translate('Karya seni perak dari Kotagede, Yogyakarta yang menggabungkan tradisi, ketelitian, dan keanggunan. Setiap detail menyimpan cerita, setiap ukiran merekam sejarah. Dibuat oleh tangan pengrajin lokal dengan teknik turun-temurun, menghadirkan keindahan otentik dengan kualitas terbaik.')
        </p>
        <a href="javascript:void(0);" class="btn btn-danger btn-lg mt-3 scroll-to-produk">@translate('Beli Sekarang')</a>
    </div>
</div>

{{-- Section Kategori Produk: menampilkan kategori dari database sebagai carousel kartu bergambar. --}}
<section class="categories">
    <div class="container">
        <div class="section-heading">
            <h2>@translate('Kategori Produk')</h2>
            <p class="text-muted">@translate('Jelajahi koleksi berdasarkan teknik, bentuk, dan bahan pembuatan')</p>
        </div>

        @php
            /*
             * Mapping ini menghubungkan slug kategori dari database dengan gambar yang tampil di carousel.
             * Dampak UI: kategori "Cincin" akan tampil sebagai kartu bergambar wedding-ring.jpg.
             * Jika gambar tidak ditemukan, tag <img> di bawah akan memakai kategori-default.jpg.
             */
            $categoryImageMap = [
                'aksesoris-manten' => 'aksesoris-manten.jpg',
                'cincin' => 'wedding-ring.jpg',
                'kalung-liontin' => 'bentuk_kalung.jpg',
                'gelang' => 'bentuk_gelang.jpg',
                'anting' => 'bentuk_anting.jpg',
                'bros' => 'bentuk_bros.jpg',
                'keris' => 'bentuk_keris.jpg',
                'souvenir' => 'bentuk_souvenir.jpg',
                'tas' => 'bentuk_tas.jpg',
                'ukir' => 'teknik_ukir.jpeg',
                'filigree' => 'teknik_filigree.jpg',
                'tatahan' => 'teknik_tatah.jpg',
                'cor' => 'teknik_cor.jpg',
                'perak' => 'bahan_perak.jpeg',
                'emas' => 'bahan_emas.png',
                'tembaga' => 'bahan_tembaga.jpg',
                'kuningan' => 'bahan_kuningan.jpg',
                'perunggu' => 'bahan_perunggu.jpg',
            ];
            // Mengubah data kategori per grup menjadi satu list item carousel yang siap dirender ke browser.
            // Data ini berasal dari PageController@index: $categoryGroups, $categoryTypeLabels, dan $categoryTypeDescriptions.
            $categoryCarouselItems = collect($categoryTypeLabels ?? [])
                ->flatMap(fn ($label, $type) => ($categoryGroups[$type] ?? collect())->map(function ($kategori) use ($label, $type, $categoryTypeDescriptions, $categoryImageMap) {
                    return [
                        'nama' => $kategori->translated_nama_kategori_produk,
                        'slug' => $kategori->slug,
                        'type' => $type,
                        'type_label' => translate_text($label),
                        'description' => translate_text(($categoryTypeDescriptions ?? [])[$type] ?? 'Pesona Perak'),
                        'image' => $categoryImageMap[$kategori->slug] ?? $kategori->slug . '.jpg',
                    ];
                }));
        @endphp

        {{-- Carousel kategori: user bisa menggeser kartu, lalu klik kategori untuk masuk ke katalog yang sudah terfilter. --}}
        <div class="category-carousel-shell" data-category-carousel>
            <button type="button" class="category-carousel-nav category-carousel-prev" aria-label="Kategori sebelumnya">
                <i class="fa fa-chevron-left"></i>
            </button>

            <div class="category-carousel-track" tabindex="0">
                @foreach($categoryCarouselItems as $kategori)
                    {{-- Setiap slide menjadi link ke route guest-katalog dengan query kategori[]=slug. --}}
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

        {{-- Tombol grup kategori: menggeser carousel ke kelompok Teknik Pembuatan, Bentuk Jadi, atau Bahan Pembuatan. --}}
        <div class="category-carousel-groups" aria-label="Kelompok kategori">
            @foreach(($categoryTypeLabels ?? []) as $type => $label)
                <button type="button" class="category-group-chip" data-category-group="{{ $type }}">
                    {{ translate_text($label) }}
                </button>
            @endforeach
        </div>
    </div>
</section>
    <!-- ***** Main Banner Area End ***** -->

{{-- Section Produk Terbaru: menampilkan $randomProduks dari PageController@index sebagai grid kartu produk. --}}
<section class="products">
    <div class="container">
        <div class="section-heading">
            <h2>@translate('Produk Terbaru Kami!')</h2>
            <span>@translate('Temukan Produk Terfavoritmu!')</span>
        </div>
    </div>
    <div class="container">
        <div class="row">
            @foreach ($randomProduks as $produk)
                {{-- Satu kartu produk yang terlihat di browser: gambar, nama, harga, rating, dan nama toko. --}}
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="product-item">
                        <a href="{{ route('guest-singleProduct', $produk->slug) }}">
                            <div class="thumb">
                                <img src="{{ asset('storage/' . optional($produk->fotoProduk->first())->file_foto_produk) }}"
                                    alt="{{ $produk->translated_nama_produk }}"
                                    onerror="this.onerror=null;this.src='{{ asset('assets/images/produk-default.jpg') }}';">
                            </div>
                            <div class="down-content">
                                <h4>{{ $produk->translated_nama_produk }}</h4>
                                <span class="product-price">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>

                                {{-- Komponen rating kecil: menghasilkan bintang dan jumlah review di kartu produk. --}}
                                @include('partials._rating', [
                                    'reviews'   => $produk->reviews,
                                    'showAvg'   => true,
                                    'showCount' => true,
                                    'size'      => 'sm',
                                ])

                                {{-- Nama toko (UMKM yang menjual produk ini) --}}
                                @php $shop = $produk->usaha->first(); @endphp
                                @if($shop)
                                    <span class="product-shop" title="{{ $shop->translated_nama_usaha }}">
                                        <i class="fa-regular fa-building"></i>{{ $shop->translated_nama_usaha }}
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
                {{-- Tombol ini membawa user ke halaman katalog lengkap, bukan hanya produk yang tampil di homepage. --}}
                <a href="{{ route('guest-katalog') }}" class="see-all-button btn">@translate('Lihat Semua')</a>
            </div>
        </div>
    </div>
</section>
    <!-- ***** Produk Area Ends ***** -->

{{-- Section Toko & Usaha: menampilkan $usahas dari controller sebagai kartu toko di beranda. --}}
<section class="usaha-section">
    <div class="container">
        <div class="section-heading">
            <h2>@translate('Toko & Usaha Perak')</h2>
            <span>@translate('Kunjungi toko-toko perak terbaik di Kotagede')</span>
        </div>

        <div class="row">
    @foreach ($usahas as $usaha)
        <div class="col-lg-6 mb-4">

            {{-- Klik kartu toko membawa user ke halaman detail usaha dan daftar produk dari toko tersebut. --}}
            <a href="{{ route('guest-detail-usaha', $usaha->id) }}"
               class="usaha-card-link">

                <div class="usaha-card">

                    {{-- Thumbnail toko: memakai foto usaha jika ada, kalau kosong memakai gambar default. --}}
                    <div class="usaha-image">
                        <img src="{{ $usaha->foto_usaha 
                            ? asset('storage/' . $usaha->foto_usaha) 
                            : asset('assets/images/kategori-default.jpg') }}"
                            alt="{{ $usaha->translated_nama_usaha }}"
                            onerror="this.onerror=null;this.src='{{ asset('assets/images/kategori-default.jpg') }}';">
                    </div>

                    {{-- Konten kartu toko: nama usaha, username pemilik, dan ringkasan deskripsi yang terlihat di homepage. --}}
                    <div class="usaha-content">
                        <h4>{{ $usaha->translated_nama_usaha }}</h4>

                        <span class="usaha-username">
                            @ {{ $usaha->user->username }}
                        </span>

                        <p>
                            {{ \Illuminate\Support\Str::limit(
                                $usaha->translated_deskripsi_usaha ?:
                                'Temukan berbagai kerajinan perak asli Kotagede dengan kualitas terbaik.',
                                85
                            ) }}
                        </p>
                    </div>

                    {{-- Elemen visual seperti tombol; seluruh kartu tetap menjadi link ke detail usaha. --}}
                    <div class="usaha-action">
                        <span class="btn-kunjungi">
                            @translate('Kunjungi')
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
                {{-- Mengarahkan user ke bagian toko terkait di halaman katalog. --}}
                <a href="{{ route('guest-katalog') }}#related-stores" class="see-all-button btn">@translate('Lihat Semua')</a>
            </div>
        </div>
    </div>
</section>

{{-- ── Peta Toko Perak (multi-marker eksplorasi) ── --}}
{{-- Peta Toko Perak: hanya muncul jika ada usaha yang memiliki latitude dan longitude. --}}
@if (isset($usahasWithLocation) && $usahasWithLocation->isNotEmpty())
    <section class="usaha-map-section">
        <div class="container">
            <div class="section-heading">
                <h2>@translate('Peta Toko Perak Kotagede')</h2>
                <span>Jelajahi lokasi toko-toko perak di peta — klik marker untuk melihat detail.</span>
            </div>
            {{-- Partial ini merender peta interaktif dengan marker toko dari $usahasWithLocation. --}}
            @include('partials._usaha-map', [
                'usahas' => $usahasWithLocation,
                'mapId'  => 'usaha-map-home',
                'height' => '460px',
            ])
        </div>
    </section>
@endif

{{-- Section About ringkas: memperkenalkan TekoPerakku dan mengarahkan user ke halaman Tentang Kami. --}}
<section class="about-us">
    <div class="container">
        <div class="row align-items-center">
            {{-- Gambar pendukung brand/cerita Kotagede di bagian bawah homepage. --}}
            <div class="col-lg-7 col-md-12">
                <div class="about-image">
                    <img src="{{ asset('assets/images/kerajinan-perak-kota-ged.png') }}" alt="Sentra Kerajinan Perak Kotagede" class="img-fluid">
                </div>
            </div>
            {{-- Ringkasan profil website; tombolnya menuju halaman about untuk informasi yang lebih lengkap. --}}
            <div class="col-lg-5 col-md-12">
                <div class="about-content">
                    <h3>TekoPerakku</h3>
                    <p>@translate('TekoPerakku menghadirkan kerajinan perak asli Kotagede dengan kualitas terbaik. Setiap karya diproses secara teliti oleh pengrajin berpengalaman untuk menjaga keaslian dan keindahan tradisi. Kami berkomitmen memberikan produk yang elegan, otentik, dan bernilai seni tinggi bagi setiap pelanggan.')</p>
                    <a href="{{ route('guest-about') }}" class="btn btn-primary about-btn">@translate('Pelajari Lebih Lanjut')</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Ketika tombol "Beli Sekarang" diklik, halaman scroll halus ke grid Produk Terbaru.
    document.querySelector('.scroll-to-produk').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector('.products').scrollIntoView({
            behavior: 'smooth'
        });
    });

    // Mengaktifkan carousel kategori: tombol panah menggeser kartu, chip grup lompat ke kelompok kategori tertentu.
    document.querySelectorAll('[data-category-carousel]').forEach(function (carousel) {
        const track = carousel.querySelector('.category-carousel-track');
        const prev = carousel.querySelector('.category-carousel-prev');
        const next = carousel.querySelector('.category-carousel-next');

        // Lebar scroll mengikuti ukuran kartu pertama supaya perpindahan carousel terasa rapi di berbagai layar.
        const scrollByCard = function (direction) {
            const slide = track.querySelector('.category-slide');
            const amount = slide ? slide.getBoundingClientRect().width + 24 : 280;
            track.scrollBy({ left: direction * amount, behavior: 'smooth' });
        };

        prev.addEventListener('click', function () { scrollByCard(-1); });
        next.addEventListener('click', function () { scrollByCard(1); });

        // Chip grup mencari slide pertama dari tipe kategori yang sama lalu menggulir carousel ke posisi tersebut.
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


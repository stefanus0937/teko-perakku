@extends('guest.layouts.main')
@section('title', 'Berbagai Macam Produk')
@section('content')

@include('partials._rating-styles')

    <!-- ***** Products Area Starts ***** -->
    <section class="section guest-content-start" id="products">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-heading">
                        <h2>Produk Terbaru Kami!</h2>
                        <span>Temukan produk yang kamu suka!</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                @if ($produks->count() > 0)
                    @foreach ($produks as $produk)
                        <div class="col-lg-4">
                            <div class="item">
                                <div class="thumb">
                                    <div class="hover-content">
                                        <ul>
                                            <li><a href="{{ route('guest-singleProduct', $produk->slug) }}">
                                                <i class="fa fa-eye"></i></a>
                                            </li>
                                            <li><a href=""><i class="fa fa-star"></i></a></li>
                                            <li><a href=""><i class="fa fa-shopping-cart"></i></a></li>
                                        </ul>
                                    </div>
                                    <img src="{{ asset('storage/' . $produk->fotoProduk->first()->file_foto_produk) }}"
                                        alt="{{ $produk->nama_produk }}">
                                </div>
                                <div class="down-content">
                                    <h4>{{ $produk->nama_produk }}</h4>
                                    <span>Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>

                                    @include('partials._rating', [
                                        'reviews'   => $produk->reviews,
                                        'showAvg'   => true,
                                        'showCount' => true,
                                        'size'      => 'sm',
                                    ])

                                    @php $shop = $produk->usaha->first(); @endphp
                                    @if($shop)
                                        <span class="product-shop" title="{{ $shop->nama_usaha }}">
                                            <i class="fa-regular fa-building"></i>{{ $shop->nama_usaha }}
                                        </span>
                                    @endif

                                    <p>{{ \Illuminate\Support\Str::limit($produk->deskripsi, 80) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center">
                        <h5>Produk belum tersedia saat ini.</h5>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <!-- ***** Products Area Ends ***** -->
@endsection

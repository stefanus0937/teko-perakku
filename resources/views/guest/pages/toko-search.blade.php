@extends('guest.layouts.main')
@section('title', 'Hasil Pencarian Toko')

@include('partials._usaha-card-styles')

@section('content')

{{-- Breadcrumb (sama gaya dgn single-product & detail-usaha) --}}
<div class="container guest-content-start">
    <div class="row">
        <div class="col-lg-12">
            <nav aria-label="breadcrumb" class="product-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('guest-katalog', ['search' => $searchTerm]) }}">Katalog</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Halaman Toko</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        {{-- Header --}}
        <div class="related-usaha-section" style="margin-top: 0;">
            <div class="related-usaha-header">
                <div>
                    <h3 class="related-usaha-title">Toko</h3>
                    @if ($searchTerm)
                        <p class="related-usaha-sub">
                            Toko berkaitan dengan "<em>{{ $searchTerm }}</em>"
                        </p>
                    @else
                        <p class="related-usaha-sub">Semua toko</p>
                    @endif
                </div>
                <a href="{{ route('guest-katalog', ['search' => $searchTerm]) }}"
                   class="toko-lain-btn">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="row">
                @forelse ($usahas as $usaha)
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
                @empty
                    <div class="col-12 text-center py-5">
                        <p>Tidak ada toko yang cocok dengan pencarian Anda.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="pagination">
                        {{ $usahas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('guest.layouts.main')
@section('title', 'Katalog')
@section('content')

@include('partials._rating-styles')

<section class="section guest-content-start" id="products">
    <div class="container">
        <div class="row mb-5">
            @php
                $selectedKategoriSlugs = collect($selectedKategoriSlugs ?? request()->input('kategori', []))
                    ->when(is_string(request()->input('kategori')), fn ($collection) => collect([request()->input('kategori')]))
                    ->filter()
                    ->unique()
                    ->values();
                $selectedKategoriItems = $kategoris->whereIn('slug', $selectedKategoriSlugs);
                $kategoriButtonText = $selectedKategoriItems->isEmpty()
                    ? 'Semua Produk'
                    : ($selectedKategoriItems->count() === 1
                        ? $selectedKategoriItems->first()->nama_kategori_produk
                        : $selectedKategoriItems->count() . ' kategori dipilih');
            @endphp
            @if (request()->filled('search'))
                <div class="col-lg-12 search-heading">
                    <h2 class="search-title">Hasil Pencarian</h2>
                    {{-- Menampilkan jumlah hasil dan kata kunci pencarian secara dinamis --}}
                    <p class="result-count">
                        Menampilkan {{ $produks->firstItem() }} - {{ $produks->lastItem() }} dari {{ $produks->total() }} hasil untuk "<span class="search-term">{{ request('search') }}</span>"
                    </p>
                </div>
            @endif
            {{-- Filter component shared dengan detail-usaha --}}
            @include('partials._catalog-filters', [
                'formAction'            => route('guest-katalog'),
                'kategoris'             => $kategoris,
                'selectedKategoriSlugs' => $selectedKategoriSlugs,
                'categoryGroups'        => $categoryGroups ?? collect(),
                'categoryTypeLabels'    => $categoryTypeLabels ?? [],
            ])
        </div>
        <!-- -- Akhir Bagian Filter Pencarian -- -->

        <div class="row">
            @forelse ($produks as $produk)
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

                            {{-- Nama toko --}}
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
            @empty
                {{-- Pesan jika tidak ada produk yang ditemukan --}}
                <div class="col-12 text-center">
                    <p>Produk tidak ditemukan.</p>
                </div>
            @endforelse
        </div>

        {{-- Bagian Paginasi Baru --}}
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="pagination">
                    {{-- Ini akan otomatis membuat link paginasi dari Laravel --}}
                    {{ $produks->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cari form spesifik yang berisi filter
        const filterForm = document.querySelector('form[action="{{ route('guest-katalog') }}"]');

        // PENTING: Lanjutkan hanya jika form-nya ditemukan
        if (filterForm) {
            const filters = filterForm.querySelectorAll('select');

            filters.forEach(function(select) {
                select.addEventListener('change', function() {
                    filterForm.submit();
                });
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
    const categoryChecks = document.querySelectorAll('.category-check input');

    categoryChecks.forEach(input => {
        input.addEventListener('change', function () {
            this.closest('.category-check')
                .classList.toggle('is-active', this.checked);
        });
    });
});

</script>
@endpush

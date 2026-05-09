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
            <form action="{{ route('guest-katalog') }}" method="GET" class="w-100">
                @if (request()->filled('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <div class="filters-wrapper">
                    <div class="filter-row">
                    {{-- Filter Kategori --}}
                        <div class="filter-group-custom filter-group-category">
                            <label for="kategoriDropdown">Kategori:</label>
                            <div class="dropdown">
                                <button class="form-select-custom dropdown-toggle {{ $selectedKategoriItems->isNotEmpty() ? 'filter-active' : '' }}" type="button" id="kategoriDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    {{ $kategoriButtonText }}
                                </button>
                                <div class="dropdown-menu category-filter-menu" aria-labelledby="kategoriDropdown">
                                    <div class="category-filter-grid">
                                        @foreach (($categoryTypeLabels ?? []) as $type => $label)
                                            <div class="category-filter-column">
                                                <h6>{{ $label }}</h6>
                                            @foreach (($categoryGroups[$type] ?? collect()) as $kategori)
                                                <label class="category-check {{ $selectedKategoriSlugs->contains($kategori->slug) ? 'is-active' : '' }}">
                                                    <input type="checkbox"
                                                           name="kategori[]"
                                                           value="{{ $kategori->slug }}"
                                                           {{ $selectedKategoriSlugs->contains($kategori->slug) ? 'checked' : '' }}>
                                                    <span class="category-check-box"></span>
                                                    <span>{{ $kategori->nama_kategori_produk }}</span>
                                                </label>
                                            @endforeach
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="category-filter-actions">
                                        <a href="{{ route('guest-katalog', request()->except(['kategori', 'page'])) }}" class="btn-filter-clear">Reset</a>
                                        <button type="submit" class="btn-filter-apply">Terapkan</button>
                                    </div>
                                </div>
                            </div>
                            @if($selectedKategoriItems->isNotEmpty())
                                <div class="active-filter-tags">
                                    @foreach($selectedKategoriItems as $kategori)
                                        @php
                                            $remainingKategori = $selectedKategoriSlugs->reject(fn ($slug) => $slug === $kategori->slug)->values()->all();
                                            $removeQuery = request()->except(['kategori', 'page']);
                                            if (!empty($remainingKategori)) {
                                                $removeQuery['kategori'] = $remainingKategori;
                                            }
                                        @endphp
                                        <a href="{{ route('guest-katalog', $removeQuery) }}" class="active-filter-tag">
                                            {{ $kategori->nama_kategori_produk }}
                                            <span aria-hidden="true">&times;</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="filter-group-custom filter-group-dropdown">
                            <label for="harga-dropdown">Harga:</label>
                            <div class="dropdown w-100">
                                <button class="btn-dropdown-custom {{ (request('min_harga') || request('max_harga')) ? 'filter-active' : '' }}" type="button" id="harga-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    @if (request('min_harga') && request('max_harga'))
                                        Rp {{ number_format(request('min_harga'), 0, ',', '.') }} - Rp {{ number_format(request('max_harga'), 0, ',', '.') }}
                                    @elseif (request('min_harga'))
                                        Diatas Rp {{ number_format(request('min_harga'), 0, ',', '.') }}
                                    @elseif (request('max_harga'))
                                        Dibawah Rp {{ number_format(request('max_harga'), 0, ',', '.') }}
                                    @else
                                        Semua Harga
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-custom" aria-labelledby="harga-dropdown">
                                    <div class="price-range-form-new">
                                        <div class="price-inputs-wrapper">
                                            <div class="price-input-group-new">
                                                <label for="min_harga" class="price-label-new">Min</label>
                                                <input type="number"
                                                    class="price-input"
                                                    name="min_harga"
                                                    id="min_harga"
                                                    placeholder="100.000"
                                                    value="{{ request('min_harga') }}">
                                            </div>

                                            <span class="price-separator">-</span>

                                            <div class="price-input-group-new">
                                                <label for="max_harga" class="price-label-new">Maks</label>
                                                <input type="number"
                                                    class="price-input"
                                                    name="max_harga"
                                                    id="max_harga"
                                                    placeholder="1.000.000"
                                                    value="{{ request('max_harga') }}">
                                            </div>
                                        </div>

                                        @if (request('urutkan'))
                                            <input type="hidden"
                                                name="urutkan"
                                                value="{{ request('urutkan') }}">
                                        @endif

                                        <div class="price-buttons-wrapper">
                                            <button type="submit" class="btn-apply-new">
                                                Terapkan
                                            </button>

                                            <a href="{{ url()->current() }}?{{ http_build_query(request()->except(['min_harga', 'max_harga'])) }}"
                                            class="btn-reset-new">
                                                Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="filter-group-custom">
                        <label for="urutkanDropdown">Urut Berdasarkan:</label>
                            @php
                            $opsiUrutkan = [
                                'terbaru' => 'Produk Terbaru',
                                'populer' => 'Popularitas',
                                'harga-rendah' => 'Harga Terendah',
                                'harga-tinggi' => 'Harga Tertinggi',
                            ];
                            $urutkanAktif = request('urutkan', 'terbaru');
                            $namaUrutkanAktif = $opsiUrutkan[$urutkanAktif] ?? 'Produk Terbaru';
                        @endphp

                        <div class="dropdown">
                            <button class="form-select-custom dropdown-toggle {{ request()->input('urutkan', 'terbaru') != 'terbaru' ? 'filter-active' : '' }}" type="button" id="urutkanDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $namaUrutkanAktif }}
                            </button>

                            <ul class="dropdown-menu" aria-labelledby="urutkanDropdown">
                                
                                @foreach ($opsiUrutkan as $value => $text)
                                <li>
                                    <a class="dropdown-item {{ $urutkanAktif == $value ? 'active' : '' }}"
                                    href="{{ route('guest-katalog', array_merge(request()->except(['page', 'urutkan']), ['urutkan' => $value])) }}">
                                        {{ $text }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
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

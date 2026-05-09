{{--
    Reusable Catalog Filter Component
    =================================
    Pemakaian:
        @include('partials._catalog-filters', [
            'formAction'       => route('guest-katalog'),
            'kategoris'        => $kategoris,
            'selectedKategoriSlugs' => $selectedKategoriSlugs ?? [],
            'categoryGroups'   => $categoryGroups ?? collect(),
            'categoryTypeLabels' => $categoryTypeLabels ?? [],
            'showSort'         => true,        // sembunyikan jika perlu
            'extraHidden'      => [],          // array of [name => value] untuk hidden inputs (mis. usaha id)
        ])

    Pakai class CSS yang sama persis dengan filter di katalog (filter-row, filter-group-custom,
    dropdown-menu category-filter-menu, dll.) — tidak ada duplikasi style.
--}}
@php
    $formAction = $formAction ?? route('guest-katalog');
    $extraHidden = $extraHidden ?? [];
    $showSort = $showSort ?? true;

    // Normalize selected slugs ke Collection
    $selectedKategoriSlugs = collect($selectedKategoriSlugs ?? request()->input('kategori', []))
        ->when(is_string(request()->input('kategori')), fn ($c) => collect([request()->input('kategori')]))
        ->filter()
        ->unique()
        ->values();

    $selectedKategoriItems = ($kategoris ?? collect())->whereIn('slug', $selectedKategoriSlugs);
    $kategoriButtonText = $selectedKategoriItems->isEmpty()
        ? 'Semua Produk'
        : ($selectedKategoriItems->count() === 1
            ? $selectedKategoriItems->first()->nama_kategori_produk
            : $selectedKategoriItems->count() . ' kategori dipilih');

    $opsiUrutkan = [
        'terbaru'      => 'Produk Terbaru',
        'populer'      => 'Popularitas',
        'harga-rendah' => 'Harga Terendah',
        'harga-tinggi' => 'Harga Tertinggi',
    ];
    $urutkanAktif    = request('urutkan', 'terbaru');
    $namaUrutkanAktif = $opsiUrutkan[$urutkanAktif] ?? 'Produk Terbaru';
@endphp

<form action="{{ $formAction }}" method="GET" class="w-100 catalog-filter-form">
    {{-- Hidden inputs untuk preserve params yang tidak terkait filter --}}
    @if (request()->filled('search'))
        <input type="hidden" name="search" value="{{ request('search') }}">
    @endif
    @foreach ($extraHidden as $name => $value)
        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
    @endforeach

    <div class="filters-wrapper">
        <div class="filter-row">

            {{-- Filter Kategori --}}
            <div class="filter-group-custom filter-group-category">
                <label for="kategoriDropdown">Kategori:</label>
                <div class="dropdown">
                    <button class="form-select-custom dropdown-toggle {{ $selectedKategoriItems->isNotEmpty() ? 'filter-active' : '' }}"
                            type="button" id="kategoriDropdown"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside"
                            aria-expanded="false">
                        {{ $kategoriButtonText }}
                    </button>
                    <div class="dropdown-menu category-filter-menu" aria-labelledby="kategoriDropdown">
                        <div class="category-filter-grid">
                            @foreach (($categoryTypeLabels ?? []) as $type => $label)
                                <div class="category-filter-column">
                                    <h6>{{ $label }}</h6>
                                    @foreach (($categoryGroups[$type] ?? collect()) as $kat)
                                        <label class="category-check {{ $selectedKategoriSlugs->contains($kat->slug) ? 'is-active' : '' }}">
                                            <input type="checkbox" name="kategori[]"
                                                   value="{{ $kat->slug }}"
                                                   {{ $selectedKategoriSlugs->contains($kat->slug) ? 'checked' : '' }}>
                                            <span class="category-check-box"></span>
                                            <span>{{ $kat->nama_kategori_produk }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <div class="category-filter-actions">
                            <a href="{{ $formAction . '?' . http_build_query(array_merge($extraHidden, request()->except(['kategori', 'page']))) }}"
                               class="btn-filter-clear">Reset</a>
                            <button type="submit" class="btn-filter-apply">Terapkan</button>
                        </div>
                    </div>
                </div>

                @if ($selectedKategoriItems->isNotEmpty())
                    <div class="active-filter-tags">
                        @foreach ($selectedKategoriItems as $kat)
                            @php
                                $remaining = $selectedKategoriSlugs->reject(fn ($s) => $s === $kat->slug)->values()->all();
                                $removeQuery = array_merge($extraHidden, request()->except(['kategori', 'page']));
                                if (!empty($remaining)) $removeQuery['kategori'] = $remaining;
                            @endphp
                            <a href="{{ $formAction . '?' . http_build_query($removeQuery) }}" class="active-filter-tag">
                                {{ $kat->nama_kategori_produk }}
                                <span aria-hidden="true">&times;</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Filter Harga --}}
            <div class="filter-group-custom filter-group-dropdown">
                <label for="harga-dropdown">Harga:</label>
                <div class="dropdown w-100">
                    <button class="btn-dropdown-custom {{ (request('min_harga') || request('max_harga')) ? 'filter-active' : '' }}"
                            type="button" id="harga-dropdown"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside"
                            aria-expanded="false">
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
                                    <input type="number" class="price-input"
                                           name="min_harga" id="min_harga"
                                           placeholder="100.000"
                                           value="{{ request('min_harga') }}">
                                </div>
                                <span class="price-separator">-</span>
                                <div class="price-input-group-new">
                                    <label for="max_harga" class="price-label-new">Maks</label>
                                    <input type="number" class="price-input"
                                           name="max_harga" id="max_harga"
                                           placeholder="1.000.000"
                                           value="{{ request('max_harga') }}">
                                </div>
                            </div>

                            @if (request('urutkan'))
                                <input type="hidden" name="urutkan" value="{{ request('urutkan') }}">
                            @endif

                            <div class="price-buttons-wrapper">
                                <button type="submit" class="btn-apply-new">Terapkan</button>
                                <a href="{{ $formAction . '?' . http_build_query(array_merge($extraHidden, request()->except(['min_harga', 'max_harga']))) }}"
                                   class="btn-reset-new">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sort dropdown --}}
        @if ($showSort)
            <div class="filter-group-custom">
                <label for="urutkanDropdown">Urut Berdasarkan:</label>
                <div class="dropdown">
                    <button class="form-select-custom dropdown-toggle {{ $urutkanAktif != 'terbaru' ? 'filter-active' : '' }}"
                            type="button" id="urutkanDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        {{ $namaUrutkanAktif }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="urutkanDropdown">
                        @foreach ($opsiUrutkan as $value => $text)
                            <li>
                                <a class="dropdown-item {{ $urutkanAktif == $value ? 'active' : '' }}"
                                   href="{{ $formAction . '?' . http_build_query(array_merge($extraHidden, request()->except(['page', 'urutkan']), ['urutkan' => $value])) }}">
                                    {{ $text }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</form>

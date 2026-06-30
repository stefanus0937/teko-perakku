@php
    $usahaImg = !empty($usaha->foto_usaha)
        ? asset('storage/' . $usaha->foto_usaha)
        : asset('assets/images/kerajinan-perak-kota-ged.png');
    $handle = '@' . \Illuminate\Support\Str::slug(optional($usaha->user)->username ?? $usaha->nama_usaha, '');
    $deskripsi = $usaha->deskripsi_usaha ?? $usaha->spesialisasi_usaha ?? '';
@endphp
<div class="usaha-search-card d-flex">
    <div class="usaha-search-card__thumb">
        <img src="{{ $usahaImg }}"
             alt="{{ translate_text($usaha->nama_usaha) }}"
             onerror="this.onerror=null;this.src='{{ asset('assets/images/kerajinan-perak-kota-ged.png') }}';">
    </div>
    <div class="usaha-search-card__body">
        <div class="usaha-search-card__head">
            <h5 class="usaha-search-card__name mb-1">{{ translate_text($usaha->nama_usaha) }}</h5>
            <span class="usaha-search-card__handle">{{ $handle }}</span>
        </div>
        <p class="usaha-search-card__desc">
            {{ \Illuminate\Support\Str::limit(translate_text($deskripsi), 80) }}
        </p>
        <a href="{{ route('guest-detail-usaha', $usaha->id) }}" class="usaha-search-card__cta">
            kunjungi
        </a>
    </div>
</div>

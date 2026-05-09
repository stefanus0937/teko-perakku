{{--
    Reusable rating display
    Pemakaian:
        @include('partials._rating', ['reviews' => $produk->reviews])
        @include('partials._rating', ['reviews' => $produk->reviews, 'showCount' => true, 'showAvg' => true])

    Parameter:
        $reviews   — Collection (atau hasMany) review milik produk; akan di-avg('rating') & ->count()
        $showCount — bool, default true; tampilkan "(N)" / "(N reviews)"
        $showAvg   — bool, default false; tampilkan angka avg di depan bintang
        $size      — string, default 'sm' (sm|md|lg) — ukuran bintang
        $compact   — bool, default false; jika true, hanya angka tanpa bintang (untuk tempat sempit)
--}}
@php
    $reviews   = $reviews   ?? collect();
    $showCount = $showCount ?? true;
    $showAvg   = $showAvg   ?? false;
    $size      = $size      ?? 'sm';
    $compact   = $compact   ?? false;

    $count = is_countable($reviews) ? count($reviews) : 0;
    $avg   = $count > 0 ? round($reviews->avg('rating'), 1) : 0;

    // Untuk render bintang: bulat ke 0.5 terdekat agar half-star akurat
    $halfRounded = round($avg * 2) / 2;
@endphp

<div class="rating rating--{{ $size }}" aria-label="Rating {{ $avg }} dari 5 ({{ $count }} ulasan)">
    @unless($compact)
        <span class="rating__stars" aria-hidden="true">
            @for($i = 1; $i <= 5; $i++)
                @if($halfRounded >= $i)
                    {{-- Bintang penuh --}}
                    <i class="fa-solid fa-star"></i>
                @elseif($halfRounded >= $i - 0.5)
                    {{-- Bintang setengah --}}
                    <i class="fa-solid fa-star-half-stroke"></i>
                @else
                    {{-- Bintang kosong (outline) --}}
                    <i class="fa-regular fa-star"></i>
                @endif
            @endfor
        </span>
    @endunless

    @if($showAvg && $count > 0)
        <span class="rating__avg">{{ number_format($avg, 1) }}</span>
    @endif

    @if($showCount)
        <span class="rating__count">
            @if($count > 0)
                {{ $count }} {{ $count === 1 ? 'Review' : 'Review' }}
            @else
                Belum ada review
            @endif
        </span>
    @endif
</div>

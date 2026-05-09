{{--
    Style untuk rating + nama toko di product card.
    Include sekali per halaman (idempotent — pakai @once).
--}}
@once
<style>
/* ──────── Rating ──────── */
.rating {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    line-height: 1;
    font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
    color: #6b7280;
    flex-wrap: wrap;
}

.rating__stars {
    display: inline-flex;
    align-items: center;
    gap: 1px;
    color: #f5b301;          /* warna bintang amber */
}

.rating__stars i {
    font-size: 13px;
    line-height: 1;
}

.rating__avg {
    font-weight: 700;
    color: #18181b;
    font-size: 13px;
}

.rating__count {
    font-size: 12.5px;
    color: #71717a;
}

/* size variants */
.rating--sm .rating__stars i { font-size: 12px; }
.rating--sm .rating__avg     { font-size: 12.5px; }
.rating--sm .rating__count   { font-size: 12px; }

.rating--md .rating__stars i { font-size: 15px; }
.rating--md .rating__avg     { font-size: 14px; }
.rating--md .rating__count   { font-size: 13.5px; }

.rating--lg .rating__stars i { font-size: 18px; }
.rating--lg .rating__avg     { font-size: 16px; }
.rating--lg .rating__count   { font-size: 14px; }

/* ──────── Nama toko di product card ──────── */
.product-shop {
    display: block;
    margin-top: 4px;
    font-size: 12.5px;
    color: #71717a;
    font-weight: 500;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.product-shop i {
    margin-right: 5px;
    color: #a1a1aa;
    font-size: 11px;
}

/* ──────── Sembunyikan stars ul lama (legacy) ──────── */
/* Aturan ini cukup defensif — bila ada .stars ul lama tertinggal, tidak overlap dengan rating baru */
.product-item .stars {
    list-style: none;
    padding: 0;
    margin: 0;
}
.product-item .stars li {
    display: inline-block;
}

/* ──────── Layout rapi di .down-content ──────── */
.product-item .down-content .rating {
    margin-top: 6px;
}
.product-item .down-content .product-shop {
    margin-top: 6px;
}

/* Mobile tweaks */
@media (max-width: 480px) {
    .rating__stars i  { font-size: 11px; }
    .rating__count    { font-size: 11.5px; }
    .product-shop     { font-size: 12px; }
}
</style>
@endonce

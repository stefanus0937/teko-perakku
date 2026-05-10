@once
@push('styles')
<style>
    .related-usaha-section { margin-top: 1.75rem; margin-bottom: 4.5rem; scroll-margin-top: 90px; }
    /* Tighter pagination spacing on katalog */
    .katalog-pagination { margin-top: .25rem; margin-bottom: .25rem; padding: .25rem 0; }
    .katalog-pagination .pagination,
    .katalog-pagination ul.pagination { margin-bottom: 0; }
    .related-usaha-section .related-usaha-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;
    }
    .related-usaha-section .related-usaha-title {
        font-weight: 700; font-size: 1.5rem; margin: 0;
    }
    .related-usaha-section .related-usaha-sub {
        font-size: .9rem; color: #6b7280; margin: 0;
    }
    .related-usaha-section .related-usaha-sub em {
        font-weight: 600; font-style: italic; color: #111;
    }
    .related-usaha-section .toko-lain-btn {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .55rem 1.1rem; border: 1px solid #d1d5db; border-radius: 10px;
        background: #fff; color: #111; font-weight: 600; font-size: .9rem;
        text-decoration: none; transition: all .2s;
    }
    .related-usaha-section .toko-lain-btn:hover {
        background: #f9fafb; border-color: #9ca3af; color: #111;
    }

    .usaha-search-card {
        background: #fff; border-radius: 14px; overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        height: 100%; min-height: 130px;
        transition: transform .2s, box-shadow .2s;
    }
    .usaha-search-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,.10);
    }
    .usaha-search-card__thumb {
        flex: 0 0 140px; width: 140px; overflow: hidden;
    }
    .usaha-search-card__thumb img {
        width: 100%; height: 100%; object-fit: cover; display: block;
    }
    .usaha-search-card__body {
        flex: 1; padding: 1rem 1.1rem; display: flex; flex-direction: column;
        justify-content: space-between; position: relative; min-width: 0;
    }
    .usaha-search-card__name {
        font-weight: 700; font-size: 1.05rem; color: #111;
    }
    .usaha-search-card__handle {
        font-size: .82rem; color: #6b7280;
    }
    .usaha-search-card__desc {
        font-size: .85rem; color: #4b5563; margin: .35rem 0 0;
        line-height: 1.45;
    }
    .usaha-search-card__cta {
        position: absolute; right: 1rem; bottom: 1rem;
        font-size: .8rem; padding: .35rem .9rem;
        border: 1px solid #d1d5db; border-radius: 8px;
        background: #fff; color: #111; text-decoration: none;
        transition: background .15s;
    }
    .usaha-search-card__cta:hover {
        background: #f3f4f6; color: #111;
    }

    @media (max-width: 575.98px) {
        .usaha-search-card__thumb { flex-basis: 110px; width: 110px; }
        .usaha-search-card__cta { position: static; align-self: flex-start; margin-top: .6rem; }
    }

    /* Dark mode */
    body.dark-mode .usaha-search-card,
    .dark-mode .usaha-search-card {
        background: #1f2937; box-shadow: 0 2px 12px rgba(0,0,0,.4);
    }
    body.dark-mode .usaha-search-card__name,
    .dark-mode .usaha-search-card__name { color: #f9fafb; }
    body.dark-mode .usaha-search-card__handle,
    body.dark-mode .usaha-search-card__desc,
    .dark-mode .usaha-search-card__handle,
    .dark-mode .usaha-search-card__desc { color: #d1d5db; }
    body.dark-mode .usaha-search-card__cta,
    .dark-mode .usaha-search-card__cta {
        background: #374151; color: #f9fafb; border-color: #4b5563;
    }
    body.dark-mode .related-usaha-section .toko-lain-btn,
    .dark-mode .related-usaha-section .toko-lain-btn {
        background: #1f2937; color: #f9fafb; border-color: #4b5563;
    }
    body.dark-mode .related-usaha-section .related-usaha-title,
    .dark-mode .related-usaha-section .related-usaha-title { color: #f9fafb; }
</style>
@endpush
@endonce

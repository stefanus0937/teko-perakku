<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Usaha;
use App\Models\FotoProduk;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $kategoris = KategoriProduk::ordered()->get();
        $categoryGroups = $kategoris->where('sort_order', '>', 0)->groupBy('category_type');
        $randomProduks = Produk::with(['fotoProduk', 'reviews'])->latest()->take(8)->get();
        $pengerajins = \App\Models\Pengerajin::inRandomOrder()->take(8)->get();
        $usahas = \App\Models\Usaha::inRandomOrder()->take(6)->get();

        // Semua usaha yang punya koordinat — utk peta eksplorasi di homepage.
        // Eager-load user agar @username di popup tidak N+1.
        $usahasWithLocation = \App\Models\Usaha::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['user:id,username'])
            ->get(['id', 'nama_usaha', 'foto_usaha', 'deskripsi_usaha', 'latitude', 'longitude', 'user_id']);

        return view('guest.pages.index', [
            'kategoris' => $kategoris,
            'categoryGroups' => $categoryGroups,
            'categoryTypeLabels' => KategoriProduk::TYPE_LABELS,
            'categoryTypeDescriptions' => KategoriProduk::TYPE_DESCRIPTIONS,
            'randomProduks' => $randomProduks,
            'pengerajins' => $pengerajins,
            'usahas' => $usahas,
            'usahasWithLocation' => $usahasWithLocation,
        ]);
    }

    public function productsByCategory($slug)
    {
        $kategori = KategoriProduk::where('slug', $slug)->firstOrFail();
        $produks = Produk::whereHas('kategoriProduk', function ($q) use ($kategori) {
            $q->where('kategori_produk_id', $kategori->id);
        })->get();

        return view('guest.pages.products', [
            'kategori' => $kategori,
            'produks' => $produks,
        ]);
    }

    public function katalog(Request $request)
    {
        // Memuat relasi yang dibutuhkan untuk efisiensi
        $query = Produk::with(['kategoriProduk', 'fotoProduk', 'reviews']);

        // -- LOGIKA PENCARIAN (SEARCH) --
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama_produk', 'like', '%' . $searchTerm . '%')
                ->orWhere('deskripsi', 'like', '%' . $searchTerm . '%')
                ->orWhereHas('kategoriProduk', function($kategoriQuery) use ($searchTerm) {
                    $kategoriQuery->where('nama_kategori_produk', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        // -- LOGIKA FILTER KATEGORI --
        $selectedKategoriSlugs = collect($request->input('kategori', []))
            ->when(is_string($request->input('kategori')), fn ($collection) => collect([$request->input('kategori')]))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!empty($selectedKategoriSlugs)) {
            $selectedKategoriGroups = KategoriProduk::query()
                ->whereIn('slug', $selectedKategoriSlugs)
                ->get(['slug', 'category_type'])
                ->groupBy('category_type');

            foreach ($selectedKategoriGroups as $groupedCategories) {
                $groupSlugs = $groupedCategories->pluck('slug')->all();

                $query->whereHas('kategoriProduk', function ($q) use ($groupSlugs) {
                    $q->whereIn('slug', $groupSlugs);
                });
            }
        }

        // -- LOGIKA FILTER HARGA --
        if ($request->filled('min_harga')) {
            $query->where('harga', '>=', $request->min_harga);
        }
        if ($request->filled('max_harga')) {
            $query->where('harga', '<=', $request->max_harga);
        }

        // -- LOGIKA PENGURUTAN (SORT) --
        $urutkan = $request->input('urutkan', 'terbaru'); 
        switch ($urutkan) {
            case 'harga-rendah':
                $query->orderBy('harga', 'asc');
                break;
            case 'harga-tinggi':
                $query->orderBy('harga', 'desc');
                break;
            case 'populer':
                // Mengurutkan berdasarkan rata-rata rating ulasan
                $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $produks = $query->paginate(12)->withQueryString();

        $kategoris = KategoriProduk::ordered()->get();

        // Related stores (toko) berdasarkan keyword pencarian
        $relatedUsahas = collect();
        if ($request->filled('search')) {
            $relatedUsahas = $this->buildUsahaSearchQuery($request->search)
                ->take(2)
                ->get();
        } else {
            $relatedUsahas = Usaha::inRandomOrder()
                ->take(2)
                ->get();
        }

        return view('guest.pages.katalog', [
            'produks' => $produks,
            'kategoris' => $kategoris,
            'selectedKategoriSlugs' => $selectedKategoriSlugs,
            'relatedUsahas' => $relatedUsahas,
            'searchTerm' => $request->search,
        ]);
    }

    /**
     * Halaman hasil pencarian toko/usaha lengkap.
     */
    public function tokoSearch(Request $request)
    {
        $searchTerm = $request->input('search', '');

        $query = $searchTerm !== ''
            ? $this->buildUsahaSearchQuery($searchTerm)
            : Usaha::query()->where('status_usaha', 'aktif')->latest();

        $usahas = $query->paginate(10)->withQueryString();

        return view('guest.pages.toko-search', [
            'usahas'     => $usahas,
            'searchTerm' => $searchTerm,
        ]);
    }

    /**
     * Reusable query builder untuk pencarian toko berdasarkan keyword.
     * Mencocokkan: nama_usaha, deskripsi, spesialisasi, username pemilik,
     * jenis usaha, nama produk yang dijual, dan kategori produk.
     */
    protected function buildUsahaSearchQuery(string $term)
    {
        $like = '%' . $term . '%';

        return Usaha::query()
            ->where('status_usaha', 'aktif')
            ->where(function ($q) use ($like) {
                $q->where('nama_usaha', 'like', $like)
                  ->orWhere('deskripsi_usaha', 'like', $like)
                  ->orWhere('spesialisasi_usaha', 'like', $like)
                  ->orWhereHas('user', function ($u) use ($like) {
                      $u->where('username', 'like', $like)
                        ->orWhere('nama', 'like', $like);
                  })
                  ->orWhereHas('jenisUsahas', function ($j) use ($like) {
                      $j->where('nama_jenis_usaha', 'like', $like);
                  })
                  ->orWhereHas('produks', function ($p) use ($like) {
                      $p->where('nama_produk', 'like', $like)
                        ->orWhere('deskripsi', 'like', $like)
                        ->orWhereHas('kategoriProduk', function ($k) use ($like) {
                            $k->where('nama_kategori_produk', 'like', $like);
                        });
                  });
            })
            ->with(['user', 'wilayah', 'jenisUsahas'])
            ->latest();
    }

    public function singleProduct($slug)
    {
        $produk = Produk::with(['fotoProduk', 'usaha', 'reviews.user'])->where('slug', $slug)->firstOrFail();
        
        $reviewsCount = $produk->reviews->count();
        $averageRating = $reviewsCount > 0 ? round($produk->reviews->avg('rating'), 1) : 0;
        
        // Hitung persentase bar rating
        $ratingStats = [
            5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0
        ];
        
        if ($reviewsCount > 0) {
            foreach ($produk->reviews as $review) {
                $ratingStats[$review->rating]++;
            }
        }

        $randomProduks = Produk::with(['fotoProduk', 'reviews'])->where('id', '!=', $produk->id)->inRandomOrder()->take(4)->get();

        return view('guest.pages.single-product', [
            'produk' => $produk,
            'reviewsCount' => $reviewsCount,
            'averageRating' => $averageRating,
            'ratingStats' => $ratingStats,
            'randomProduks' => $randomProduks,
        ]);
    }

    public function detailUsaha(Request $request, Usaha $usaha)
    {
        $usaha->load('pengerajins');

        $previousProduct = null;
        if ($request->has('from_product')) {
            $previousProduct = Produk::where('slug', $request->from_product)->first();
        }

        // Build query produk milik usaha ini, dengan filter (kategori/harga/urutkan)
        $query = $usaha->produks()->with(['kategoriProduk', 'fotoProduk', 'reviews']);

        // Filter kategori (multi-select, sama seperti katalog)
        $selectedKategoriSlugs = collect($request->input('kategori', []))
            ->when(is_string($request->input('kategori')), fn ($c) => collect([$request->input('kategori')]))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!empty($selectedKategoriSlugs)) {
            $selectedKategoriGroups = KategoriProduk::query()
                ->whereIn('slug', $selectedKategoriSlugs)
                ->get(['slug', 'category_type'])
                ->groupBy('category_type');

            foreach ($selectedKategoriGroups as $groupedCategories) {
                $groupSlugs = $groupedCategories->pluck('slug')->all();
                $query->whereHas('kategoriProduk', function ($q) use ($groupSlugs) {
                    $q->whereIn('slug', $groupSlugs);
                });
            }
        }

        // Filter harga
        if ($request->filled('min_harga')) $query->where('harga', '>=', $request->min_harga);
        if ($request->filled('max_harga')) $query->where('harga', '<=', $request->max_harga);

        // Sort
        switch ($request->input('urutkan', 'terbaru')) {
            case 'harga-rendah': $query->orderBy('harga', 'asc'); break;
            case 'harga-tinggi': $query->orderBy('harga', 'desc'); break;
            case 'populer':
                $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc');
                break;
            default: $query->latest(); break;
        }

        $produks   = $query->paginate(8)->withQueryString();
        $kategoris = KategoriProduk::ordered()->get();
        $categoryGroups = $kategoris->where('sort_order', '>', 0)->groupBy('category_type');

        // Toko sekitar utk peta "Toko di Sekitar" — semua usaha yg punya koordinat
        // (termasuk usaha ini, di-highlight beda di peta). Limit 50 — scope kecil
        // jadi belum perlu spatial query / clustering.
        $nearbyUsahas = \App\Models\Usaha::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['user:id,username'])
            ->limit(50)
            ->get(['id', 'nama_usaha', 'foto_usaha', 'deskripsi_usaha', 'latitude', 'longitude', 'user_id']);

        return view('guest.pages.detail-usaha', [
            'usaha'                 => $usaha,
            'produks'               => $produks,
            'previousProduct'       => $previousProduct,
            'kategoris'             => $kategoris,
            'selectedKategoriSlugs' => $selectedKategoriSlugs,
            'categoryGroups'        => $categoryGroups,
            'categoryTypeLabels'    => KategoriProduk::TYPE_LABELS,
            'nearbyUsahas'          => $nearbyUsahas,
        ]);
    }

    public function about()
    {
        return view('guest.pages.about');
    }
    public function contact()
    {
        return view('guest.pages.contact');
    }
}

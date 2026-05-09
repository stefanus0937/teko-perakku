<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\KategoriProduk;
use App\Models\Produk;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $kategoris = KategoriProduk::ordered()->get();
            $structuredKategoris = $kategoris->where('sort_order', '>', 0);

            $view->with([
                'randomKategoris' => KategoriProduk::inRandomOrder()->take(4)->get(),
                'randomProduks' => Produk::inRandomOrder()->take(8)->get(),
                'kategoris' => $kategoris,
                'categoryGroups' => $structuredKategoris->groupBy('category_type'),
                'categoryTypeLabels' => KategoriProduk::TYPE_LABELS,
                'categoryTypeDescriptions' => KategoriProduk::TYPE_DESCRIPTIONS,
            ]);
        });
    }
}

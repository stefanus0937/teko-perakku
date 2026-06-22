<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriProdukSeeder extends Seeder
{
    public static function categories(): array
    {
        // Master data for product categories shown to users in the header category dropdown,
        // catalog filter, store-detail product filter, and admin product category selector.
        // category_type splits the UI into groups: technique, finished product form, and material.
        return [
            KategoriProduk::TYPE_TECHNIQUE => ['Ukir', 'Filigree', 'Tatahan', 'Cor'],
            KategoriProduk::TYPE_FORM => ['Cincin', 'Kalung & Liontin', 'Gelang', 'Anting', 'Bros', 'Aksesoris Manten', 'Keris', 'Souvenir', 'Tas'],
            KategoriProduk::TYPE_MATERIAL => ['Perak', 'Emas', 'Tembaga', 'Kuningan', 'Perunggu'],
        ];
    }

    public static function seedCategories(): void
    {
        foreach (self::categories() as $type => $categories) {
            foreach ($categories as $index => $name) {
                // The slug is used in URLs and filters, for example /katalog?kategori[]=cincin.
                // updateOrCreate keeps the visual category list stable when seeding is run again.
                KategoriProduk::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        // This label is what visitors see in category menus, filter checkboxes, and product forms.
                        'nama_kategori_produk' => $name,
                        // This code appears in the admin category table as the internal category identifier.
                        'kode_kategori_produk' => 'KAT-' . strtoupper(Str::slug($type, '')) . '-' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                        // This decides which visual group/column the category appears under in the UI.
                        'category_type' => $type,
                        // This controls display order inside each category group in dropdowns and filters.
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }

    public function run(): void
    {
        // Called by DatabaseSeeder during php artisan db:seed so the browser has category options to render.
        self::seedCategories();
    }
}

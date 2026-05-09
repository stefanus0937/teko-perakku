<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriProdukSeeder extends Seeder
{
    public static function categories(): array
    {
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
                KategoriProduk::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'nama_kategori_produk' => $name,
                        'kode_kategori_produk' => 'KAT-' . strtoupper(Str::slug($type, '')) . '-' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                        'category_type' => $type,
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }

    public function run(): void
    {
        self::seedCategories();
    }
}

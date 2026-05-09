<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use DB;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = \App\Models\KategoriProduk::first();
        $usaha = \App\Models\Usaha::first();

        if ($kategori && $usaha) {
            for ($i = 1; $i <= 5; $i++) {
                $produk = \App\Models\Produk::create([
                    'kode_produk' => 'PRD-SEED' . $i,
                    'kategori_produk_id' => $kategori->id,
                    'nama_produk' => 'Produk Seeder ' . $i,
                    'deskripsi' => 'Deskripsi untuk produk seeder ke-' . $i,
                    'harga' => 100000 * $i,
                    'stok' => 10,
                ]);

                $produk->kategoriProduk()->sync([$kategori->id]);
                $produk->usaha()->sync([$usaha->id]);
            }
        }
    }
}

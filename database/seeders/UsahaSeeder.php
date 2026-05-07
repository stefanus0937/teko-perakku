<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use DB;

class UsahaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $umkmUser = \App\Models\User::where('username', 'umkm_user')->first();

        if ($umkmUser) {
            \App\Models\Usaha::updateOrCreate(
                ['user_id' => $umkmUser->id],
                [
                    'kode_usaha' => 'USH-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)),
                    'nama_usaha' => 'Kerajinan Perak Jaya',
                    'telp_usaha' => '081234567890',
                    'email_usaha' => 'info@perakjaya.com',
                    'deskripsi_usaha' => 'Bengkel kerajinan perak tradisional dengan kualitas terbaik.',
                    'status_usaha' => 'aktif',
                    'wilayah_id' => $umkmUser->wilayah_id,
                ]
            );
        }

        $umkmUser2 = \App\Models\User::where('username', 'umkm_user2')->first();
        if ($umkmUser2) {
            \App\Models\Usaha::updateOrCreate(
                ['user_id' => $umkmUser2->id],
                [
                    'kode_usaha' => 'USH-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)),
                    'nama_usaha' => 'Situmorang Silver Works',
                    'telp_usaha' => '081222333444',
                    'email_usaha' => 'contact@situmorang.com',
                    'deskripsi_usaha' => 'Spesialis perhiasan perak ukir khas Kotagede.',
                    'status_usaha' => 'aktif',
                    'wilayah_id' => $umkmUser2->wilayah_id,
                ]
            );
        }
    }
}

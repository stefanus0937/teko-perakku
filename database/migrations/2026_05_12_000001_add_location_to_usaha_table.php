<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom koordinat geografis pada tabel `usaha`.
 *
 * - latitude / longitude : koordinat numerik utk render marker Leaflet.
 *   Pakai DECIMAL(10,7) — presisi ~1cm, cukup untuk lokasi toko.
 *
 * Catatan: alamat teks tidak disimpan di sini — sudah ada di `users.alamat`
 * (relasi usaha->user->alamat). `link_gmap_usaha` yang sudah ada tetap
 * dipakai sebagai sumber URL "Open in Google Maps" + ekstraksi koordinat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usaha', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('link_gmap_usaha');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('usaha', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};

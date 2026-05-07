<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Menggabungkan: wilayahs, users (+profile_fields, +last_seen_at), jenis_usaha,
// pengerajin (+social_links, +foto), kategori_produk, usaha (+social_links, +extra_fields),
// produk, foto_produk, usaha_produk, usaha_jenis, usaha_pengerajin

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_wilayah');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('nama')->nullable();
            $table->string('email')->unique();
            $table->string('no_hp')->nullable();
            $table->enum('gender', ['Pria', 'Wanita'])->nullable();
            $table->integer('usia')->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin_utama', 'admin_wilayah', 'umkm', 'user'])->default('user');
            $table->foreignId('wilayah_id')->nullable()->constrained('wilayahs')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('jenis_usaha', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jenis_usaha')->unique();
            $table->string('nama_jenis_usaha');
            $table->timestamps();
        });

        Schema::create('pengerajin', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengerajin')->unique();
            $table->string('nama_pengerajin')->nullable();
            $table->enum('jk_pengerajin', ['P', 'W'])->nullable();
            $table->integer('usia_pengerajin')->nullable();
            $table->string('telp_pengerajin')->nullable();
            $table->string('email_pengerajin')->nullable();
            $table->text('alamat_pengerajin')->nullable();
            $table->string('foto_pengerajin')->nullable();
            $table->string('link_tokopedia_pengerajin')->nullable();
            $table->string('link_shopee_pengerajin')->nullable();
            $table->string('link_instagram_pengerajin')->nullable();
            $table->string('link_tiktok_pengerajin')->nullable();
            $table->string('link_facebook_pengerajin')->nullable();
            $table->timestamps();
        });

        Schema::create('kategori_produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kategori_produk')->unique();
            $table->string('nama_kategori_produk');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('usaha', function (Blueprint $table) {
            $table->id();
            $table->string('kode_usaha')->unique();
            $table->string('nama_usaha')->nullable();
            $table->string('telp_usaha')->nullable();
            $table->string('email_usaha')->nullable();
            $table->text('deskripsi_usaha')->nullable();
            $table->string('foto_usaha')->nullable();
            $table->string('link_gmap_usaha')->nullable();
            $table->string('link_tokopedia_usaha')->nullable();
            $table->string('link_shopee_usaha')->nullable();
            $table->string('link_instagram_usaha')->nullable();
            $table->string('link_tiktok_usaha')->nullable();
            $table->string('link_facebook_usaha')->nullable();
            $table->string('link_website_usaha')->nullable();
            $table->string('link_wa_usaha')->nullable();
            $table->text('spesialisasi_usaha')->nullable();
            $table->text('foto_tempat')->nullable();
            $table->enum('status_usaha', ['aktif', 'nonaktif', 'tutup', 'pending', 'dibekukan'])->default('aktif');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('wilayah_id')->nullable()->constrained('wilayahs')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->foreignId('kategori_produk_id')->constrained('kategori_produk');
            $table->string('nama_produk')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('harga')->nullable();
            $table->integer('stok')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('foto_produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_foto_produk')->unique();
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->string('file_foto_produk')->nullable();
            $table->timestamps();
        });

        Schema::create('usaha_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usaha_id')->constrained('usaha')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('usaha_jenis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usaha_id')->constrained('usaha')->onDelete('cascade');
            $table->foreignId('jenis_usaha_id')->constrained('jenis_usaha')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('usaha_pengerajin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usaha_id')->constrained('usaha')->onDelete('cascade');
            $table->foreignId('pengerajin_id')->constrained('pengerajin')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usaha_pengerajin');
        Schema::dropIfExists('usaha_jenis');
        Schema::dropIfExists('usaha_produk');
        Schema::dropIfExists('foto_produk');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('usaha');
        Schema::dropIfExists('kategori_produk');
        Schema::dropIfExists('pengerajin');
        Schema::dropIfExists('jenis_usaha');
        Schema::dropIfExists('users');
        Schema::dropIfExists('wilayahs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategori_produk', function (Blueprint $table) {
            if (!Schema::hasColumn('kategori_produk', 'category_type')) {
                $table->string('category_type')->default('product_form')->after('slug')->index();
            }

            if (!Schema::hasColumn('kategori_produk', 'sort_order')) {
                $table->unsignedSmallInteger('sort_order')->default(0)->after('category_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kategori_produk', function (Blueprint $table) {
            if (Schema::hasColumn('kategori_produk', 'sort_order')) {
                $table->dropColumn('sort_order');
            }

            if (Schema::hasColumn('kategori_produk', 'category_type')) {
                $table->dropColumn('category_type');
            }
        });
    }
};

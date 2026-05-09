<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KategoriProduk extends Model
{
    use HasFactory;

    protected $table = 'kategori_produk';
    protected $fillable = [
        'kode_kategori_produk',
        'nama_kategori_produk',
        'slug',
        'category_type',
        'sort_order',
    ];

    public const TYPE_TECHNIQUE = 'production_technique';
    public const TYPE_FORM = 'product_form';
    public const TYPE_MATERIAL = 'material_type';

    public const TYPE_LABELS = [
        self::TYPE_TECHNIQUE => 'Teknik Pembuatan',
        self::TYPE_FORM => 'Bentuk Jadi',
        self::TYPE_MATERIAL => 'Bahan Pembuatan',
    ];

    public const TYPE_DESCRIPTIONS = [
        self::TYPE_TECHNIQUE => 'Pilih berdasarkan proses pembuatan karya perak.',
        self::TYPE_FORM => 'Jelajahi koleksi berdasarkan bentuk produk.',
        self::TYPE_MATERIAL => 'Temukan produk dari bahan utama pilihan.',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($kategori) {
            $slug = Str::slug($kategori->nama_kategori_produk);
            $existingSlugCount = self::where('slug', $slug)->count();
            if ($existingSlugCount > 0) {
                $slug .= '-' . ($existingSlugCount + 1);
            }
            $kategori->slug = $slug;
        });

        static::updating(function ($kategori) {
            $kategori->slug = Str::slug($kategori->nama_kategori_produk);
        });
    }

    public function produks()
    {
        return $this->belongsToMany(Produk::class, 'produk_kategoris_pivot', 'kategori_produk_id', 'produk_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('category_type')->orderBy('sort_order')->orderBy('nama_kategori_produk');
    }

    public static function groupedForDisplay()
    {
        return static::query()
            ->orderBy('sort_order')
            ->orderBy('nama_kategori_produk')
            ->get()
            ->groupBy('category_type');
    }

    public function getCategoryTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->category_type] ?? 'Kategori Produk';
    }
}

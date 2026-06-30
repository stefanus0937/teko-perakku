<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KategoriProduk extends Model
{
    use HasFactory, HasTranslatedAttributes;

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

    public static function typeLabels(): array
    {
        return [
            self::TYPE_TECHNIQUE => __('messages.category_types.production_technique'),
            self::TYPE_FORM => __('messages.category_types.product_form'),
            self::TYPE_MATERIAL => __('messages.category_types.material_type'),
        ];
    }

    public static function typeDescriptions(): array
    {
        return [
            self::TYPE_TECHNIQUE => __('messages.category_type_descriptions.production_technique'),
            self::TYPE_FORM => __('messages.category_type_descriptions.product_form'),
            self::TYPE_MATERIAL => __('messages.category_type_descriptions.material_type'),
        ];
    }

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

    public function getTranslatedNamaKategoriProdukAttribute(): string
    {
        $localized = __('messages.category_names.' . $this->slug);

        if ($localized !== 'messages.category_names.' . $this->slug) {
            return $localized;
        }

        return $this->translated('nama_kategori_produk');
    }

    public function getTranslatedCategoryTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->category_type] ?? translate_text($this->category_type_label);
    }
}

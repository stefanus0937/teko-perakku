<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usaha extends Model
{
    use HasFactory, HasTranslatedAttributes;

    protected $table = 'usaha';
    protected $fillable = [
        'kode_usaha',
        'nama_usaha',
        'telp_usaha',
        'email_usaha',
        'deskripsi_usaha',
        'foto_usaha',
        'link_gmap_usaha',
        'status_usaha',
        'user_id',
        'wilayah_id',
        'link_website_usaha',
        'link_wa_usaha',
        'link_tokopedia_usaha',
        'link_shopee_usaha',
        'link_instagram_usaha',
        'link_tiktok_usaha',
        'link_facebook_usaha',
        'spesialisasi_usaha',
        'foto_tempat',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'foto_tempat' => 'array',
        'latitude'    => 'float',
        'longitude'   => 'float',
    ];

    /**
     * Apakah usaha sudah punya koordinat valid untuk dirender di Leaflet.
     */
    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    // public function usahaJenis()
    // {
    //     return $this->hasMany(UsahaJenis::class, 'usaha_id');
    // }

    // public function usahaPengerajin()
    // {
    //     return $this->hasMany(UsahaPengerajin::class, 'usaha_id');
    // }

    public function pengerajins()
    {
        return $this->belongsToMany(Pengerajin::class, 'usaha_pengerajin', 'usaha_id', 'pengerajin_id');
    }

    public function jenisUsahas()
    {
        return $this->belongsToMany(JenisUsaha::class, 'usaha_jenis', 'usaha_id', 'jenis_usaha_id');
    }

    public function produks()
    {
        return $this->belongsToMany(Produk::class, 'usaha_produk', 'usaha_id', 'produk_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function getTranslatedNamaUsahaAttribute(): string
    {
        return $this->translated('nama_usaha');
    }

    public function getTranslatedDeskripsiUsahaAttribute(): string
    {
        return $this->translated('deskripsi_usaha');
    }

    public function getTranslatedSpesialisasiUsahaAttribute(): string
    {
        return $this->translated('spesialisasi_usaha');
    }
}

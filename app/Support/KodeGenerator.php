<?php

namespace App\Support;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Generator kode unik untuk entitas (User/Admin/Pengrajin/Usaha/Produk/Pelaporan).
 *
 * Format standar: PREFIX-XXXX (4 digit, zero-padded).
 *   - USR-0001  → user biasa
 *   - ADM-0001  → admin
 *   - PNG-0001  → pengerajin
 *   - UMK-0001  → usaha/UMKM
 *   - PRD-0001  → produk
 *   - LAP-0001  → pelaporan
 *
 * Race-safety:
 *   `next()` membungkus pembacaan MAX(id) + return string dalam transaction.
 *   Kalau insert pakai kode itu gagal karena unique-violation (race antar
 *   request), caller bisa pakai `safeCreate()` yang me-retry sampai 3x.
 *
 * Pemakaian:
 *
 *   $kode = KodeGenerator::next('UMK', 'usaha');
 *   $usaha = Usaha::create([...] + ['kode_usaha' => $kode]);
 *
 *   // Atau pakai wrapper retry:
 *   $usaha = KodeGenerator::safeCreate(
 *       fn ($kode) => Usaha::create([...] + ['kode_usaha' => $kode]),
 *       'UMK', 'usaha', 'kode_usaha'
 *   );
 */
class KodeGenerator
{
    /**
     * Ambil kode berikutnya untuk sebuah tabel.
     *
     * Pakai MAX(id)+1 di dalam transaction → konsisten dengan pattern lama
     * tapi terbungkus untuk race-safety dasar.
     *
     * @param string $prefix Contoh: 'UMK'
     * @param string $table  Nama tabel, contoh: 'usaha'
     * @param int    $width  Lebar zero-padding (default 4)
     */
    public static function next(string $prefix, string $table, int $width = 4): string
    {
        return DB::transaction(function () use ($prefix, $table, $width) {
            $nextId = ((int) DB::table($table)->max('id')) + 1;
            return $prefix . '-' . str_pad((string) $nextId, $width, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Buat row dengan kode baru, retry kalau bentrok unique.
     * Bermanfaat saat dua admin submit form bersamaan.
     *
     * @param callable $insertCallable  fn(string $kode): Model — caller bertanggung jawab insert
     * @param string   $prefix
     * @param string   $table
     * @param string   $kodeColumn      kolom yang punya UNIQUE constraint
     * @param int      $maxRetries
     * @return mixed                    Return value dari $insertCallable
     */
    public static function safeCreate(
        callable $insertCallable,
        string $prefix,
        string $table,
        string $kodeColumn,
        int $maxRetries = 3
    ) {
        $lastException = null;
        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            $kode = self::next($prefix, $table);
            try {
                return $insertCallable($kode);
            } catch (QueryException $e) {
                // SQLSTATE 23000 (integrity constraint) — kemungkinan unique-violation.
                // Cek nama kolom di pesan error untuk hindari swallow error lain.
                if ($e->getCode() === '23000' && str_contains($e->getMessage(), $kodeColumn)) {
                    $lastException = $e;
                    continue; // retry dengan kode baru
                }
                throw $e;
            }
        }
        throw $lastException ?? new \RuntimeException("Gagal generate kode unik untuk {$table} setelah {$maxRetries} percobaan.");
    }
}

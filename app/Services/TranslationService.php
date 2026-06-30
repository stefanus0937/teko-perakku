<?php

namespace App\Services;

use Datlechin\GoogleTranslate\Facades\GoogleTranslate;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class TranslationService
{
    private const CACHE_DAYS = 30;
    private const MAX_CHUNK_LENGTH = 4500;

    public function translate(?string $text, ?string $locale = null): ?string
    {
        // Abaikan nilai kosong agar Blade aman memanggil helper untuk data opsional.
        if ($text === null) {
            return null;
        }

        $text = trim($text);

        if ($text === '') {
            return $text;
        }

        // Target bahasa mengikuti session karena LocaleMiddleware sudah menjalankan App::setLocale().
        $targetLocale = $locale ?: App::getLocale();
        $sourceLocale = config('translation.source_language', 'id');
        $supportedLocales = config('translation.supported_languages', ['id', 'en']);

        if ($targetLocale === $sourceLocale || ! in_array($targetLocale, $supportedLocales, true)) {
            return $text;
        }

        if (mb_strlen($text) <= self::MAX_CHUNK_LENGTH) {
            return $this->translateChunk($text, $targetLocale);
        }

        $chunks = $this->splitText($text);
        $translatedChunks = [];

        foreach ($chunks as $chunk) {
            $translatedChunks[] = $this->translateChunk($chunk, $targetLocale);
        }

        return implode("\n\n", $translatedChunks);
    }

    private function translateChunk(string $text, string $targetLocale): string
    {
        // Cache hasil translasi untuk mengurangi request berulang ke Google Translate.
        $cacheKey = 'translation_' . $targetLocale . '_' . sha1($text);

        return Cache::remember(
            $cacheKey,
            now()->addDays(self::CACHE_DAYS),
            fn () => $this->requestTranslation($text, $targetLocale)
        );
    }

    private function requestTranslation(string $text, string $targetLocale): string
    {
        try {
            $result = GoogleTranslate::source(config('translation.source_language', 'id'))
                ->target($targetLocale)
                ->translate($text);

            return $result->getTranslatedText();
        } catch (Throwable $exception) {
            // Jika API gagal, tampilkan teks asli supaya halaman tetap bisa dirender.
            Log::warning('Google Translate request exception.', [
                'message' => $exception->getMessage(),
            ]);

            return $text;
        }
    }

    /**
     * Split long text into paragraph-sized chunks so LibreTranslate is less likely to reject it.
     */
    private function splitText(string $text): array
    {
        $paragraphs = preg_split("/\R{2,}/", $text) ?: [$text];
        $chunks = [];
        $current = '';

        foreach ($paragraphs as $paragraph) {
            $candidate = $current === '' ? $paragraph : $current . "\n\n" . $paragraph;

            if (mb_strlen($candidate) <= self::MAX_CHUNK_LENGTH) {
                $current = $candidate;
                continue;
            }

            if ($current !== '') {
                $chunks[] = $current;
            }

            $current = $paragraph;
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }
}

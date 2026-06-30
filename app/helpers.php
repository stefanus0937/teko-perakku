<?php

use App\Services\TranslationService;

if (! function_exists('translate_text')) {
    function translate_text(mixed $text): string
    {
        // Helper Blade: otomatis memakai target bahasa dari session/App locale.
        if ($text === null) {
            return '';
        }

        $text = trim((string) $text);

        if ($text === '') {
            return $text;
        }

        return app(TranslationService::class)->translate($text) ?? $text;
    }
}

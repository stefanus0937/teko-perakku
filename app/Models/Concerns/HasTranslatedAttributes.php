<?php

namespace App\Models\Concerns;

use App\Services\TranslationService;

trait HasTranslatedAttributes
{
    /**
     * Ambil versi terjemahan dari atribut model tanpa mengubah data asli di database.
     */
    public function translated(string $attribute, ?string $locale = null): string
    {
        $value = $this->getAttribute($attribute);

        if ($value === null) {
            return '';
        }

        return app(TranslationService::class)->translate((string) $value, $locale) ?? (string) $value;
    }
}

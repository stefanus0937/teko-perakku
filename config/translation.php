<?php

return [
    // Bahasa asli konten aplikasi. Jika locale aktif sama dengan ini, teks tidak diterjemahkan.
    'source_language' => 'id',

    // Daftar bahasa yang boleh dipilih user dan dipakai sebagai target Google Translate.
    'supported_languages' => ['id', 'en'],

    // Cache membantu mencegah request berulang yang bisa memicu rate limit 429.
    'cache_days' => env('TRANSLATION_CACHE_DAYS', 30),
];

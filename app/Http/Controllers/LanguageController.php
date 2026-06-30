<?php

namespace App\Http\Controllers;

use App\Services\TranslationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Simpan pilihan bahasa ke session supaya tetap aktif saat user berpindah halaman.
     */
    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'in:id,en'],
        ]);

        session(['locale' => $validated['locale']]);

        return back()->with('success', __('settings.language_saved'));
    }

    /**
     * Contoh endpoint untuk memproses translasi teks dari request.
     */
    public function translate(Request $request, TranslationService $translationService): RedirectResponse
    {
        $validated = $request->validate([
            'text' => ['required', 'string'],
        ]);

        return back()->with([
            'translated_text' => $translationService->translate($validated['text']),
        ]);
    }
}

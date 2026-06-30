<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil locale dari session; fallback ke config/app.php saat session belum punya pilihan.
        $locale = session('locale', config('app.locale', 'id'));

        if (! in_array($locale, ['id', 'en'], true)) {
            $locale = config('app.locale', 'id');
        }

        App::setLocale($locale);

        return $next($request);
    }
}

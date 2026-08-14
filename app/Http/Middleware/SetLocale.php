<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Applies the user's chosen UI language (ID / EN) for the current request.
 * Only affects static, translated UI strings — OMDb API response data is
 * left untouched, per the test brief.
 */
class SetLocale
{
    public function handle($request, Closure $next)
    {
        $locale = session('locale', config('app.locale'));

        if (in_array($locale, ['en', 'id'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}

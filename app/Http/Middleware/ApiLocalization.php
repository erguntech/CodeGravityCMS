<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;

/**
 * Resolves the response language for public API calls.
 *
 * Priority: ?lang= query param, then Accept-Language header, then the client's default language.
 * A language the client has not activated falls back to the client's default, and the client's
 * default is also used as the fallback locale so records without a translation never come back empty.
 */
class ApiLocalization
{
    public function handle(Request $request, Closure $next)
    {
        $requested = $request->query('lang') ?: $request->header('Accept-Language');
        $requested = $requested ? strtolower(substr(trim($requested), 0, 2)) : null;

        $client = $request->bearerToken()
            ? Client::where('api_token', $request->bearerToken())->first()
            : null;

        if ($client) {
            $languages = $client->languages()->where('is_active', true)->get(['code', 'is_default']);
            $default = $languages->firstWhere('is_default', true)?->code
                ?? $languages->first()?->code
                ?? config('app.locale');
            $active = $languages->pluck('code')->all();

            $locale = ($requested && in_array($requested, $active, true)) ? $requested : $default;

            app()->setLocale($locale);
            app()->setFallbackLocale($default);
        } elseif ($requested) {
            app()->setLocale($requested);
        }

        return $next($request);
    }
}

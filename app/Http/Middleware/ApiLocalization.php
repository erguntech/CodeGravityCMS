<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiLocalization
{
    public function handle(Request $request, Closure $next)
    {
        // Check Accept-Language header or ?lang= query param
        $lang = $request->header('Accept-Language');
        if (!$lang && $request->has('lang')) {
            $lang = $request->get('lang');
        }

        if ($lang) {
            $lang = substr($lang, 0, 2);
            app()->setLocale($lang);
        }

        return $next($request);
    }
}

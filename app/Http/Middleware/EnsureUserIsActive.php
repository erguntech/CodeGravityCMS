<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ends the session of a user whose account was set to "passive" after login.
 * The login form itself rejects passive accounts via Fortify::authenticateUsing().
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ($user->status ?? 'active') !== 'active') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Unauthenticated', 'redirect' => route('login')], 401);
            }

            return redirect()->route('login')->with('error', __('Hesabınız pasif durumda. Lütfen yönetici ile iletişime geçin.'));
        }

        return $next($request);
    }
}

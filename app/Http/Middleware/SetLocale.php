<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetLocale — reads the authenticated user's stored locale preference and
 * sets the application locale for the current request.
 *
 * Falls back to session → cookie → app default ('en') in that order.
 * Supported locales: 'en', 'es'
 */
class SetLocale
{
    private const SUPPORTED = ['en', 'es'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        // 1. Authenticated user's stored preference (via user_prefs table)
        if ($user = $request->user()) {
            $stored = $user->GetPref('locale');
            if ($stored && in_array($stored, self::SUPPORTED, true)) {
                return $stored;
            }
        }

        // 2. Session (set by locale switcher before user saves preference)
        $session = $request->session()->get('locale');
        if ($session && in_array($session, self::SUPPORTED, true)) {
            return $session;
        }

        // 3. App default
        return config('app.locale', 'en');
    }
}

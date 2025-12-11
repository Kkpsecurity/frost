<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminGuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only redirect guests away from admin login if BOTH admin and web
        // guards are authenticated. If only the admin guard is set but the
        // web guard is not, allow the request to continue so the login
        // process can establish both guards and avoid redirect loops.
        if (Auth::guard('admin')->check() && Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}

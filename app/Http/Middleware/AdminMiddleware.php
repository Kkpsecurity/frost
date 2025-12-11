<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Prefer the admin guard; if not present, fall back to web guard.
        $user = null;

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            // Mirror to web guard for compatibility
            Auth::guard('web')->setUser($user);
        } elseif (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            // Mirror to admin guard for compatibility
            Auth::guard('admin')->setUser($user);
        } else {
            return redirect()->route('admin.login');
        }

        // Check if the authenticated user has admin role
        if (!$user || !$user->isAdmin()) {
            return redirect()->route('admin.login')->withErrors([
                'email' => 'You do not have admin privileges.'
            ]);
        }

        return $next($request);
    }
}

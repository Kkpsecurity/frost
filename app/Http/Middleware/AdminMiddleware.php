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
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // Check if the authenticated user has admin role (role_id = 1)
        $user = Auth::guard('admin')->user();
        if (!$user || !$user->isAdmin()) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->withErrors([
                'email' => 'You do not have admin privileges.'
            ]);
        }

        return $next($request);
    }
}

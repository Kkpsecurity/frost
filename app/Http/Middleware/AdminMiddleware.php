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
        // Check if user is authenticated on either web or admin guard
        if (!Auth::guard('web')->check()) {
            return redirect()->route('admin.login');
        }

        // Check if the authenticated user has admin role
        $user = Auth::guard('web')->user();
        if (!$user || !$user->isAdmin()) {
            return redirect()->route('admin.login')->withErrors([
                'email' => 'You do not have admin privileges.'
            ]);
        }

        // Set the admin guard to use the same user
        Auth::guard('admin')->setUser($user);

        return $next($request);
    }
}

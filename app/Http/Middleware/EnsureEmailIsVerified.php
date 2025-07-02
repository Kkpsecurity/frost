<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EnsureEmailIsVerified
{
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (!$request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
            !$request->user()->hasVerifiedEmail())
        ) {
            // return $request->expectsJson()
            //     ? abort(403, 'Your email address is not verified.')
            //     : redirect()->route($redirectToRoute ?: 'verification.notice');
            return false;
        }

        return $next($request);
    }
}

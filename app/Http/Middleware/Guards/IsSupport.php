<?php

namespace App\Http\Middleware\Guards;

use Closure;
use Illuminate\Http\Request;


class IsSupport
{

    public function handle(Request $request, Closure $next)
    {

        kkpdebug('Guard', __CLASS__);

        // Must use the 'admin' guard — admin users are NOT on the default web guard
        $user = auth('admin')->user();
        abort_unless($user && $user->IsSupport(), 403);

        return $next($request);
    }
}

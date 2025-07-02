<?php

namespace App\RCache;

use Closure;
use Illuminate\Http\Request;

use RCache;


class RCacheMiddleware
{

    public function handle( Request $Request, Closure $next )
    {

        //
        // wait until after Response has been generated
        //

        $Response = $next( $Request );

        //
        // this handles DebugBar itself
        //

        RCache::RedisDebugBar();

        return $Response;

    }

}

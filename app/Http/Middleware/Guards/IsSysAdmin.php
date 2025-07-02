<?php

namespace App\Http\Middleware\Guards;

use Closure;
use Illuminate\Http\Request;


class IsSysAdmin
{

    public function handle( Request $request, Closure $next )
    {

        kkpdebug( 'Guard', __CLASS__ );

        abort_unless( auth()->authenticate()->IsSysAdmin(), 403 );

        return $next( $request );

    }

}

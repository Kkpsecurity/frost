<?php

namespace App\Traits;


trait CriticalFailTrait
{


    public static function CriticalFail( string $error_msg, string $route = null )
    {

        logger( 'CRITICAL FAIL: ' . $error_msg );

        if ( $route )
        {
            abortToRoute( $route, $error_msg, 'error' );
        }
        else
        {
            api_abort( 500, $error_msg );
        }

    }


}

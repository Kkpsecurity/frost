<?php

namespace App\RCache;


trait RCacheLocker
{


    public static function Locker( string $lock_name, int $lock_timeout = 3 ) : bool
    {

        $key = "lock:{$lock_name}";

        //
        // clear lock
        //

        if ( 0 == $lock_timeout )
        {
            self::Redis()->del( $key );
            return true;
        }

        //
        // try to set lock
        //

        if ( ! self::Redis()->setnx( $key, true ) )
        {
            return false;
        }

        //
        // set expiration
        //

        self::Redis()->expire( $key, $lock_timeout );
        return true;

    }


}

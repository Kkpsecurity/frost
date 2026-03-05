<?php

namespace App\RCache;


trait RCacheLocker
{


    public static function Locker( string $lock_name, int $lock_timeout = 3 ) : bool
    {

        $key = "lock:{$lock_name}";

        $redis = self::Redis();
        if ( ! $redis )
        {
            // If Redis is unavailable, bypass distributed locking.
            return true;
        }

        //
        // clear lock
        //

        if ( 0 == $lock_timeout )
        {
            $redis->del( $key );
            return true;
        }

        //
        // try to set lock
        //

        if ( ! $redis->setnx( $key, true ) )
        {
            return false;
        }

        //
        // set expiration
        //

        $redis->expire( $key, $lock_timeout );
        return true;

    }


}

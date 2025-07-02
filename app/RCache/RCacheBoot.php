<?php

namespace App\RCache;

use Exception;
use Illuminate\Support\Collection;


trait RCacheBoot
{

    public static function boot() : void
    {

        self::_SetSerializer();

        if ( ! self::Redis() )
        {
            throw new Exception( 'Redis connection failed' );
        }

        //
        // convert cache props to Collections
        //

        self::$_StaticCaches = new Collection;
        self::$_ModelCaches  = new Collection;

        //
        // enable / disable ModelCaches
        //

        if ( IsQueueWorker() )
        {
            kkpdebug( 'RCacheRedis', 'Disabling ModelCaches' );
            self::$_cache_models = false;
        }
        else
        {
            kkpdebug( 'RCacheRedis', 'Enabling ModelCaches' );
            self::$_cache_models = true;
        }

    }

}

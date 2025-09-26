<?php

namespace App\Support\RCache;

use Exception;
use Illuminate\Support\Collection;

/**
 * RCacheBoot
 *
 * This trait is responsible for bootstrapping the RCache system.
 * It initializes the Redis connection and sets up the cache properties.
 */
trait RCacheBoot
{
    /**
     * Static cache collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected static $_StaticCaches;

    /**
     * Static model cache collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected static $_ModelCaches;

    /**
     * Enable or disable model caches.
     *
     * @var bool
     */
    protected static $_cache_models = true;

    public static function boot(): void
    {

        self::_SetSerializer();

        if (! self::Redis()) {
            throw new Exception('Redis connection failed');
        }

        //
        // convert cache props to Collections
        //

        self::$_StaticCaches = new Collection;
        self::$_ModelCaches  = new Collection;

        //
        // enable / disable ModelCaches
        //

        if (IsQueueWorker()) {
            \kkpdebug('RCacheRedis', 'Disabling ModelCaches');
            self::$_cache_models = false;
        } else {
            \kkpdebug('RCacheRedis', 'Enabling ModelCaches');
            self::$_cache_models = true;
        }
    }
}

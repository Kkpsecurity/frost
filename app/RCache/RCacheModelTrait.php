<?php

namespace App\RCache;

use RCache;


trait RCacheModelTrait
{

    protected static function boot()
    {

        parent::boot();

        static::saved(function ( $Model ) {
            kkpdebug( 'Observer', get_class($Model) . '::saved (RCacheModelTrait)' );
            RCache::observer_saved( $Model );
        });

        static::deleted(function ( $Model ) {
            kkpdebug( 'Observer', get_class($Model) . '::deleted (RCacheModelTrait)' );
            RCache::observer_deleted( $Model );
        });

    }

}

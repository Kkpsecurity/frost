<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\RCache;


class RCacheServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton( RCache::class, function( $app ) {
            return new RCache;
        });
    }

    public function boot()
    {
        RCache::boot();
    }

    public function provides()
    {
        return [ RCache::class ];
    }

}

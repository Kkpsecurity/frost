<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

use App\Classes\KKPS3;


class KKPS3ServiceProvider extends ServiceProvider implements DeferrableProvider
{

    public function register()
    {
        $this->app->singleton( KKPS3::class, function( $app ) {
            return new KKPS3;
        });
    }

    public function boot()
    {
        KKPS3::boot();
    }

    public function provides()
    {
        return [ KKPS3::class ];
    }

}

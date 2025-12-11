<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

use App\Classes\Keymaster;


class KeymasterServiceProvider extends ServiceProvider implements DeferrableProvider
{

    public function register()
    {
        $this->app->singleton( Keymaster::class, function( $app ) {
            return new Keymaster;
        });
    }

    public function boot()
    {
        Keymaster::boot();
    }

    public function provides()
    {
        return [ Keymaster::class ];
    }

}

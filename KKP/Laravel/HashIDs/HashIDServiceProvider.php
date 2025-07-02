<?php

namespace KKP\Laravel\HashIDs;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

use KKP\Laravel\HashIDs\HashID;


class HashIDServiceProvider extends ServiceProvider
{

    public function register()
    {

        Route::pattern( 'hash_id', '^\d*$' ); // only digits

        Route::bind( 'hash_id', function( $hash_id ) {
            return HashID::decode( $hash_id );
        });

        $this->app->singleton( 'HashID', function ( $app ) {
            return new HashID;
        });

    }

}

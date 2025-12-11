<?php
/**
 *
 * config/app.php  providers:
 *         KKP\Laravel\ServiceProviders\JSDataServiceProvider::class,
 *
 * config/app.php  aliases:
 *        'JSData' => KKP\Laravel\JSData::class,
 *
 */

namespace KKP\Laravel\ServiceProviders;

use Illuminate\Support\ServiceProvider;

use KKP\Laravel\JSData;


class JSDataServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton( 'JSData', function( $app ) {
            return new JSData;
        });
    }

}

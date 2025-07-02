<?php

namespace App\Providers;

use Auth;
use Blade;
use Illuminate\Support\ServiceProvider;

#use App\Helpers\Helpers;


class BladeServiceProvider extends ServiceProvider
{

    public function boot()
    {


        Blade::directive( 'nl2br', function ( string $string ) {
            return "<?php echo nl2br(e({$string})); ?>";
        });


        /**
         * @isAnyAdmin
         * @else
         * @endisAnyAdmin
         */
        Blade::if( 'isAnyAdmin', function() {
            return Auth::check() && Auth::user()->IsAnyAdmin();
        });


        Blade::directive( 'version', function( string $path ) {

            $asset_path = str_replace( '//', '/', ( 'assets/' . $path  ) );
            $asset_url  = asset( $asset_path );
            $filename   = public_path() . DIRECTORY_SEPARATOR . str_replace( '/', DIRECTORY_SEPARATOR, $asset_path );

            if ( ! is_file( $filename ) )
            {
                logger( "@version({$asset_path}) Not Found: '{$filename}'" );
                return $asset_url;
            }

            return "<?php echo '{$asset_url}?v=' . filemtime('{$filename}'); ?>";

        });



        /*
        Blade::directive( 'info_email_link', function() {
            return Helpers::InfoEmailLink();
        });

        Blade::directive( 'support_email_link', function() {
            return Helpers::SupportEmailLink();
        });
        */

    }

}

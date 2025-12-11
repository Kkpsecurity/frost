<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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
            return Auth::guard('admin')->check() && Auth::guard('admin')->user()->IsAnyAdmin();
        });

        /**
         * @sysadmin
         * @else
         * @endsysadmin
         */
        Blade::if( 'sysadmin', function() {
            return Auth::guard('admin')->check() && Auth::guard('admin')->user()->IsSysAdmin();
        });

        /**
         * @administrator
         * @else
         * @endadministrator
         */
        Blade::if( 'administrator', function() {
            return Auth::guard('admin')->check() && Auth::guard('admin')->user()->IsAdministrator();
        });

        /**
         * @support
         * @else
         * @endsupport
         */
        Blade::if( 'support', function() {
            return Auth::guard('admin')->check() && Auth::guard('admin')->user()->IsSupport();
        });

        /**
         * @instructor
         * @else
         * @endinstructor
         */
        Blade::if( 'instructor', function() {
            return Auth::guard('admin')->check() && Auth::guard('admin')->user()->IsInstructor();
        });

        /**
         * @student
         * @else
         * @endstudent
         */
        Blade::if( 'student', function() {
            return Auth::guard('admin')->check() && Auth::guard('admin')->user()->IsStudent();
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

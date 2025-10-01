<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Services\RCache;
use App\Models\Course;
use KKP\Laravel\JSData;


class Helpers
{


    public static function AppVersion() : int
    {
        if ( ! $filename = public_path( 'js/app.js' ) )
        {
            logger( 'Could not find js/app.js' );
            return 0;
        }
        return filemtime( $filename );
    }


    public static function EnrollButton( int|Course $Course ) : string
    {

        if ( is_int( $Course ) )
        {
            $Course = Course::findOrFail( $Course );
        }

        if ( ! Auth::check() )
        {
            return view( 'frontend.shop.partials.enroll_buttons.register' )->render();
        }

        if ( Auth::user()->IsEnrolled( $Course ) )
        {
            return view( 'frontend.shop.partials.enroll_buttons.active' )->render();
        }

        return view(
                    'frontend.shop.partials.enroll_buttons.enroll',
            ['route' => route('courses.enroll', $Course)]
               )->render();

    }



    public static function SiteConfigsKVP( array $keys ) : array
    {
        return RCache::SiteConfigs()->whereIn( 'config_name', $keys )
                                      ->pluck( 'config_value', 'config_name' )
                                    ->toArray();
    }


    public static function JSDataSiteConfigs( array $keys ) : void
    {

        $configs_json = json_encode(
            RCache::SiteConfigs()->whereIn( 'config_name', $keys )
                                 ->pluck( 'config_value', 'config_name' )
                                 ->toArray()
        );

        JSData::SetKeyVal( 'site_configs', $configs_json, false );

    }


    public static function IsLocal() : bool
    {
        return ( strpos( request()->server( 'REMOTE_ADDR' ), '10.10.' ) !== false );
    }


    public static function GoogleMapLink( string $address, string $class = null ) : string
    {

        $address = preg_replace( '/[\n\,]/', ' ', $address ); // convert chars to spaces
        $address = preg_replace( '/\s+/',    ' ', $address ); // reduce extra spaces

        return '<a href="https://www.google.com/search?q=' . urlencode( $address ) . '"'
             . ( $class ? " class=\"{$class}\"" : '' )
             . ' target="_blank">' . nl2br( e( $address ) ) . '</a>';

    }


    public static function MakeSelectOpt( string|int $key, string $value, string|int $selected = null ) : string
    {
        return '<option value="' . e( $key ) . '"'
             . ( $selected == $key ? ' selected' : '' )
             . '>' . e( $value ) . "</option>\n";
    }


    public static function TimezoneOpts( string $selected = null ) : string
    {

        $opts = '<option value="UTC"' . ( $selected == 'UTC' ? ' selected' : '' ) . ">UTC</option>\n";

        $current_continent = '';

        foreach ( timezone_identifiers_list() as $timezone )
        {

            if ( strpos( $timezone, '/' ) !== false )
            {

                list( $continent, $zone ) = explode( '/', $timezone, 2 );

                if ( $continent != $current_continent )
                {
                    $current_continent = $continent;
                    $opts .= "<optgroup label=\"" .  e( $continent ) . "\">\n";
                }

                $opts .= self::MakeSelectOpt( $timezone, str_replace( '_', ' ', $zone ), $selected );

            }

        }

        return $opts;

    }


}

<?php

namespace App\Classes\Redirectors;

use Auth;
use Request;
use Route;
use Illuminate\Http\Exceptions\HttpResponseException;


trait RedirectorTrait
{


    protected static $_always_ignore_routes = [

        // DO NOT put logout here
        'impersonate',

    ];


    public static function ShouldContinue() : bool
    {

        if ( ! Auth::check() or Request::ajax() )
        {
            return false;
        }

        if ( ! $route_name = Route::currentRouteName() )
        {
            // no route name
            return false;
        }

        //
        // ignore these routes
        //

        foreach ( array_merge( self::$_always_ignore_routes, self::$_ignore_routes ) as $route )
        {
            if ( false !== strpos( $route_name, $route ) )
            {
                return false;
            }
        }

        return true;

    }


    public static function Redirect( object $Model ) : void
    {
        throw new HttpResponseException(
            redirect()->route( self::$_redirect_route, $Model )
        );
    }

}

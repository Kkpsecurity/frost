<?php

/*
 *
 * WARNING:
 *
 *   This assumes Model is App\Models\Model
 *         and Observer is App\Observers\ModelObserver
 *
 * Override:
 *
 *   public static $observer = 'App\Observers\<NAME>Observer';
 *
 */

namespace KKP\Laravel\ModelTraits;


trait Observable
{

    public static function bootObservable() : void
    {

        if ( property_exists( self::class, 'observer' ) )
        {
            static::observe( self::$observer );
        }
        else
        {
            static::observe(
                str_replace( 'Models', 'Observers', get_called_class() ) . 'Observer'
            );
        }

    }

}

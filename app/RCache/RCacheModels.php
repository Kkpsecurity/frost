<?php

namespace App\RCache;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


trait RCacheModels
{


    public static function IsStaticModel( string $model_name )
    {
        return in_array( $model_name, self::$_static_models );
    }


    public static function LoadModelCache( string $model_name, bool $force_db_query = false ) : Collection
    {

        //
        // return static models
        //

        if ( ! $force_db_query && $Models = self::_getStatic( $model_name ) )
        {
            \kkpdebug("LoadModelCache :: {$model_name} :: Returning Static", 'RCacheDebug');
            return $Models;
        }


        //
        // retrieve from:
        //   ModelCache
        //   Redis
        //   DB
        //

        // Initialize ModelCaches if null
        if (self::$_ModelCaches === null) {
            self::$_ModelCaches = collect();
        }

        $model_key_name = self::_model_get_key_name( $model_name );

        if ( ! $force_db_query && self::$_ModelCaches->has( $model_name ) )
        {

            \kkpdebug("LoadModelCache :: {$model_name} :: Returning from ModelCaches", 'RCacheDebug');

            return self::$_ModelCaches->get( $model_name );

        }
        else if ( ! $force_db_query && self::exists( $model_name ) )
        {

            \kkpdebug("LoadModelCache :: {$model_name} :: Loading from Redis", 'RCacheDebug');

            $Models = $model_name::hydrate( array_map( self::Unserializer() , array_values( self::hgetall( $model_name ) ) ) )
                                  ->sortBy( $model_key_name )
                                   ->keyBy( $model_key_name );

        }
        else
        {

            \kkpdebug("LoadModelCache :: {$model_name} :: " . ($force_db_query ? 'RELOADING' : 'Loading') . ' from DB', 'RCache');

            $Models = $model_name::all()
                              ->sortBy( $model_key_name )
                               ->keyBy( $model_key_name );

            foreach ( $Models as $Model )
            {
                self::hset( $model_name, $Model->$model_key_name, self::Serialize( $Model ) );
            }

        }


        //
        // Cache models?
        //

        if ( self::IsStaticModel( $model_name ) )
        {

            \kkpdebug("LoadModelCache :: {$model_name} :: Caching Static", 'RCacheDebug');
            self::$_StaticCaches->put( $model_name, $Models );

        }
        else if ( self::$_cache_models )
        {

            \kkpdebug("LoadModelCache :: {$model_name} :: Caching Model", 'RCacheDebug');
            if (self::$_ModelCaches === null) {
                self::$_ModelCaches = collect();
            }
            self::$_ModelCaches->put( $model_name, $Models );

        }

        return $Models;

    }




    #####################
    ###               ###
    ###   internals   ###
    ###               ###
    #####################


    protected static function _getStatic( string $model_name ) : ?Collection
    {

        // Initialize static caches if not already done
        if (is_null(self::$_StaticCaches)) {
            self::$_StaticCaches = new Collection();
        }

        if ( self::$_StaticCaches->has( $model_name ) )
        {
            return self::$_StaticCaches->get( $model_name );
        }

        return null;

    }


    protected static function _getModelCache( string $model_name, $value, $key ) : object
    {

        \kkpdebug("_getModelCache :: {$model_name}", 'RCacheDebug');

        $Models = self::LoadModelCache( $model_name, false );

        if ( is_null( $value ) )
        {
            return $Models;
        }

        return $Models->where( $key, $value )->firstOrFail();

    }


    protected static function _model_get_key_name( string $model_name ) : string
    {

        $model_key_name = ( new $model_name )->getKeyName();

        if ( ! $model_key_name )
        {
            throw new Exception( "RCache:: {$model_name} has no key" );
        }

        if ( is_array( $model_key_name ) )
        {
            throw new Exception( "RCache:: {$model_name} has multiple keys" );
        }

        return $model_key_name;

    }



    #####################
    ###               ###
    ###   observers   ###
    ###               ###
    #####################


    public static function observer_saved( Model $Model ) : void
    {
        // don't pollute redis cache
        if ( self::exists( get_class( $Model ) ) )
        {
            self::hset( get_class( $Model ), $Model->getKey(), self::Serialize( $Model ) );
        }
    }


    public static function observer_deleted( Model $Model ) : void
    {
        // don't pollute redis cache
        if ( self::exists( get_class( $Model ) ) )
        {
            self::hdel( get_class( $Model ), $Model->getKey() );
        }
    }


}

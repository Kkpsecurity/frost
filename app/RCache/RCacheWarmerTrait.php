<?php

namespace App\RCache;

use DB;

use RCache;
use App\Models\User;
use KKP\Laravel\PgTk;


trait RCacheWarmerTrait
{

    //
    // methods called by CacheWarmer
    //


    public static function LoadAdmins( bool $force_db_query = false ) : void
    {

        $cache_key = 'admin_user_ids';

        if ( ! RCache::exists( $cache_key ) or $force_db_query )
        {

            kkpdebug( 'RCacheWarmer', 'Loading Admins' );

            $user_ids = [];

            PgTk::toModels(

                User::class,
                DB::select( 'SELECT * FROM sp_users_getadmins()' )

            )->map( function( $User ) use ( &$user_ids ) {

                array_push( $user_ids, $User->id );
                RCache::StoreUser( $User );

            });

			RCache::set( $cache_key, RCache::Serialize( $user_ids ) );

        }

    }


    protected static function LoadCountries() : void
    {
        if ( ! RCache::exists( 'countries' ) )
        {
            kkpdebug( 'RCacheWarmer', 'Loading Countries' );
            RCache::set( 'countries', RCache::Serialize( PgTk::toSimple( DB::select( 'SELECT * FROM sp_countries()' ) ) ) );
        }
    }

}

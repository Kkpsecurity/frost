<?php

namespace App\RCache;

use Exception;
use Illuminate\Support\Collection;

use App\Models\Role;
use App\Models\User;


trait RCacheUsers
{


    public static function RoleID( string $name ) : int
    {
        return self::_getStatic( Role::class )->firstOrFail( 'name', $name )->id;
    }


    #################
    ###           ###
    ###   users   ###
    ###           ###
    #################


    public static function User( int $user_id ) : User
    {

        if ( $record = self::get( self::UserCacheKey( $user_id ) ) )
        {

            kkpdebug( 'RCacheDebug', "User( {$user_id} ) :: Returning from Redis");
            return User::hydrate([ self::Unserialize( $record ) ])[0];

        }

        kkpdebug( 'RCacheDebug', "User( {$user_id} ) :: Searching DB");

        $User = User::findOrFail( $user_id );

        self::StoreUser( $User );

        return $User;

    }


    public static function UsersByIDs( array $user_ids ) : Collection
    {

        if ( ! is_simple( $user_ids ) )
        {
            throw new Exception( __METHOD__ . ' $user_ids not a simple array' );
        }

        $Users = new Collection;

        foreach ( $user_ids as $user_id )
        {
            $Users->put( $user_id, self::User( $user_id ) );
        }

        return $Users;

    }


    public static function StoreUser( User $User ) : self
    {

        $expires = ( $User->IsAdministrator() ? null : 86400 * 15 ); // 15 days

        self::set(
            self::UserCacheKey( $User->id ),
            self::Serialize( $User->withoutRelations()->toArray() ),
            $expires
        );

        return new self();

    }


    public static function Admins() : Collection
    {

        $cache_key = 'Admins';

        if ( self::$_ModelCaches->has( $cache_key ) )
        {
            kkpdebug( 'RCacheDebug', 'Admins() :: Returning from ModelCaches' );
            return self::$_ModelCaches->get( 'Admins' );
        }


        kkpdebug( 'RCacheDebug', 'Admins() :: Loading from Redis' );

        $Admins = new Collection;

        foreach ( self::Unserialize( self::get( 'admin_user_ids' ) ) as $user_id )
        {
            $Admins->put( $user_id, self::User( $user_id ) );
        }


        if ( self::$_cache_models )
        {
            kkpdebug( 'RCacheDebug', 'Admins() :: Caching Admins' );
            self::$_ModelCaches->put( $cache_key, $Admins );
        }

        return $Admins;

    }


    public static function Admin( int $user_id = null, $return_model = false ) : ?User
    {

        if ( $user_id )
        {
            return self::User( $user_id );
        }

        return ( $return_model ? ( new User ) : null );

    }



    #####################
    ###               ###
    ###   internals   ###
    ###               ###
    #####################


    protected static function UserCacheKey( $id ) : string
    {
        return 'user:' . $id;
    }


}

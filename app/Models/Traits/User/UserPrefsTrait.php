<?php
declare(strict_types=1);

namespace App\Models\Traits\User;

use Auth;

use App\Models\UserPref;


trait UserPrefsTrait
{


    public static $user_prefs_session_key = 'user_prefs';


    public function InitPrefs() : void
    {

        if ( ! session( self::$user_prefs_session_key ) )
        {
            $this->ReloadPrefs();
        }

    }


    public function ReloadPrefs() : void
    {

        if ( Auth::id() != $this->id )
        {
            return;
        }

        //
        // convert UserPrefs to KVP Collection
        //

        session([
            self::$user_prefs_session_key => UserPref::where( 'user_id', $this->id )->get()->pluck( 'pref_value', 'pref_name' )
        ]);

    }


    public function GetPref( string $pref_name, $default = null )
    {

        if ( Auth::id() == $this->id )
        {

            if ( session( self::$user_prefs_session_key ) )
            {
                return session( self::$user_prefs_session_key )->get( $pref_name )
                    ?: $default;
            }

            //
            // don't requery database
            //

            return $default;

        }

        //
        // attempt to retrieve from database
        //

        if ( $value = UserPref::where( 'user_id', $this->id )->where( 'pref_name', $pref_name )->first() )
        {
            return $value;
        }

        return $default;

    }


    public function SetPref( string $pref_name, $pref_value ) : void
    {

        UserPref::updateOrCreate([
            'user_id'    => $this->id,
            'pref_name'  => $pref_name
        ],
        [
            'pref_value' => $pref_value,
        ]);

        $this->ReloadPrefs();

    }


    public function DeletePref( string $pref_name ) : void
    {

        //
        // quietly delete; no error if not found
        //

        UserPref::where([

            'user_id'   => $this->id,
            'pref_name' => $pref_name,

        ])->delete();

        $this->ReloadPrefs();

    }


}

<?php

/**
 *
 * We'll use this later - J
 *
 */


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\UserPref;


class _CreateAdminUser extends Controller
{


    public function CreateAdminUser( Request $Request ) : User
    {

        // add validation
        $validated = $Request->validated();

        $timestamp = Carbon::now();

        $AdminUser = User::create([

            'id'                => User::where( 'id', '<', 10000 )->max( 'id' ) + 1,
            'is_active'         => true,
            'lname'             => $validated[ 'lname' ],
            'fname'             => $validated[ 'fname' ],
            'email'             => $validated[ 'email' ],
            'created_at'        => $timestamp,
            'updated_at'        => $timestamp,
            'email_verified_at' => $timestamp,
            'password'          => Hash::make( $validated[ 'password' ] ),

        ])->refresh();


        /*
         * may need to do these:
         *
        $AdminUser->forceFill([
            'is_active' => true,
            'role_id'   => $Request->input( 'role_id' ),
            'password'  => Hash::make( $validated[ 'password' ] ),
        ])->update();
        */


        UserPref::create([
            'user_id'       => $AdminUser->id,
            'pref_name'     => 'timezone',
            'pref_value'    => 'America/New_York'
        ]);


        return $AdminUser;

    }


}

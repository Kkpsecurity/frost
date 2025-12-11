<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;


class DateHelpers
{


    public static function DayStartSQL( string $timezone = 'America/New_York' ) : string
    {
        return Carbon::parse( date( 'Y-m-d 00:00:00' ), $timezone )
                 ->isoFormat( 'YYYY-MM-DD HH:mm:ssZ' );
    }

    public static function DayEndSQL( string $timezone = 'America/New_York' ) : string
    {
        return Carbon::parse( date( 'Y-m-d 23:59:59' ), $timezone )
                 ->isoFormat( 'YYYY-MM-DD HH:mm:ssZ' );
    }


}

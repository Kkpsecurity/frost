<?php

namespace App\Traits;

use Illuminate\Support\Carbon;


trait ExpirationTrait
{


    /**
     * Calculate expiration for [ Course, Exam ] based on policies
     *
     * returns  string|null  timestamp (UTC)
     */
    public function CalcExpire( bool $date_only = false ) : ?string
    {

        $fmt = ( $date_only ? 'YYYY-MM-DD 00:00:00' : 'YYYY-MM-DD HH:mm:ss' );

        if ( $this->policy_expire_minutes )
        {
            return Carbon::now( 'UTC' )->addMinutes( $this->policy_expire_minutes )->isoFormat( $fmt );
        }

        if ( $this->policy_expire_days )
        {
            return Carbon::now( 'UTC' )->addDays( $this->policy_expire_days )->isoFormat( $fmt );
        }

        return null;

    }


    /**
     * Determine if [ CourseAuth, ExamAuth ] is expired
     *
     * returns  boolean
     */
    public function IsExpired() : bool
    {

        if ( ! $this->expires_at ) return false;

        return Carbon::now( 'UTC' )->gt( new Carbon( $this->expires_at ) );

    }


}

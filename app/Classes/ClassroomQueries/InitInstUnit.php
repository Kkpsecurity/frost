<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use Auth;

use RCache;
use App\Models\CourseDate;
use App\Models\InstUnit;


trait InitInstUnit
{

    /**
     * Create or Retrieve InstUnit
     *   uses RCache::Locker
     *
     * @param   int|string|CourseDate  $CourseDate
     * @return  InstUnit|null
     */
    public static function InitInstUnit( int|string|CourseDate $CourseDate ) : ?InstUnit
    {

        $course_date_id = $CourseDate->id ?? (int) $CourseDate;

        $locker_lock_sec = 3;
        $locker_retry_ms = 50000; // 50ms


        //
        // InstUnit already created?
        //

        if ( $InstUnit = InstUnit::firstWhere( 'course_date_id', $course_date_id ) )
        {
            kkpdebug( 'ClassroomQueries_Dbg', 'InitInstUnit :: InstUnit already created' );
            return $InstUnit;
        }


        //
        // attempt to get lock
        //

        $locker_key = "InitInstUnit:{$course_date_id}";

        if ( ! RCache::Locker( $locker_key, $locker_lock_sec ) )
        {
            logger( "Locker {$locker_key} :: failed to get lock" );
        	return null;
        }


        //
        // create InstUnit
        //

        $InstUnit = InstUnit::create([

            'course_date_id' => $course_date_id,
            'created_by'     => Auth::id(),

        ])->refresh();


        //
        // cleanup
        //

        RCache::Locker( $locker_key, 0 );

        return $InstUnit;

    }

}

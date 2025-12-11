<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use RCache;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\StudentUnit;


trait InitStudentUnit
{

    /**
     * Create or Retrieve StudentUnit
     *   uses RCache::Locker
     *
     * @param   CourseAuth        $CourseAuth
     * @param   CourseDate        $CourseDate
     * @return  StudentUnit|null
     */
    public static function InitStudentUnit( CourseAuth $CourseAuth, CourseDate $CourseDate ) : ?StudentUnit
    {

        $locker_lock_sec = 3;
        $locker_retry_ms = 50000; // 50ms

        $log_prefix = "InitStudentUnit(CAID:{$CourseAuth->id},CDID:{$CourseDate->id})";


        //
        // validate CourseAuth
        //

        if ( $CourseAuth->completed_at )
        {
            kkpdebug( 'ClassroomQueries_Dbg', "{$log_prefix} CourseAuth COMPLETED" );
            return null;
        }

        if ( $CourseAuth->disabled_at )
        {
            kkpdebug( 'ClassroomQueries_Dbg', "{$log_prefix} CourseAuth DISABLED" );
            return null;
        }


        //
        // instructor is online?
        //

        if ( ! $InstUnit = $CourseDate->InstUnit )
        {
            kkpdebug( 'ClassroomQueries_Dbg', "{$log_prefix} no InstUnit" );
            return null;
        }


        //
        // instructor has closed the day
        //

        if ( $InstUnit->completed_at )
        {
            kkpdebug( 'ClassroomQueries_Dbg', "{$log_prefix} InstUnit->completed_at" );
            return null;
        }


        //
        // retrieve existing record
        //

        if ( $StudentUnit = self::_InitStudentUnit_GetStudentUnit( $CourseAuth, $CourseDate ) )
        {

            return $StudentUnit;

            //
            // StudentUnit still active ?
            //

            /*
             * 2024-01-16 per Richie
             *
            if ( $StudentUnit->completed_at )
            {
                kkpdebug( 'ClassroomQueries_Dbg', "{$log_prefix} StudentUnit completed" );
                return null;
            }
            else if ( $StudentUnit->ejected_at )
            {
                kkpdebug( 'ClassroomQueries_Dbg', "{$log_prefix} StudentUnit EJECTED" );
                return null;
            }
            else
            {
                kkpdebug( 'ClassroomQueries_Dbg', "{$log_prefix} Returning active StudentUnit" );
                return $StudentUnit;
            }
            */

        }


        //
        // attempt to get lock
        //

        $locker_key = "InitStudentUnit:{$CourseAuth->id}:{$CourseDate->id}";

        if ( ! RCache::Locker( $locker_key, $locker_lock_sec ) )
        {

            logger( "Locker {$locker_key} :: couldn't get lock - trying again" );

            usleep( $locker_retry_ms );

            if ( $StudentUnit = self::_InitStudentUnit_GetStudentUnit( $CourseAuth, $CourseDate ) )
            {
                logger( "Locker {$locker_key} :: retrieved record" );
                return $StudentUnit;
            }

        	logger( "Locker {$locker_key} :: failed to get lock" );

        	return null;

        }


        //
        // create StudentUnit
        //

        kkpdebug( 'ClassroomQueries_Dbg', "{$log_prefix} Creating new StudentUnit" );

        $StudentUnit = StudentUnit::create([

            'course_auth_id' => $CourseAuth->id,
            'course_unit_id' => $CourseDate->course_unit_id,
            'course_date_id' => $CourseDate->id,
            'inst_unit_id'   => $InstUnit->id,

        ])->refresh();


        //
        // cleanup
        //

        RCache::Locker( $locker_key, 0 );

        $CourseAuth->SetStartDate();

        $CourseAuth->PCLCache( true );


        return $StudentUnit;

    }


    protected static function _InitStudentUnit_GetStudentUnit( CourseAuth $CourseAuth, CourseDate $CourseDate ) : ?StudentUnit
    {
        return StudentUnit::where( 'course_auth_id', $CourseAuth->id )
                          ->where( 'course_date_id', $CourseDate->id )
                          ->first();
    }


}

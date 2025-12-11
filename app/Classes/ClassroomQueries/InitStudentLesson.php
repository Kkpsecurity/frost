<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use RCache;
use App\Models\InstLesson;
use App\Models\StudentLesson;
use App\Models\StudentUnit;


trait InitStudentLesson
{

    /**
     * Create or Retrieve StudentLesson based on active InstLesson
     *   uses RCache::Locker
     *
     * @param   StudentUnit         $StudentUnit
     * @return  StudentLesson|null
     */
    public static function InitStudentLesson( StudentUnit $StudentUnit ) : ?StudentLesson
    {

        $locker_lock_sec = 3;
        $locker_retry_ms = 50000; // 50ms

        $log_prefix = "InitStudentLesson(SUID:{$StudentUnit->id})";


        //
        // Is there an active InstLesson?
        //

        if ( ! $InstLesson = self::ActiveInstLesson( $StudentUnit->InstUnit ) )
        {
            kkpdebug( 'ClassroomQueries_Dbg', "{$log_prefix} No Active InstLesson" );
            return null;
        }


        //
        // StudentLesson already created?
        //

        if ( $StudentLesson = self::_InitStudentLesson_GetStudentLesson( $StudentUnit, $InstLesson ) )
        {
            kkpdebug( 'ClassroomQueries_Dbg', "{$log_prefix} Returning StudentLessonID {$StudentLesson->id}" );
            return $StudentLesson;
        }


        //
        // can Student join the Lesson?
        //

        if ( ! self::StudentCanJoinLesson( $StudentUnit, $InstLesson ) )
        {
            kkpdebug( 'ClassroomQueries', "{$log_prefix} TOO LATE" );
            return null;
        }


        //
        // attempt to get lock
        //

        $locker_key = "InitStudentLesson:{$StudentUnit->id}:{$InstLesson->lesson_id}";

        if ( ! RCache::Locker( $locker_key, $locker_lock_sec ) )
        {

            logger( "Locker {$locker_key} :: couldn't get lock - trying again" );

            usleep( $locker_retry_ms );

            if ( $StudentLesson = self::_InitStudentLesson_GetStudentLesson( $StudentUnit, $InstLesson ) )
            {
                logger( "Locker {$locker_key} :: retrieved record" );
                return $StudentLesson;
            }

        	logger( "Locker {$locker_key} :: failed to get lock" );
        	return null;

        }


        //
        // create StudentLesson
        //

        kkpdebug( 'ClassroomQueries_Dbg', "{$log_prefix} Creating StudentLesson" );

        $StudentLesson = StudentLesson::create([

            'student_unit_id'   => $StudentUnit->id,
            'lesson_id'         => $InstLesson->lesson_id,
            'inst_lesson_id'    => $InstLesson->id,

        ])->refresh();


        //
        // cleanup
        //

        RCache::Locker( $locker_key, 0 );

        return $StudentLesson;

    }


    protected static function _InitStudentLesson_GetStudentLesson( StudentUnit $StudentUnit, InstLesson $InstLesson ) : ?StudentLesson
    {
        return StudentLesson::where( 'student_unit_id', $StudentUnit->id )
                            ->where( 'inst_lesson_id',  $InstLesson->id  )
                            ->first();
    }


}

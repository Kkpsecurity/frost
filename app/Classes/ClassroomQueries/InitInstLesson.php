<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use Auth;

use RCache;
use App\Models\InstLesson;
use App\Models\InstUnit;
use App\Models\Lesson;


trait InitInstLesson
{

    /**
     * Create or Retrieve InstLesson
     *   uses RCache::Locker
     *
     * @param   int|string|InstUnit  $InstUnit
     * @param   int|string|Lesson    $Lesson
     * @return  InstLesson|null
     */
    public static function InitInstLesson( int|string|InstUnit $InstUnit, int|string|Lesson $Lesson ) : ?InstLesson
    {

        $inst_unit_id = $InstUnit->id ?? (int) $InstUnit;
        $lesson_id    = $Lesson->id   ?? (int) $Lesson;

        $locker_lock_sec = 3;
        $locker_retry_ms = 50000; // 50ms


        //
        // InstLesson already created?
        //

        if ( $InstLesson = InstLesson::where( 'inst_unit_id', $inst_unit_id )
                                     ->where( 'lesson_id', $lesson_id )
                                     ->first()
        )
        {
            kkpdebug( 'ClassroomQueries_Dbg', 'InitInstLesson :: InstLesson already created' );
            return $InstLesson;
        }


        //
        // attempt to get lock
        //

        $locker_key = "InitInstLesson:{$inst_unit_id}:{$lesson_id}";

        if ( ! RCache::Locker( $locker_key, $locker_lock_sec ) )
        {
        	logger( "Locker {$locker_key} :: failed to get lock" );
        	return null;
        }


        //
        // create InstLesson
        //

        $InstLesson = InstLesson::create([

            'inst_unit_id' => $inst_unit_id,
            'lesson_id'    => $lesson_id,
            'created_by'   => Auth::id(),

        ])->refresh();


        //
        // cleanup
        //

        RCache::Locker( $locker_key, 0 );

        return $InstLesson;

    }

}

<?php
declare(strict_types=1);

namespace App\Models\Traits\CourseAuth;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;


use RCache;


trait LessonsTrait
{


    public function AllLessonsCompleted() : bool
    {

        $completed_lesson_ids = $this->PCLCache();

        foreach ( $this->GetCourse()->GetLessons() as $Lesson )
        {
            if ( ! isset( $completed_lesson_ids[ $Lesson->id ] ) )
            {
                return false;
            }
        }

        return true;

    }


    public function CompletedLessons( string $carbon_format = null ) : array
    {

        $lessons = [];


        foreach ( $this->StudentUnits as $StudentUnit )
        {

            foreach ( $StudentUnit->StudentLessons()->whereNotNull( 'completed_at' )->get() as $StudentLesson )
            {

                $lesson_id    = $StudentLesson->lesson_id;
                $completed_at = Carbon::parse( $StudentLesson->completed_at );

                if ( isset( $lessons[ $lesson_id ] ) )
                {
                    if ( $completed_at->gt( $lessons[ $lesson_id ] ) )
                    {
                        $lessons[ $lesson_id ] = $completed_at;
                    }
                }
                else
                {
                    $lessons[ $lesson_id ] = $completed_at;
                }

            }

        }


        foreach ( $this->SelfStudyLessons()->whereNotNull( 'completed_at' )->get() as $SelfStudyLesson )
        {

            $lesson_id    = $SelfStudyLesson->lesson_id;
            $completed_at = Carbon::parse( $SelfStudyLesson->completed_at );

            if ( isset( $lessons[ $lesson_id ] ) )
            {
                if ( $completed_at->gt( $lessons[ $lesson_id ] ) )
                {
                    $lessons[ $lesson_id ] = $completed_at;
                }
            }
            else
            {
                $lessons[ $lesson_id ] = $completed_at;
            }

        }


        if ( $carbon_format )
        {

            $timezone = $this->UserTimezone( $this->GetUser() );

            foreach ( $lessons as $lesson_id => $completed_at )
            {
                $lessons[$lesson_id] = $completed_at->tz( $timezone )->isoFormat( $carbon_format );
            }

        }


        return $lessons;

    }


    public function IncompleteLessons() : Collection
    {

        return $this->GetCourse()
                    ->GetLessons()
                    ->whereNotIn( 'id', array_keys( $this->PCLCache() ) );

    }


    public function PCLCache( bool $force_update = false ) : array
    {

        $RedisConn = Cache::store( 'redis' )->connection();
        $redis_key = 'previous_completed_lessons:' . $this->id;

        if ( ! $RedisConn->exists( $redis_key ) or $force_update )
        {

            $CompletedLessons = $this->CompletedLessons();

            $RedisConn->set( $redis_key, RCache::Serialize( $CompletedLessons ), 'EX', 3600 ); // 1 hour

            return $CompletedLessons;

        }

        if ( $serialized = $RedisConn->get( $redis_key ) )
        {
            return RCache::Unserialize( $serialized );
        }

        logger( 'PLCCache reached the end?' );
        return [];

    }


}

<?php

namespace App\RCache;

use Illuminate\Support\Collection;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\CourseUnitLesson;
use App\Models\Lesson;
use App\Models\SiteConfig;


trait RCacheQueries
{


    ##################
    ###            ###
    ###   Course   ###
    ###            ###
    ##################


    public static function Course_CourseUnits( int $course_id ) : Collection
    {
        return self::CourseUnits()
                   ->where( 'course_id', $course_id )
                   ->sortBy( 'ordering' );
    }


    public static function Course_Lessons( int $course_id ) : Collection
    {

        $Lessons = new Collection();

        foreach( self::Course_CourseUnits( $course_id ) as $CourseUnit )
        {
            foreach ( $CourseUnit->GetLessons() as $Lesson )
            {
                $Lessons->put( $Lesson->id, $Lesson );
            }
        }

        return $Lessons;

    }


    public static function CourseLessonCounts() : array
    {

        $counts = [];

        foreach ( self::Courses() as $Course )
        {
            $counts[ $Course->id ] = self::Course_Lessons( $Course->id )->count();
        }

        return $counts;

    }


    ######################
    ###                ###
    ###   CourseUnit   ###
    ###                ###
    ######################


    public static function CourseUnit_CourseUnitLessons( CourseUnit $CourseUnit ) : Collection
    {
        return self::CourseUnitLessons()
                   ->where( 'course_unit_id', $CourseUnit->id )
                   ->sortBy( 'ordering' );
    }


    public static function CourseUnit_Lessons( CourseUnit $CourseUnit ) : Collection
    {
        return self::Lessons()
                   ->whereIn( 'id', self::CourseUnit_CourseUnitLessons( $CourseUnit )->pluck( 'lesson_id' )->toArray() );
    }


    ##################
    ###            ###
    ###   Lesson   ###
    ###            ###
    ##################


    public static function Lesson_Courses( Lesson $Lesson ) : Collection
    {

        $Courses = new Collection();

        foreach ( self::Lesson_CourseUnits( $Lesson ) as $CourseUnit )
        {
            $Courses->put( $CourseUnit->course_id, self::Courses( $CourseUnit->course_id ) );
        }

        return $Courses;

    }


    public static function Lesson_CourseUnits( Lesson $Lesson ) : Collection
    {
        return self::CourseUnits()
                   ->whereIn( 'id', self::Lesson_CourseUnitLessons( $Lesson )->pluck( 'course_unit_id' )->toArray() );
    }


    public static function Lesson_CourseUnitLessons( Lesson $Lesson ) : Collection
    {
        return self::CourseUnitLessons()
                   ->where( 'lesson_id', $Lesson->id );
    }



    #######################
    ###                 ###
    ###   SiteConfigs   ###
    ###                 ###
    #######################


    public static function SiteConfig( string $config_name ) : mixed
    {
        return self::LoadModelCache( SiteConfig::class )
                   ->where( 'config_name', $config_name )
                   ->firstOrFail()
                   ->config_value;
    }


    public static function SiteConfigsKVP() : array
    {
        return self::LoadModelCache( SiteConfig::class )
                   ->pluck( 'config_value', 'config_name' )
                   ->toArray();
    }


}

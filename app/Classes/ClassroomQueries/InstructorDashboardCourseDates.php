<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use RCache;
use App\Helpers\DateHelpers;
use App\Models\CourseDate;


trait InstructorDashboardCourseDates
{

    /**
     * Retrieve today's CourseDates for Instructor
     *   uses SiteConfig: instructor_pre_start_minutes
     *   uses SiteConfig: instructor_post_end_minutes
     *
     * @return  Collection  [CourseDate]
     */
    public static function InstructorDashboardCourseDates() : Collection
    {

        //
        // today's active CourseDates
        //

        return CourseDate::where( 'starts_at', '>=', DateHelpers::DayStartSQL() )
                         ->where( 'ends_at',   '<=', DateHelpers::DayEndSQL() )
                         ->where( 'is_active', true )
                          ->with( 'InstUnit' )
                           ->get()
                        ->filter(function( $CourseDate ) {

            //
            // filter by start / end times
            //

            return Carbon::now()->addMinutes(
                        RCache::SiteConfig( 'instructor_pre_start_minutes' )
                    )->gt( Carbon::parse( $CourseDate->starts_at ) )
                    &&
                    Carbon::parse( $CourseDate->ends_at )->addMinutes(
                        RCache::SiteConfig( 'instructor_post_end_minutes' )
                    )->gt( Carbon::now() );

        });

    }

}

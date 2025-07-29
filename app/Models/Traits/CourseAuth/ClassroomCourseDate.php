<?php

declare(strict_types=1);

namespace App\Models\Traits\CourseAuth;

/**
 * @file ClassroomCourseDate.php
 * @brief Trait for managing classroom course dates.
 * @details This trait provides methods to retrieve the current classroom course date for a user.
 */

use Illuminate\Support\Carbon;

use App\Services\RCache;

use App\Models\CourseDate;

use App\Helpers\DateHelpers;


trait ClassroomCourseDate
{

    public function ClassroomCourseDate(): ?CourseDate
    {

        #$allow_minutes_pre  = 30;
        #$allow_seconds_post = 90;


        $course_unit_ids = RCache::Course_CourseUnits($this->course_id)->pluck('id')->toArray();

        //
        // find candidate CourseDate
        //

        $CourseDate = CourseDate::where('starts_at', '>=', DateHelpers::DayStartSQL())
            ->where('ends_at',   '<=', DateHelpers::DayEndSQL())
            ->where('is_active', true)
            ->whereIn('course_unit_id', $course_unit_ids)
            ->get()
            ->first();

        //
        // no candidate CourseDate
        //

        if (! $CourseDate) {
            return null;
        }


        //
        // no InstUnit
        //

        if (! $CourseDate->InstUnit) {

            return null;

            //
            // allow students access shortly before class starts
            //
            /*
            if ( Carbon::now()->gt( Carbon::parse( $CourseDate->starts_at )->subMinutes( $allow_minutes_pre ) ) )
            {
                return $CourseDate;
            }
            else
            {
                return null;
            }
            */
        }


        //
        // active InstUnit
        //

        if (! $CourseDate->InstUnit->completed_at) {
            return $CourseDate;
        }


        /*
        //
        // allow students to complete final Challenge
        //
        if ( Carbon::now()->lt( Carbon::parse( $CourseDate->InstUnit->completed_at )->addSeconds( $allow_seconds_post ) ) )
        {
            return $CourseDate;
        }
        */

        return null;
    }
}

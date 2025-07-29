<?php

declare(strict_types=1);

namespace App\Models\Traits\CourseAuth;

use Illuminate\Support\Carbon;


trait ClassroomButton
{


    public function ClassroomButton(): bool
    {

        $allow_minutes_pre = 60;


        //
        // CourseAuth is expired
        //

        if ($this->IsExpired()) {
            return false;
        }

        //
        // CourseAuth is completed or disabled
        //

        if ($this->completed_at or $this->disabled_at) {
            return false;
        }


        //
        // Student has accessed the Course at least once
        //   or admin has allowed Exam access
        //

        if ($this->start_date or $this->exam_admin_id) {
            return true;
        }


        //
        // Do not allow student access to the Course until
        //   there is a live class in progress or about to start
        //

        if ($CourseDate = $this->ClassroomCourseDate()) {

            /*
            if ( Carbon::now()->gt( Carbon::parse( $CourseDate->starts_at )->subMinutes( $allow_minutes_pre ) ) )
            {
                logger( "{$this->id} / {$this->course_id} :: Ready" );
            }
            else
            {
                logger( "{$this->id} / {$this->course_id} :: Not Ready" );
            }
            */

            return Carbon::now()->gt(Carbon::parse($CourseDate->starts_at)->subMinutes($allow_minutes_pre));
        }


        //
        // Student must wait for the next CourseDate
        //

        return false;
    }
}

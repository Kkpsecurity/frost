<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\CourseAuth;
use App\Models\SelfStudyLesson;


function self_study_lessons()
{

    $CourseAuth = CourseAuth::firstWhere( 'user_id', 13 );

    DB::select( DB::raw( 'TRUNCATE self_study_lessons RESTART IDENTITY CASCADE' ) );

    foreach ([ 1, 3, 5, 6, 8, 9, 10, 11, 13, 14, 16, 17, 18 ] as $lesson_id )
    {
        SelfStudyLesson::create([
            'course_auth_id'    => $CourseAuth->id,
            'lesson_id'         => $lesson_id,
            'completed_at'      => Carbon::now(),
        ]);
    }

    return dumpcap( $CourseAuth->CompletedLessons( 'YYYY-MM-DD' ) );

}


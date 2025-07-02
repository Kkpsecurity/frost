<?php

namespace App\Helpers;

use stdClass;
use Auth;
use Exception;
use Illuminate\Support\Carbon;

use App\RCache;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\InstLesson;
use App\Models\StudentUnit;
use App\Models\StudentLesson;




class DevHelpers
{


    const COURSEID = 1;


    public static function CourseAuth( int $course_id = self::COURSEID ) : CourseAuth
    {

        abort_unless( Auth::check(), 500, 'You are not logged in' );

        if ( ! $CourseAuth = CourseAuth::firstWhere([ 'user_id' => Auth::id(), 'course_id' => $course_id ]) )
        {

            $CourseAuth = CourseAuth::create([

                'user_id'     => Auth::id(),
                'course_id'   => $course_id,
                'start_date'  => Carbon::now( 'UTC' )->isoFormat( 'YYYY-MM-DD' ),
                'expire_date' => RCache::Courses( $course_id )->CalcExpire( true )

            ])->refresh();

        }

        return $CourseAuth;

    }


    public static function CurrentCourseDate( int $course_id = self::COURSEID ) : CourseDate
    {

        return CourseDate::where( 'starts_at', '<=', date('c') )
                         ->where( 'ends_at',   '>=', date('c') )
                       ->whereIn( 'course_unit_id', RCache::Course_CourseUnits( $course_id )->pluck( 'id' )->toArray() )
                   ->firstOrFail();

    }


    public static function CurrentInstUnit( CourseDate $CourseDate ) : InstUnit
    {

        if ( ! $InstUnit = InstUnit::firstWhere( 'course_date_id', $CourseDate->id ) )
        {

            $InstUnit = InstUnit::create([

                'course_date_id' => $CourseDate->id,
                'created_by'     => 10, // Scott Steiman

            ])->refresh();

        }

        return $InstUnit;

    }


    public static function CourseAuthReset() : stdClass
    {

        abort_unless( Auth::check(), 500, 'You are not logged in' );

        $result = (object) [
            'exam_auths_deleted' => 0,
            'course_auths_reset' => 0,
        ];

        foreach ( Auth::user()->CourseAuths as $CourseAuth )
        {

            foreach ( $CourseAuth->ExamAuths as $ExamAuth )
            {
                $ExamAuth->delete();
                $result->exam_auths_deleted++;
            }

            $CourseAuth->forceFill([

                'completed_at'      => null,
                'is_passed'         => false,
                'dol_notified_at'   => null,
                'expire_date'       => null,
                'disabled_at'       => null,
                'disabled_reason'   => null,

            ])->update();

            $result->course_auths_reset++;

        }

        return $result;

    }


}

<?php
declare(strict_types=1);

namespace App\Models\Traits\User;

use Illuminate\Support\Carbon;

use App\Models\CourseAuth;
use App\Models\Course;


trait CourseAuthsTrait
{


    public function ActiveCourseAuths()
    {

        return $this->hasMany( CourseAuth::class, 'user_id' )
                    ->where( function( $query ) {
                        $query->whereNull( 'expire_date' )
                                ->orWhere( 'expire_date', '>', Carbon::now() );
                    })
                    ->whereNull( 'completed_at' )
                    ->whereNull( 'disabled_at'  );

    }


    public function InactiveCourseAuths()
    {

        return $this->hasMany( CourseAuth::class, 'user_id' )
                 ->whereNotIn( 'id', $this->ActiveCourseAuths->pluck( 'id' )->toArray() );

    }


    public function IsEnrolled( Course|int $Course_or_id ) : bool
    {

        return in_array(
            ( is_int( $Course_or_id ) ? $Course_or_id : $Course_or_id->id ),
            $this->ActiveCourseAuths->pluck( 'course_id' )->toArray()
        );

    }


}

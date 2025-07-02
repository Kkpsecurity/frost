<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

use RCache;
use App\Models\User;
use App\Models\Order;
use KKP\Laravel\PgTk;
use App\Models\Course;
use App\Models\ExamAuth;
use App\Models\RangeDate;
use App\Models\StudentUnit;
use App\Models\Validation;
use App\Traits\ExpirationTrait;
use App\Models\SelfStudyLesson;
use App\Models\Traits\CourseAuth\ClassroomButton;
use App\Models\Traits\CourseAuth\ClassroomCourseDate;
use App\Models\Traits\CourseAuth\ExamsTrait;
use App\Models\Traits\CourseAuth\LastInstructor;
use App\Models\Traits\CourseAuth\LessonsTrait;
use App\Models\Traits\CourseAuth\SetStartDateTrait;
use App\Presenters\CourseAuthPresenter;
use App\Presenters\PresentsTimeStamps;
use KKP\Laravel\ModelTraits\PgTimestamps;
use KKP\Laravel\ModelTraits\NoString;


class CourseAuth extends Model
{

    use ClassroomButton, ClassroomCourseDate, ExamsTrait, LastInstructor, LessonsTrait, SetStartDateTrait;
    use CourseAuthPresenter;
    use ExpirationTrait, PgTimestamps, PresentsTimeStamps;
    use NoString;


    protected $table        = 'course_auths';
    protected $primaryKey   = 'id';
    public    $timestamps   = true;

    protected $casts        = [

        'id'                => 'integer',

        'user_id'           => 'integer',
        'course_id'         => 'integer',

        'created_at'        => 'timestamp',
        'updated_at'        => 'timestamp',
        'agreed_at'         => 'timestamp',
        'completed_at'      => 'timestamp',

        'is_passed'         => 'boolean',

        'start_date'        => 'date',
        'expire_date'       => 'date',

        'disabled_at'       => 'timestamp',
        'disabled_reason'   => 'string',  // text

        'submitted_at'      => 'timestamp',
        'submitted_by'      => 'integer',
        'dol_tracking'      => 'string',  // 32

        'exam_admin_id'     => 'integer',
        'range_date_id'     => 'integer',

        'id_override'       => 'boolean',

    ];

    protected $guarded = [

        'id',
        'completed_at',
        'is_passed',

    ];

    protected $attributes = [

        'is_passed'     => false,
        'id_override'   => false,

    ];


    //
    // relationships
    //


    public function Course()
    {
        return $this->belongsTo( Course::class, 'course_id' );
    }

    public function ExamAuths()
    {
        return $this->hasMany( ExamAuth::class, 'course_auth_id' );
    }

    public function Order()
    {
        return $this->hasOne( Order::class, 'course_auth_id' );
    }

    public function RangeDate()
    {
        return $this->belongsTo( RangeDate::class, 'range_date_id' );
    }

    public function SelfStudyLessons()
    {
        return $this->hasMany( SelfStudyLesson::class, 'course_auth_id' );
    }

    public function StudentUnits()
    {
        return $this->hasMany( StudentUnit::class, 'course_auth_id' );
    }

    public function SubmittedBy()
    {
        return $this->belongsTo( User::class, 'submitted_by' );
    }

    public function User()
    {
        return $this->belongsTo( User::class, 'user_id' );
    }

    public function Validation()
    {
        return $this->hasOne( Validation::class, 'course_auth_id' );
    }


    //
    // cache queries
    //


    public function GetCourse() : Course
    {
        return RCache::Courses( $this->course_id );
    }

    public function GetExamAdmin() : ?User
    {
        return RCache::Admin( $this->exam_admin_id );
    }

    public function GetSubmittedBy() : ?User
    {
        return RCache::Admin( $this->submitted_by );
    }

    public function GetUser() : User
    {
        return RCache::User( $this->user_id );
    }


    //
    // helpers
    //


    public function IsActive() : bool
    {

        if ( ! $this->start_date )
        {
            return true;
        }

        if ( $this->completed_at or $this->disabled_at )
        {
            return false;
        }

        if ( $this->IsExpired() )
        {
            return false;
        }

        return true;

    }


    public function IsExpired() : bool
    {

        if ( ! $this->expire_date )
        {
            return false;
        }

        return Carbon::now()->gt( Carbon::parse( $this->expire_date ) );

    }


    public function IsFailed() : bool
    {
        return ( $this->completed_at && ! $this->is_passed );
    }


    public function MarkCompleted( bool $is_passed )
    {

        $this->forceFill([
            'completed_at'  => PgTk::now(),
            'is_passed'     => $is_passed,
        ])->update();

        $this->refresh();

    }


    /*
    private function _StudentLessons()
    {

        return \App\Models\StudentLesson::whereIn( 'student_unit_id',
                    \App\Models\StudentUnit::where( 'course_auth_id', $this->id )->get()->pluck( 'id' )
               )->get();

    }
    */

}

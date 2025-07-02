<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use RCache;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Models\InstUnit;
use App\Models\Validation;
use App\Presenters\PresentsTimeStamps;
use KKP\Laravel\ModelTraits\NoString;
use KKP\Laravel\ModelTraits\Observable;
use KKP\Laravel\ModelTraits\PgTimestamps;
use KKP\Laravel\Casts\JSONCast;


class StudentUnit extends Model
{

    use PgTimestamps, PresentsTimeStamps;
    use NoString, Observable;


    protected $table        = 'student_unit';
    protected $primaryKey   = 'id';
    public    $timestamps   = true;

    protected $casts        = [

        'id'                => 'integer',

        'course_auth_id'    => 'integer',
        'course_unit_id'    => 'integer',

        'course_date_id'    => 'integer',
        'inst_unit_id'      => 'integer',

        'created_at'        => 'timestamp',
        'updated_at'        => 'timestamp',
        'completed_at'      => 'timestamp',

        'ejected_at'        => 'timestamp',
        'ejected_for'       => 'string',  // 255

        'verified'          => JSONCast::class,
        #'verified'          => 'array',

        'unit_completed'    => 'boolean',

    ];

    protected $guarded      = [ 'id' ];

    protected $attributes   = [ 'unit_completed' => false ];


    //
    // relationships
    //


    public function CourseAuth()
    {
        return $this->belongsTo( CourseAuth::class, 'course_auth_id' );
    }

    public function CourseDate()
    {
        return $this->belongsTo( CourseDate::class, 'course_date_id' );
    }

    public function CourseUnit()
    {
        return $this->belongsTo( CourseUnit::class, 'course_unit_id' );
    }

    public function InstUnit()
    {
        return $this->belongsTo( InstUnit::class, 'inst_unit_id' );
    }

    public function StudentLessons()
    {
        return $this->hasMany( StudentLesson::class, 'student_unit_id' );
    }

    public function Validation()
    {
        return $this->hasOne( Validation::class, 'student_unit_id' );
    }


    //
    // cache queries
    //


    public function GetCourse() : Course
    {
        return RCache::Courses( $this->GetCourseUnit()->course_id );
    }

    public function GetCourseUnit() : CourseUnit
    {
        return RCache::CourseUnits( $this->course_unit_id );
    }

    public function GetUser() : User
    {
        return $this->CourseAuth->GetUser();
    }


    //
    // misc
    //


    public static function IDTypes() : array
    {
        return [
            'Drivers License',
            'State Issued ID',
            'Student ID',
            'Military / Govt ID',
            'Passport',
            'Personal Recognition',
            'Prevously Verified',
            'Other',
        ];
    }


    public static function EjectionReasons() : array
    {
        return [
            'Failed To Provide ID',
            'Sleeping / Inattentiveness',
            'Disruptive Behavior',
        ];
    }


}

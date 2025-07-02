<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use RCache;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\InstUnit;
use App\Models\StudentUnit;
use App\Presenters\PresentsTimeStamps;
use KKP\Laravel\ModelTraits\NoString;
use KKP\Laravel\ModelTraits\PgTimestamps;
use KKP\Laravel\ModelTraits\ResetsSequence;


class CourseDate extends Model
{

    use PgTimestamps, PresentsTimeStamps;
    use NoString, ResetsSequence;


    protected $table        = 'course_dates';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',
        'is_active'         => 'boolean',
        'course_unit_id'    => 'integer',
        'starts_at'         => 'timestamp',
        'ends_at'           => 'timestamp',

    ];

    protected $guarded      = [ 'id' ];


    //
    // relationships
    //


    public function CourseUnit()
    {
        return $this->belongsTo( CourseUnit::class, 'course_unit_id' );
    }

    public function InstUnit()
    {
        return $this->hasOne( InstUnit::class, 'course_date_id' );
    }

    public function StudentUnits()
    {
        return $this->hasMany( StudentUnit::class, 'course_date_id' );
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


    //
    // helpers
    //


    public function CalendarTitle() : string
    {
        return $this->GetCourse()->ShortTitle()
             . ' '
             . $this->GetCourseUnit()->title;
    }

}

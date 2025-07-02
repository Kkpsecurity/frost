<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;


use RCache;
use App\RCache\RCacheModelTrait;

use App\Models\CourseUnit;
use App\Models\Lesson;
use KKP\Laravel\ModelTraits\NoString;
#use KKP\Laravel\ModelTraits\Observable;
use KKP\TextTk;


class CourseUnitLesson extends Pivot
{

    #use NoString, Observable, RCacheModelTrait;
    use NoString, RCacheModelTrait;


    protected $table        = 'course_unit_lessons';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',

        'course_unit_id'    => 'integer',
        'lesson_id'         => 'integer',

        'progress_minutes'  => 'integer',
        'instr_seconds'     => 'integer',

        'ordering'          => 'integer',

    ];

    protected $guarded      = [ 'id' ];


    //
    // relationships
    //


    public function CourseUnit()
    {
        return $this->belongsTo( CourseUnit::class, 'course_unit_id' );
    }

    public function Lesson()
    {
        return $this->belongsTo( Lesson::class, 'lesson_id' );
    }


    //
    // cache queries
    //


    public function GetCourseUnit() : CourseUnit
    {
        return RCache::CourseUnits( $this->course_unit_id );
    }

    public function GetLesson() : Lesson
    {
        return RCache::Lessons( $this->lesson_id );
    }



    //
    // misc
    //


    public function ProgressHours() : string
    {
        return sprintf( '%0.1f', $this->progress_minutes / 60 );
    }

    public function InstructorHours() : string
    {
        return sprintf( '%0.1f', $this->instr_seconds / 3600 );
    }


}

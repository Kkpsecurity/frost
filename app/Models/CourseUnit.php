<?php

namespace App\Models;

use DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use RCache;
use App\RCache\RCacheModelTrait;

use App\Models\Course;
use App\Models\CourseUnitLesson;
use App\Models\Lesson;
use KKP\Laravel\ModelTraits\Observable;
use KKP\TextTk;


class CourseUnit extends Model
{

    use Observable, RCacheModelTrait;


    protected $table        = 'course_units';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',

        'course_id'         => 'integer',

        'title'             => 'string',  // 64
        'admin_title'       => 'string',  // 64

        'ordering'          => 'integer',

    ];

    protected $guarded      = [ 'id' ];

    public function __toString() { return "{$this->title}"; }


    //
    // relationships
    //


    public function Course()
    {
        return $this->belongsTo( Course::class, 'course_id' );
    }

    public function CourseUnitLessons()
    {
        return $this->hasMany( CourseUnitLesson::class, 'course_unit_id' );
    }

    public function Lessons()
    {
        return $this->belongsToMany( Lesson::class, 'course_unit_lessons', 'course_unit_id', 'lesson_id' )
                            ->using( CourseUnitLesson::class )
                          ->orderBy( 'ordering' );
    }


    //
    // incoming data filters
    //


    public function setTitleAttribute( $value )
    {
        $this->attributes[ 'title' ] = TextTk::Sanitize( $value );
    }

    public function setAdminTitleAttribute( $value )
    {
        $this->attributes[ 'admin_title' ] = TextTk::Sanitize( $value );
    }


    //
    // cache queries
    //


    public function GetCourse() : Course
    {
        return RCache::Courses( $this->course_id );
    }

    public function GetCourseUnitLessons() : Collection
    {
        return RCache::CourseUnit_CourseUnitLessons( $this );
    }

    public function GetLessons() : Collection
    {
        return RCache::CourseUnit_Lessons( $this );
    }


    //
    // helpers
    //


    public function LongTitle() : string
    {
        return preg_replace( '/ \(.*/', '', $this->GetCourse()->title_long )
               . " - {$this->title}";
    }


    public function TotalMinutes() : int
    {
        return DB::table( 'course_unit_lessons' )
                 ->where( 'course_unit_id', $this->id )
                   ->sum( 'progress_minutes' );
    }


}

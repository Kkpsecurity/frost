<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Services\RCache;
use App\Traits\RCacheModelTrait;

use App\Models\CourseUnit;
use App\Models\ExamQuestion;
use App\Models\CourseUnitLesson;

use App\Helpers\TextTk;
use App\Traits\Observable;


class Lesson extends Model
{

    use Observable, RCacheModelTrait;

    protected $table        = 'lessons';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',
        'title'             => 'string',  // 64
        'credit_minutes'    => 'integer',
        'video_seconds'     => 'integer',

    ];

    protected $guarded      = ['id'];

    public function __toString()
    {
        return $this->title;
    }


    //
    // relationships
    //


    public function CourseUnits()
    {
        return $this->belongsToMany(CourseUnit::class, 'course_unit_lessons', 'lesson_id', 'course_unit_id')
            ->using(CourseUnitLesson::class);
    }

    public function CourseUnitLessons()
    {
        return $this->hasMany(CourseUnitLesson::class, 'lesson_id');
    }

    public function ExamQuestions()
    {
        return $this->hasMany(ExamQuestion::class, 'lesson_id');
    }


    //
    // incoming data filters
    //


    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = TextTk::Sanitize($value);
    }


    //
    // cache queries
    //


    public function GetCourses()
    {
        return RCache::Lesson_Courses($this);
    }

    public function GetCourseUnits()
    {
        return RCache::Lesson_CourseUnits($this);
    }

    public function GetCourseUnitLessons()
    {
        return RCache::Lesson_CourseUnitLessons($this);
    }


    //
    // misc
    //


    public function CreditHours(): string
    {
        return sprintf('%0.1f', $this->credit_minutes / 60);
    }


    public function SelfStudyMinSeconds(): int
    {
        return $this->video_seconds - 300;  // 5 minutes
    }
}

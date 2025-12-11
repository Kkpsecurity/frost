<?php

namespace App\Models;

/**
 * @file CourseDate.php
 * @brief Model for course_dates table.
 * @details This model represents course dates, including attributes like start and end times, and associated course units.
 * It provides methods for managing course dates and retrieving related data.
 */

use Illuminate\Database\Eloquent\Model;

use App\Services\RCache;

use App\Models\Course;
use App\Models\InstUnit;
use App\Models\CourseUnit;
use App\Models\StudentUnit;

use App\Traits\NoString;
use App\Traits\PgTimestamps;
use App\Traits\ResetsSequence;
use App\Presenters\PresentsTimeStamps;


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
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'classroom_created_at' => 'datetime',
        'classroom_metadata' => 'array',

    ];

    protected $guarded      = ['id'];


    //
    // relationships
    //


    public function CourseUnit()
    {
        return $this->belongsTo(CourseUnit::class, 'course_unit_id');
    }

    public function InstUnit()
    {
        // Get the most recent active InstUnit (not completed)
        return $this->hasOne(InstUnit::class, 'course_date_id')
            ->whereNull('completed_at')
            ->latest('created_at');
    }

    public function InstUnits()
    {
        return $this->hasMany(InstUnit::class, 'course_date_id');
    }

    /**
     * Get InstUnit for today's date specifically
     * This ensures we only get InstUnits created on the same day as the CourseDate
     */
    public function TodaysInstUnit()
    {
        return $this->hasOne(InstUnit::class, 'course_date_id')
            ->whereDate('created_at', now()->format('Y-m-d'));
    }

    public function StudentUnits()
    {
        return $this->hasMany(StudentUnit::class, 'course_date_id');
    }

    public function Classroom()
    {
        return $this->hasOne(Classroom::class, 'course_date_id');
    }


    //
    // cache queries
    //


    public function GetCourse(): Course
    {
        return RCache::Courses($this->GetCourseUnit()->course_id);
    }

    public function GetCourseUnit(): CourseUnit
    {
        $result = RCache::CourseUnits($this->course_unit_id);

        // If RCache returns a Collection, get the first item
        if ($result instanceof \Illuminate\Database\Eloquent\Collection || $result instanceof \Illuminate\Support\Collection) {
            return $result->first();
        }

        return $result;
    }


    //
    // helpers
    //


    public function CalendarTitle(): string
    {
        return $this->GetCourse()->ShortTitle()
            . ' '
            . $this->GetCourseUnit()->title;
    }
}

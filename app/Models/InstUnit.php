<?php

namespace App\Models;

/**
 * @file InstUnit.php
 * @brief Model for inst_unit table.
 * @details This model represents an instructional unit in the system, including attributes like course date, assistant, and relationships to lessons and students.
 */

use Illuminate\Database\Eloquent\Model;

use App\Services\RCache;

use App\Models\User;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Models\InstLesson;
use App\Models\StudentUnit;

use App\Traits\NoString;
use App\Traits\PgTimestamps;
use App\Presenters\PresentsTimeStamps;


class InstUnit extends Model
{

    use PgTimestamps, PresentsTimeStamps;
    use NoString;


    protected $table        = 'inst_unit';
    protected $primaryKey   = 'id';
    public $timestamps      = false;

    protected $casts        = [

        'id'                => 'integer',
        'course_date_id'    => 'integer',

        'created_at'        => 'timestamp',
        'created_by'        => 'integer',
        'completed_at'      => 'timestamp',
        'completed_by'      => 'integer',

        'assistant_id'      => 'integer',

    ];

    protected $guarded = ['id'];


    //
    // relationships
    //


    public function CourseDate()
    {
        return $this->belongsTo(CourseDate::class, 'course_date_id');
    }

    public function InstLessons()
    {
        return $this->hasMany(InstLesson::class, 'inst_unit_id');
    }

    public function StudentUnits()
    {
        return $this->hasMany(StudentUnit::class, 'inst_unit_id');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function CompletedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Assistant()
    {
        return $this->belongsTo(User::class, 'user_id');
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
        return RCache::CourseUnits($this->CourseDate->course_unit_id);
    }

    public function GetCreatedBy(): User
    {
        return RCache::Admin($this->created_by);
    }

    public function GetCompletedBy(): ?User
    {
        return RCache::Admin($this->completed_by);
    }

    public function GetAssistant(): ?User
    {
        return RCache::Admin($this->assistant_id);
    }
}

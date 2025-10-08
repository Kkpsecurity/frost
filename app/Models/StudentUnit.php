<?php

namespace App\Models;

/**
 * @file StudentUnit.php
 * @brief Model for student_unit table.
 * @details This model represents a student's unit in a course, including attributes like course authorization ID,
 * course unit ID, inst unit ID, and various timestamps. It provides relationships to related models such as CourseAuth,
 * CourseDate, CourseUnit, InstUnit, StudentLessons, and Validation.
 */

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Casts\JSONCast;
use App\Services\RCache;
use App\Models\InstUnit;
use App\Traits\NoString;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Models\Validation;
use App\Traits\Observable;
use App\Traits\PgTimestamps;
use App\Presenters\PresentsTimeStamps;


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
        'attendance_type' => 'string',

    ];

    protected $guarded      = ['id'];

    protected $attributes   = ['unit_completed' => false];


    //
    // relationships
    //


    public function CourseAuth()
    {
        return $this->belongsTo(CourseAuth::class, 'course_auth_id');
    }

    public function CourseDate()
    {
        return $this->belongsTo(CourseDate::class, 'course_date_id');
    }

    public function CourseUnit()
    {
        return $this->belongsTo(CourseUnit::class, 'course_unit_id');
    }

    public function InstUnit()
    {
        return $this->belongsTo(InstUnit::class, 'inst_unit_id');
    }

    public function StudentLessons()
    {
        return $this->hasMany(StudentLesson::class, 'student_unit_id');
    }

    public function Validation()
    {
        return $this->hasOne(Validation::class, 'student_unit_id');
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
        return RCache::CourseUnits($this->course_unit_id);
    }

    public function GetUser(): User
    {
        return $this->CourseAuth->GetUser();
    }


    //
    // misc
    //


    public static function IDTypes(): array
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


    public static function EjectionReasons(): array
    {
        return [
            'Failed To Provide ID',
            'Sleeping / Inattentiveness',
            'Disruptive Behavior',
        ];
    }

    /**
     * Attendance Type Scopes and Helper Methods
     */

    /**
     * Scope to filter online attendance records
     */
    public function scopeOnlineAttendance($query)
    {
        return $query->where('attendance_type', 'online');
    }

    /**
     * Scope to filter offline attendance records
     */
    public function scopeOfflineAttendance($query)
    {
        return $query->where('attendance_type', 'offline');
    }

    /**
     * Check if this attendance record is online
     */
    public function isOnlineAttendance(): bool
    {
        return $this->attendance_type === 'online';
    }

    /**
     * Check if this attendance record is offline
     */
    public function isOfflineAttendance(): bool
    {
        return $this->attendance_type === 'offline';
    }
}

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

    /**
     * Get verified attribute - Check both JSON field and physical files
     *
     * Priority order:
     * 1. Check JSON 'verified' field for upload status (new system)
     * 2. Fall back to checking physical files on disk
     *
     * The verified JSON field has structure:
     * {
     *   "id_card_uploaded": true,
     *   "id_card_path": "...",
     *   "headshot_uploaded": true,
     *   "headshot_path": "...",
     *   "verified_at": "..."
     * }
     */
    public function getVerifiedAttribute($value): bool
    {
        // If verified field has a value, parse it (stored as JSON string in database)
        if ($value) {
            // Try to decode if it's a JSON string
            $verifiedData = $value;
            if (is_string($value)) {
                $verifiedData = json_decode($value, true) ?? $value;
            }

            // Check if both id_card and headshot are marked as uploaded in JSON
            if (is_array($verifiedData) || is_object($verifiedData)) {
                $verifiedArray = is_array($verifiedData) ? $verifiedData : (array) $verifiedData;
                $idCardUploaded = $verifiedArray['id_card_uploaded'] ?? false;
                $headshotUploaded = $verifiedArray['headshot_uploaded'] ?? false;

                // If both are uploaded in JSON, consider verified
                if ($idCardUploaded && $headshotUploaded) {
                    return true;
                }
            }
        }

        // Fall back to checking physical files
        // Get user from CourseAuth relationship (not from student_id which doesn't exist)
        $courseAuth = $this->courseAuth;
        if (!$courseAuth) {
            return false;
        }

        $user = $courseAuth->User; // This gets the related User instance
        if (!$user) {
            return false;
        }

        // Build filenames based on proper naming conventions
        $fullName = $user->fname . '_' . $user->lname;

        // ID Card: validations/idcards/{course_auth_id}_{firstname}_{lastname}.jpg
        $idCardPath = "validations/idcards/{$this->course_auth_id}_{$fullName}.jpg";

        // Headshot: validations/headshots/{studentunit_id}_{todays_date}_{firstname}_{lastname}.jpg
        $todaysDate = now()->format('Y-m-d');
        $headshotPath = "validations/headshots/{$this->id}_{$todaysDate}_{$fullName}.jpg";

        // Check if both files exist using Storage disk (works with local, public, S3)
        $idCardExists = \Storage::disk('media')->exists($idCardPath);
        $headshotExists = \Storage::disk('media')->exists($headshotPath);

        return $idCardExists && $headshotExists;
    }

    /**
     * Relationship to student (User)
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

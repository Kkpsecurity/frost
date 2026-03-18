<?php

namespace App\Models;

/**
 * @file CourseAuth.php
 * @brief Model for course_auths table.
 * @details This model represents course authorizations, including attributes like user ID, course ID, and various timestamps.
 * It provides methods for managing course authorizations and retrieving related data.
 */

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

use App\Services\RCache;

use App\Models\User;
use App\Models\Order;
use KKP\Laravel\PgTk;
use App\Models\Course;
use App\Models\ExamAuth;
use App\Models\RangeDate;
use App\Models\Validation;
use App\Models\StudentUnit;
use App\Models\SelfStudyLesson;

use App\Models\Traits\CourseAuth\ExamsTrait;
use App\Models\Traits\CourseAuth\LessonsTrait;
use App\Models\Traits\CourseAuth\LastInstructor;
use App\Models\Traits\CourseAuth\ClassroomButton;
use App\Models\Traits\CourseAuth\SetStartDateTrait;
use App\Models\Traits\CourseAuth\ClassroomCourseDate;

use App\Traits\NoString;
use App\Traits\PgTimestamps;
use App\Traits\ExpirationTrait;
use App\Presenters\PresentsTimeStamps;
use App\Presenters\CourseAuthPresenter;


class CourseAuth extends Model
{

    use ClassroomButton, ClassroomCourseDate, ExamsTrait, LastInstructor, LessonsTrait, SetStartDateTrait;
    use CourseAuthPresenter;
    use ExpirationTrait, PgTimestamps, PresentsTimeStamps;
    use NoString, Searchable;

    const SEARCHABLE_FIELDS = ['id', 'user_id', 'dol_tracking'];


    protected $table        = 'course_auths';
    protected $primaryKey   = 'id';
    public    $timestamps   = true;

    protected $casts        = [

        'id'                => 'integer',

        'user_id'           => 'integer',
        'course_id'         => 'integer',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'agreed_at' => 'datetime',
        'completed_at' => 'datetime',

        'is_passed'         => 'boolean',

        'start_date'        => 'date',
        'expire_date'       => 'date',

        'disabled_at' => 'datetime',
        'disabled_reason'   => 'string',  // text

        'submitted_at' => 'datetime',
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

    public function toSearchableArray(): array
    {
        return [
            'id'           => (string) $this->id,
            'user_id'      => (string) $this->user_id,
            'dol_tracking' => $this->dol_tracking ?? '',
        ];
    }


    //
    // relationships
    //


    public function Course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function ExamAuths()
    {
        return $this->hasMany(ExamAuth::class, 'course_auth_id');
    }

    public function Order()
    {
        return $this->hasOne(Order::class, 'course_auth_id');
    }

    public function RangeDate()
    {
        return $this->belongsTo(RangeDate::class, 'range_date_id');
    }

    public function SelfStudyLessons()
    {
        return $this->hasMany(SelfStudyLesson::class, 'course_auth_id');
    }

    public function StudentUnits()
    {
        return $this->hasMany(StudentUnit::class, 'course_auth_id');
    }

    public function SubmittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Validation()
    {
        return $this->hasOne(Validation::class, 'course_auth_id');
    }

    // Offline Play relationship
    public function offlinePlayBalance()
    {
        return $this->hasOne(OfflinePlayBalance::class);
    }


    //
    // cache queries
    //


    public function GetCourse(): Course
    {
        return RCache::Courses($this->course_id);
    }

    public function GetExamAdmin(): ?User
    {
        return RCache::Admin($this->exam_admin_id);
    }

    public function GetSubmittedBy(): ?User
    {
        return RCache::Admin($this->submitted_by);
    }

    public function GetUser(): User
    {
        return RCache::User($this->user_id);
    }


    //
    // helpers
    //


    /**
     * Returns true if this CourseAuth is locked because another g_class course
     * for the same user is currently in progress. Admin id_override bypasses this.
     */
    public function IsLocked(): bool
    {
        if ($this->id_override) {
            return false;
        }

        $course = $this->GetCourse();

        if ($course->course_type !== 'g_class') {
            return false;
        }

        // locked if another g_class course_auth for this user is in-progress
        return self::query()
            ->where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->whereHas('Course', fn($q) => $q->where('course_type', 'g_class'))
            ->whereNotNull('start_date')
            ->whereNull('completed_at')
            ->whereNull('disabled_at')
            ->exists();
    }

    /**
     * Returns a user-facing reason why the course is locked, or null if not locked.
     */
    public function LockReason(): ?string
    {
        if (! $this->IsLocked()) {
            return null;
        }

        $blocking = self::query()
            ->where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->whereHas('Course', fn($q) => $q->where('course_type', 'g_class'))
            ->whereNotNull('start_date')
            ->whereNull('completed_at')
            ->whereNull('disabled_at')
            ->with('Course')
            ->first();

        $courseName = $blocking?->Course?->title ?? 'another course';

        return "You must complete {$courseName} before starting this course.";
    }

    /**
     * Returns true if the user is eligible to enroll in a new course of this course_type.
     * Returns false if the renewal cycle has not expired since their last passed completion.
     */
    public function IsRenewalEligible(): bool
    {
        $course = $this->GetCourse();

        if (! $course->renewal_cycle_months) {
            return true;
        }

        $lastPassed = self::query()
            ->where('user_id', $this->user_id)
            ->whereHas('Course', fn($q) => $q->where('course_type', $course->course_type))
            ->whereNotNull('completed_at')
            ->where('is_passed', true)
            ->orderByDesc('completed_at')
            ->first();

        if (! $lastPassed) {
            return true;
        }

        $eligibleFrom = Carbon::parse($lastPassed->completed_at)->addMonths($course->renewal_cycle_months);

        return Carbon::today()->gte($eligibleFrom);
    }

    /**
     * Returns the earliest date the user can re-enroll in this course_type,
     * or null if already eligible (or no renewal cycle applies).
     */
    public function RenewalEligibleFrom(): ?Carbon
    {
        $course = $this->GetCourse();

        if (! $course->renewal_cycle_months) {
            return null;
        }

        $lastPassed = self::query()
            ->where('user_id', $this->user_id)
            ->whereHas('Course', fn($q) => $q->where('course_type', $course->course_type))
            ->whereNotNull('completed_at')
            ->where('is_passed', true)
            ->orderByDesc('completed_at')
            ->first();

        if (! $lastPassed) {
            return null;
        }

        $eligibleFrom = Carbon::parse($lastPassed->completed_at)->addMonths($course->renewal_cycle_months);

        return Carbon::today()->lt($eligibleFrom) ? $eligibleFrom : null;
    }

    public function IsActive(): bool
    {

        if (! $this->start_date) {
            return true;
        }

        if ($this->completed_at or $this->disabled_at) {
            return false;
        }

        if ($this->IsExpired()) {
            return false;
        }

        return true;
    }


    public function IsExpired(): bool
    {

        if (! $this->expire_date) {
            return false;
        }

        return Carbon::now()->gt(Carbon::parse($this->expire_date));
    }


    public function IsFailed(): bool
    {
        return ($this->completed_at && ! $this->is_passed);
    }


    public function MarkCompleted(bool $is_passed)
    {
        $completedAt = Carbon::now();

        $fillData = [
            'completed_at' => PgTk::now(),
            'is_passed'    => $is_passed,
        ];

        // Auto-set expire_date when passed and course has a renewal cycle
        if ($is_passed) {
            $course = $this->GetCourse();
            if ($course->renewal_cycle_months) {
                $fillData['expire_date'] = $completedAt
                    ->addMonths($course->renewal_cycle_months)
                    ->toDateString();
            }
        }

        $this->forceFill($fillData)->update();

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

<?php

namespace App\Models;

/**
 * @file SelfStudyLesson.php
 * @brief Model for self_study_lessons table.
 * @details This model represents a self-study lesson, including attributes like course authorization ID,
 * lesson ID, timestamps, and seconds viewed. It provides relationships to related models such as CourseAuth
 * and Lesson, and methods for retrieving the associated lesson.
 */

use Illuminate\Database\Eloquent\Model;

use App\Models\Lesson;
use App\Services\RCache;
use App\Models\CourseAuth;

use App\Traits\NoString;
use App\Traits\Observable;
use App\Traits\PgTimestamps;
use App\Presenters\PresentsTimeStamps;


class SelfStudyLesson extends Model
{

    use PgTimestamps, PresentsTimeStamps;
    use NoString, Observable;


    protected $table        = 'self_study_lessons';
    protected $primaryKey   = 'id';
    public    $timestamps   = true;

    protected $casts        = [

        'id'                => 'integer',

        'course_auth_id'    => 'integer',
        'lesson_id'         => 'integer',

        'created_at'        => 'timestamp',
        'updated_at'        => 'timestamp',
        'agreed_at' => 'timestamp',
        'completed_at'      => 'timestamp',
        'dnc_at' => 'timestamp', // Did Not Complete - for failed lessons

        'seconds_viewed'    => 'integer',
        'credit_minutes' => 'integer',

        // Session management
        'session_expires_at' => 'timestamp',
        'session_duration_minutes' => 'integer',

        // Pause tracking
        'total_pause_minutes_allowed' => 'integer',
        'total_pause_minutes_used' => 'integer',
        'pause_intervals' => 'array', // JSON array of intervals

        // Progress tracking
        'video_duration_seconds' => 'integer',
        'playback_progress_seconds' => 'integer',
        'completion_percentage' => 'decimal:2',

        // Quota tracking
        'quota_consumed_minutes' => 'integer',

        // Recovery tracking
        'original_student_lesson_id' => 'integer',
        'is_redo' => 'boolean',
        'redo_passed' => 'boolean',

    ];

    protected $guarded      = ['id'];

    protected $attributes   = [

        'seconds_viewed'    => 0,

    ];


    //
    // relationships
    //


    public function CourseAuth()
    {
        return $this->belongsTo(CourseAuth::class, 'course_auth_id');
    }

    public function Lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function OriginalStudentLesson()
    {
        return $this->belongsTo(StudentLesson::class, 'original_student_lesson_id');
    }


    //
    // cache queries
    //


    public function GetLesson(): Lesson
    {
        return RCache::Lessons($this->lesson_id);
    }


    //
    // status methods
    //


    /**
     * Check if lesson was completed successfully
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Check if lesson failed (did not complete)
     */
    public function isFailed(): bool
    {
        return $this->dnc_at !== null;
    }

    /**
     * Mark lesson as failed
     */
    public function markAsFailed(): void
    {
        $this->dnc_at = now();
        $this->save();
    }

    /**
     * Mark lesson as completed
     */
    public function markAsCompleted(): void
    {
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Get lesson status
     */
    public function getStatus(): string
    {
        if ($this->isCompleted()) {
            return 'completed';
        }

        if ($this->isFailed()) {
            return 'failed';
        }

        if ($this->agreed_at !== null) {
            return 'in-progress';
        }

        return 'not-started';
    }

    /*
    |--------------------------------------------------------------------------
    | Session Status Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if session is currently active
     */
    public function isSessionActive(): bool
    {
        return $this->agreed_at !== null
            && $this->completed_at === null
            && $this->dnc_at === null
            && $this->session_expires_at > now();
    }

    /**
     * Check if session has expired
     */
    public function isSessionExpired(): bool
    {
        return $this->session_expires_at !== null && $this->session_expires_at <= now();
    }

    /*
    |--------------------------------------------------------------------------
    | Pause Tracking Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get remaining pause time in minutes
     */
    public function getRemainingPauseMinutes(): int
    {
        return max(0, $this->total_pause_minutes_allowed - $this->total_pause_minutes_used);
    }

    /**
     * Check if there's pause time remaining
     */
    public function hasPauseTimeRemaining(): bool
    {
        return $this->getRemainingPauseMinutes() > 0;
    }

    /**
     * Consume pause time
     *
     * @param int $minutes Minutes to consume
     * @return void
     */
    public function consumePauseTime(int $minutes): void
    {
        $this->total_pause_minutes_used = min(
            $this->total_pause_minutes_allowed,
            $this->total_pause_minutes_used + $minutes
        );
        $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Progress Tracking Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Update playback progress
     *
     * @param int $playbackSeconds Current playback position
     * @return void
     */
    public function updateProgress(int $playbackSeconds): void
    {
        $this->playback_progress_seconds = $playbackSeconds;

        if ($this->video_duration_seconds > 0) {
            $this->completion_percentage = ($playbackSeconds / $this->video_duration_seconds) * 100;
        }

        $this->save();
    }

    /**
     * Check if completion threshold is met
     *
     * @return bool True if meets threshold (default 80%)
     */
    public function meetsCompletionThreshold(): bool
    {
        $threshold = config('self_study.completion_threshold', 80);
        return $this->completion_percentage >= $threshold;
    }

    /**
     * Get time remaining in session (minutes)
     *
     * @return int Minutes until session expires
     */
    public function getSessionTimeRemaining(): int
    {
        if ($this->session_expires_at === null) {
            return 0;
        }

        $seconds = now()->diffInSeconds($this->session_expires_at, false);
        return max(0, (int) ceil($seconds / 60));
    }
}


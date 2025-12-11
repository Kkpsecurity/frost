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
}

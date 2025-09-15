<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\NoString;
use App\Traits\PgTimestamps;
use App\Presenters\PresentsTimeStamps;

/**
 * @file Classroom.php
 * @brief Model for classrooms table.
 * @details This model represents a live classroom session, including meeting links, participants, and materials.
 */
class Classroom extends Model
{
    use PgTimestamps, PresentsTimeStamps;
    use NoString;

    protected $table = 'classrooms';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $casts = [
        'id' => 'integer',
        'course_date_id' => 'integer',
        'course_unit_id' => 'integer',
        'title' => 'string',
        'starts_at' => 'timestamp',
        'ends_at' => 'timestamp',
        'modality' => 'string',
        'location' => 'string',
        'status' => 'string',
        'meeting_url' => 'string',
        'meeting_id' => 'string',
        'meeting_config' => 'array',
        'join_instructions' => 'string',
        'capacity' => 'integer',
        'waitlist_policy' => 'string',
        'late_join_cutoff' => 'timestamp',
        'classroom_created_at' => 'timestamp',
        'created_by' => 'integer',
        'creation_metadata' => 'array',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $guarded = ['id'];

    protected $attributes = [
        'modality' => 'online',
        'status' => 'preparing',
        'capacity' => 30,
        'waitlist_policy' => 'none',
    ];

    //
    // Relationships
    //

    public function courseDate(): BelongsTo
    {
        return $this->belongsTo(CourseDate::class, 'course_date_id');
    }

    public function courseUnit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class, 'course_unit_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ClassroomParticipant::class, 'classroom_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(ClassroomParticipant::class, 'classroom_id')
                    ->where('role', 'student');
    }

    public function instructors(): HasMany
    {
        return $this->hasMany(ClassroomParticipant::class, 'classroom_id')
                    ->where('role', 'instructor');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ClassroomMaterial::class, 'classroom_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //
    // Helper Methods
    //

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function currentEnrollment(): int
    {
        return $this->students()->count();
    }

    public function availableSpots(): int
    {
        return max(0, $this->capacity - $this->currentEnrollment());
    }

    public function enrollmentPercentage(): float
    {
        if ($this->capacity <= 0) {
            return 0;
        }
        return round(($this->currentEnrollment() / $this->capacity) * 100, 2);
    }

    public function getCourse(): Course
    {
        return $this->courseDate->GetCourse();
    }

    public function getInstructor(): ?User
    {
        $instructorParticipant = $this->instructors()->first();
        return $instructorParticipant ? $instructorParticipant->user : null;
    }

    //
    // Scopes
    //

    public function scopeToday($query)
    {
        return $query->whereDate('starts_at', today('America/New_York'));
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopeForCourseDate($query, int $courseDateId)
    {
        return $query->where('course_date_id', $courseDateId);
    }
}

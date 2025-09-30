<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * StudentSession Model
 *
 * Represents student learning sessions (both online and offline)
 * Tracks session duration, activities, and completion metrics
 */
class StudentSession extends Model
{
    use HasFactory;

    protected $table = 'student_sessions';

    protected $fillable = [
        'user_id',
        'course_auth_id',
        'course_date_id',
        'session_type',
        'started_at',
        'ended_at',
        'duration_minutes',
        'activities_count',
        'lessons_accessed',
        'completion_rate',
        'ip_address',
        'user_agent',
        'data',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'lessons_accessed' => 'array',
        'completion_rate' => 'float',
        'data' => 'array',
    ];

    // =============================================================================
    // RELATIONSHIPS
    // =============================================================================

    /**
     * Get the user this session belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course authorization for this session
     */
    public function courseAuth(): BelongsTo
    {
        return $this->belongsTo(CourseAuth::class);
    }

    /**
     * Get the course date this session is associated with
     */
    public function courseDate(): BelongsTo
    {
        return $this->belongsTo(CourseDate::class);
    }

    /**
     * Get all activities that occurred during this session
     */
    public function activities(): HasMany
    {
        return $this->hasMany(StudentActivity::class, 'user_id', 'user_id')
                    ->where('course_auth_id', $this->course_auth_id)
                    ->whereBetween('created_at', [
                        $this->started_at,
                        $this->ended_at ?? now()
                    ]);
    }

    // =============================================================================
    // SCOPES
    // =============================================================================

    /**
     * Scope for active sessions (not ended)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('ended_at');
    }

    /**
     * Scope for completed sessions
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('ended_at');
    }

    /**
     * Scope for offline sessions
     */
    public function scopeOffline($query)
    {
        return $query->where('session_type', 'offline');
    }

    /**
     * Scope for online sessions
     */
    public function scopeOnline($query)
    {
        return $query->where('session_type', 'online');
    }

    /**
     * Scope for sessions within date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('started_at', [$startDate, $endDate]);
    }

    /**
     * Scope for sessions by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for sessions by course
     */
    public function scopeForCourse($query, int $courseAuthId)
    {
        return $query->where('course_auth_id', $courseAuthId);
    }

    // =============================================================================
    // ACCESSORS & MUTATORS
    // =============================================================================

    /**
     * Check if session is currently active
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->ended_at === null;
    }

    /**
     * Get formatted session duration
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_minutes) {
            return '0 minutes';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . ' minutes';
    }

    /**
     * Get current session duration (for active sessions)
     */
    public function getCurrentDurationAttribute(): int
    {
        if ($this->ended_at) {
            return $this->duration_minutes ?? 0;
        }

        return $this->started_at->diffInMinutes(now());
    }

    /**
     * Get session status
     */
    public function getStatusAttribute(): string
    {
        if ($this->is_active) {
            return 'active';
        }

        if ($this->completion_rate >= 80) {
            return 'completed';
        }

        if ($this->completion_rate >= 50) {
            return 'partial';
        }

        return 'abandoned';
    }

    /**
     * Get lessons accessed count
     */
    public function getLessonsAccessedCountAttribute(): int
    {
        return count($this->lessons_accessed ?? []);
    }

    // =============================================================================
    // METHODS
    // =============================================================================

    /**
     * Calculate and update session metrics
     */
    public function updateMetrics(): void
    {
        $activities = $this->activities()->get();

        $this->update([
            'activities_count' => $activities->count(),
            'lessons_accessed' => $activities->whereNotNull('lesson_id')
                                           ->pluck('lesson_id')
                                           ->unique()
                                           ->values()
                                           ->toArray(),
        ]);
    }

    /**
     * End the session with final calculations
     */
    public function endSession(): void
    {
        $endTime = now();
        $duration = $endTime->diffInMinutes($this->started_at);

        // Calculate completion rate based on activities
        $activities = $this->activities()->get();
        $lessonStarts = $activities->where('activity_type', 'like', '%lesson_start%')->count();
        $lessonCompletes = $activities->where('activity_type', 'like', '%lesson_complete%')->count();

        $completionRate = $lessonStarts > 0 ? min(100, ($lessonCompletes / $lessonStarts) * 100) : 0;

        $this->update([
            'ended_at' => $endTime,
            'duration_minutes' => $duration,
            'completion_rate' => $completionRate,
        ]);

        $this->updateMetrics();
    }
}

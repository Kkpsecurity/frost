<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StudentActivity Model
 *
 * Tracks all student activities across the learning platform
 * including entry events, lesson activities, and session interactions
 */
class StudentActivity extends Model
{
    use HasFactory;

    protected $table = 'student_activities';

    protected $fillable = [
        'user_id',
        'course_auth_id',
        'course_date_id',
        'student_unit_id',
        'lesson_id',
        'activity_type',
        'category',
        'data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =============================================================================
    // RELATIONSHIPS
    // =============================================================================

    /**
     * Get the user that performed this activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course authorization this activity belongs to
     */
    public function courseAuth(): BelongsTo
    {
        return $this->belongsTo(CourseAuth::class);
    }

    /**
     * Get the course date this activity is associated with
     */
    public function courseDate(): BelongsTo
    {
        return $this->belongsTo(CourseDate::class);
    }

    /**
     * Get the student unit this activity relates to
     */
    public function studentUnit(): BelongsTo
    {
        return $this->belongsTo(StudentUnit::class);
    }

    /**
     * Get the lesson this activity is associated with
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    // =============================================================================
    // SCOPES
    // =============================================================================

    /**
     * Scope to filter by activity category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by activity type
     */
    public function scopeActivityType($query, string $activityType)
    {
        return $query->where('activity_type', $activityType);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for entry activities (school entry, classroom entry)
     */
    public function scopeEntryActivities($query)
    {
        return $query->where('category', 'entry');
    }

    /**
     * Scope for offline activities
     */
    public function scopeOffline($query)
    {
        return $query->where('category', 'offline');
    }

    /**
     * Scope for online activities
     */
    public function scopeOnline($query)
    {
        return $query->where('category', 'online');
    }

    // =============================================================================
    // ACCESSORS & MUTATORS
    // =============================================================================

    /**
     * Get formatted activity type for display
     */
    public function getFormattedActivityTypeAttribute(): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $this->activity_type));
    }

    /**
     * Get activity duration if available in data
     */
    public function getDurationAttribute(): ?int
    {
        return $this->data['duration_minutes'] ?? $this->data['duration'] ?? null;
    }

    /**
     * Check if this is an entry activity
     */
    public function getIsEntryActivityAttribute(): bool
    {
        return $this->category === 'entry';
    }

    /**
     * Check if this is a session-related activity
     */
    public function getIsSessionActivityAttribute(): bool
    {
        return in_array($this->activity_type, [
            'school_entry',
            'classroom_entry',
            'offline_session_start',
            'offline_session_end',
            'online_session_start',
            'online_session_end'
        ]);
    }
}

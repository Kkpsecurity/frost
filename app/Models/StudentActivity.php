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

    protected $table = 'student_activity';

    protected $fillable = [
        'course_auth_id',
        'student_unit_id',
        'inst_unit_id',
        'action',
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
     * Get the course authorization this activity belongs to
     */
    public function courseAuth(): BelongsTo
    {
                return $this->belongsTo(CourseAuth::class, 'course_auth_id');
    }

    /**
     * Get the student unit this activity relates to
     */
    public function studentUnit(): BelongsTo
    {
        return $this->belongsTo(StudentUnit::class, 'student_unit_id');
    }

    /**
     * Get the instructor unit associated with this activity
     */
    public function instUnit(): BelongsTo
    {
        return $this->belongsTo(InstUnit::class, 'inst_unit_id');
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
        return in_array($this->action, [
            'student_unit_created',
            'onboarding_started',
            'classroom_entered',
            'session_ended'
        ]);
    }

    // =============================================================================
    // ONBOARDING STATIC METHODS
    // =============================================================================

    /**
     * Log a student activity event.
     * 
     * @param int $courseAuthId
     * @param int $studentUnitId
     * @param string $action
     * @param int|null $instUnitId
     * @return self
     */
    public static function logActivity(int $courseAuthId, int $studentUnitId, string $action, ?int $instUnitId = null): self
    {
        return self::create([
            'course_auth_id' => $courseAuthId,
            'student_unit_id' => $studentUnitId,
            'inst_unit_id' => $instUnitId,
            'action' => $action
        ]);
    }

    /**
     * Get activities for a specific student unit.
     * 
     * @param int $studentUnitId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForStudentUnit(int $studentUnitId)
    {
        return self::where('student_unit_id', $studentUnitId)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Check if a specific action has been logged for a student unit.
     * 
     * @param int $studentUnitId
     * @param string $action
     * @return bool
     */
    public static function hasAction(int $studentUnitId, string $action): bool
    {
        return self::where('student_unit_id', $studentUnitId)
            ->where('action', $action)
            ->exists();
    }

    /**
     * Get commonly tracked onboarding activity actions.
     * 
     * @return array
     */
    public static function getOnboardingActionTypes(): array
    {
        return [
            'student_unit_auto_created' => 'StudentUnit automatically created',
            'onboarding_started' => 'Onboarding process started',
            'agreement_accepted' => 'Student agreement accepted',
            'rules_acknowledged' => 'Classroom rules acknowledged',
            'identity_verified' => 'Identity verification completed',
            'onboarding_completed' => 'Onboarding process completed',
            'classroom_entered' => 'Student entered classroom'
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentActivity extends Model
{
    protected $table = 'student_activity';

    protected $fillable = [
        'user_id',
        'course_auth_id',
        'course_date_id',
        'student_unit_id',
        'inst_unit_id',
        'category',
        'activity_type',
        'description',
        'data',
        'metadata',
        'session_id',
        'ip_address',
        'user_agent',
        'url',
        'started_at',
        'ended_at',
        'duration_seconds',
    ];

    protected $casts = [
        'data' => 'array',
        'metadata' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Activity Categories
     */
    public const CATEGORY_ENTRY = 'entry';           // Site/classroom entry/exit
    public const CATEGORY_NAVIGATION = 'navigation';  // Page views, clicks
    public const CATEGORY_INTERACTION = 'interaction'; // Button clicks, form submissions
    public const CATEGORY_AGREEMENT = 'agreement';    // Terms, rules acceptance
    public const CATEGORY_SYSTEM = 'system';          // Tab visibility, errors

    /**
     * Activity Types
     */
    public const TYPE_SITE_ENTRY = 'site_entry';
    public const TYPE_SITE_EXIT = 'site_exit';
    public const TYPE_CLASSROOM_ENTRY = 'classroom_entry';
    public const TYPE_CLASSROOM_EXIT = 'classroom_exit';
    public const TYPE_PAGE_VIEW = 'page_view';
    public const TYPE_BUTTON_CLICK = 'button_click';
    public const TYPE_AGREEMENT_ACCEPTED = 'agreement_accepted';
    public const TYPE_TERMS_ACCEPTED = 'terms_accepted';
    public const TYPE_RULES_ACCEPTED = 'rules_accepted';
    public const TYPE_ID_CARD_UPLOADED = 'id_card_uploaded';
    public const TYPE_HEADSHOT_UPLOADED = 'headshot_uploaded';
    public const TYPE_TAB_HIDDEN = 'tab_hidden';
    public const TYPE_TAB_VISIBLE = 'tab_visible';
    public const TYPE_IDLE_START = 'idle_start';
    public const TYPE_IDLE_END = 'idle_end';

    // Lesson progress
    public const TYPE_LESSON_STARTED = 'lesson_started';
    public const TYPE_LESSON_COMPLETED = 'lesson_completed';
    public const TYPE_LESSON_PAUSED = 'lesson_paused';
    public const TYPE_LESSON_UNPAUSED = 'lesson_unpaused';

    public static function lessonType(string $baseType, int $lessonId): string
    {
        if ($lessonId <= 0) {
            return $baseType;
        }

        return $baseType . '_' . $lessonId;
    }

    public static function suffixType(string $baseType, int|string|null $suffix): string
    {
        if (is_null($suffix)) {
            return $baseType;
        }

        $suffixStr = trim((string) $suffix);
        if ($suffixStr === '' || $suffixStr === '0') {
            return $baseType;
        }

        return $baseType . '_' . $suffixStr;
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function courseAuth(): BelongsTo
    {
        return $this->belongsTo(CourseAuth::class, 'course_auth_id');
    }

    public function courseDate(): BelongsTo
    {
        return $this->belongsTo(CourseDate::class, 'course_date_id');
    }

    public function studentUnit(): BelongsTo
    {
        return $this->belongsTo(StudentUnit::class, 'student_unit_id');
    }

    public function instUnit(): BelongsTo
    {
        return $this->belongsTo(InstUnit::class, 'inst_unit_id');
    }

    /**
     * Scopes
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}

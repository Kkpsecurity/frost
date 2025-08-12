<?php

namespace App\Models;

/**
 * @file Course.php
 * @brief Model for courses table.
 * @details This model represents a course, including attributes like title, price, and associated units.
 * It provides methods for managing courses and retrieving related data.
 */

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Services\RCache;

use App\Models\CourseAuth;
use App\Models\CourseUnit;
use App\Models\Exam;
use App\Models\ExamQuestionSpec;
use App\Models\ZoomCreds;

use App\Casts\JSONCast;
use App\Helpers\TextTk;
use App\Traits\Observable;
use App\Traits\ExpirationTrait;
use App\Traits\RCacheModelTrait;


class Course extends Model
{

    use ExpirationTrait;
    use Observable, RCacheModelTrait;


    protected $table        = 'courses';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',
        'is_active'         => 'boolean',

        'exam_id'           => 'integer',
        'eq_spec_id'        => 'integer',

        'title'             => 'string',  // 64
        'title_long'        => 'string',  // text

        'price'             => 'decimal:2',

        'total_minutes'     => 'integer',

        'policy_expire_days'  => 'integer',

        'dates_template'    => JSONCast::class,
        # TODO: revisit this after RCache update
        #'dates_template'    => 'array',

        'zoom_creds_id'     => 'integer',

        'needs_range'       => 'boolean',

    ];

    protected $guarded      = ['id'];

    protected $attributes   = [
        'is_active'         => true,
        'needs_range'       => false,
    ];

    public function __toString()
    {
        return $this->title;
    }


    public function ShortTitle(): string
    {
        return preg_replace('/\s*\(.*/', '', $this->title);
    }

    public function LongTitle(): string
    {
        return preg_replace('/\s*\(.*/', '', $this->title_long);
    }


    //
    // relationships
    //


    public function CourseAuths()
    {
        return $this->hasMany(CourseAuth::class, 'course_id');
    }

    public function CourseUnits()
    {
        return $this->hasMany(CourseUnit::class, 'course_id');
    }

    public function Exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function ExamQuestionSpec()
    {
        return $this->belongsTo(ExamQuestionSpec::class, 'eq_spec_id');
    }

    public function ZoomCreds()
    {

        //
        // ensure devs use admin Zoom account
        //
        if (! app()->environment('production')) {
            $this->zoom_creds_id = 1;
        }

        return $this->belongsTo(ZoomCreds::class, 'zoom_creds_id');
    }


    //
    // incoming data filters
    //


    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = TextTk::Sanitize($value);
    }

    public function setTitleLongAttribute($value)
    {
        $this->attributes['title_long'] = TextTk::Sanitize($value);
    }


    //
    // cache queries
    //


    public function GetCourseUnits(): Collection
    {
        return RCache::Course_CourseUnits($this->id);
    }

    public function GetExam(): Exam
    {
        return RCache::Exams($this->exam_id);
    }

    public function GetEQSpec(): ExamQuestionSpec
    {
        return RCache::ExamQuestionSpecs($this->eq_spec_id);
    }

    public function GetLessons(): Collection
    {
        return RCache::Course_Lessons($this->id);
    }


    //
    // helpers
    //


    /**
     * Get course type (D or G) based on title
     */
    public function getCourseType(): string
    {
        if (str_contains(strtoupper($this->title), 'D COURSE') || str_contains(strtoupper($this->title), 'D-COURSE')) {
            return 'D';
        }

        if (str_contains(strtoupper($this->title), 'G COURSE') || str_contains(strtoupper($this->title), 'G-COURSE')) {
            return 'G';
        }

        // Default fallback - check if title starts with D or G
        $firstChar = strtoupper(substr(trim($this->title), 0, 1));
        return in_array($firstChar, ['D', 'G']) ? $firstChar : 'D';
    }

    /**
     * Get course duration in days
     */
    public function getDurationDays(): int
    {
        return $this->getCourseType() === 'D' ? 5 : 3;
    }

    /**
     * Get course frequency type
     */
    public function getFrequencyType(): string
    {
        return $this->getCourseType() === 'D' ? 'weekly' : 'biweekly';
    }

    /**
     * Get course type display name
     */
    public function getCourseTypeDisplayName(): string
    {
        return $this->getCourseType() === 'D' ? 'D Course (5-day, Weekly)' : 'G Course (3-day, Biweekly)';
    }

    /**
     * Get maximum participants for course type
     */
    public function getMaxParticipants(): int
    {
        // Default max participants - can be overridden per course
        return $this->getCourseType() === 'D' ? 20 : 15;
    }

    /**
     * Calculate total minutes based on course type
     */
    public function getCalculatedTotalMinutes(): int
    {
        // Assuming 8-hour days (480 minutes per day)
        return $this->getDurationDays() * 480;
    }

    /**
     * Check if course is D type
     */
    public function isDCourse(): bool
    {
        return $this->getCourseType() === 'D';
    }

    /**
     * Check if course is G type
     */
    public function isGCourse(): bool
    {
        return $this->getCourseType() === 'G';
    }

    /**
     * Get course type badge color for UI
     */
    public function getCourseTypeBadgeColor(): string
    {
        return $this->getCourseType() === 'D' ? 'success' : 'info';
    }

    /**
     * Check if course is archived
     */
    public function isArchived(): bool
    {
        return !$this->is_active;
    }

    /**
     * Archive the course
     */
    public function archive(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Restore the course from archive
     */
    public function restore(): bool
    {
        return $this->update(['is_active' => true]);
    }

    public function GetDocs(): array
    {

        $rel_path = '/docs/' . Str::slug($this->ShortTitle());
        $abs_path = public_path($rel_path);

        if (! is_dir($abs_path)) {
            logger(__METHOD__ . " Not found: {$abs_path}");
            return [];
        }


        $files = [];

        foreach (glob("{$abs_path}/*.pdf") as $filename) {
            $basename = basename($filename);
            $files[$basename] = url("{$rel_path}/{$basename}");
        }

        return $files;
    }
}

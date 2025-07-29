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

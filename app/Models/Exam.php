<?php

namespace App\Models;

/**
 * @file Exam.php
 * @brief Model for exams table.
 * @details This model represents exams, including attributes like title, description, and associated courses.
 * It provides methods for managing exam settings and retrieving related data.
 */

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Services\RCache;

use App\Models\Course;

use App\Helpers\TextTk;
use App\Traits\RCacheModelTrait;


class Exam extends Model
{

    #use Observable, RCacheModelTrait;
    use RCacheModelTrait;

    protected $table        = 'exams';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',

        'admin_title'       => 'string',  // 32
        'num_questions'     => 'integer',
        'num_to_pass'       => 'integer',

        'policy_expire_seconds' => 'integer',
        'policy_wait_seconds'   => 'integer',
        'policy_attempts'       => 'integer',

    ];

    protected $guarded      = ['id'];

    protected $attributes   = [

        'policy_expire_seconds' =>  7200, // 2 hours
        'policy_wait_seconds'   => 86400, // 1 day
        'policy_attempts'       => 2,

    ];

    public function __toString()
    {
        return $this->admin_title;
    }


    //
    // relationships
    //


    public function Courses()
    {
        return $this->belongsToMany(Course::class, 'course_id');
    }


    //
    // incoming data filters
    //


    public function setAdminTitleAttribute($value)
    {
        $this->attributes['admin_title'] = TextTk::Sanitize($value);
    }


    //
    // cache queries
    //


    public function GetCourses(): Collection
    {
        return RCache::Courses()
            ->where('exam_id', $this->id)
            ->sortBy('title');
    }


    //
    // misc
    //


    public function ExamTime(): string
    {

        $hours   = floor($this->policy_expire_seconds / 3600);
        $minutes = ($this->policy_expire_seconds % 3600) / 60;

        return $minutes
            ? "$hours hours $minutes minutes"
            : "$hours hours";
    }


    public function Minutes(): string
    {
        return $this->policy_expire_seconds / 60;
    }
}

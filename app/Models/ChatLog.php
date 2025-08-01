<?php

namespace App\Models;

/**
 * @file ChatLog.php
 * @brief Model for chat_logs table.
 * @details This model represents chat logs, including attributes like course date ID, instructor ID,
 * student ID, and the chat body. It provides methods for sanitizing input and retrieving associated
 * users (instructor and student).
 */

use Illuminate\Database\Eloquent\Model;

use App\Services\RCache;

use App\Models\User;
use App\Models\CourseDate;

use App\helpers\TextTk;
use App\Traits\NoString;
use App\Traits\Observable;
use App\Traits\PgTimestamps;
use App\Presenters\PresentsTimeStamps;


class ChatLog extends Model
{

    use PgTimestamps, PresentsTimeStamps;
    use Observable, NoString;


    protected $table        = 'chat_logs';
    protected $primaryKey   = 'id';
    public    $timestamps   = true;

    protected $casts        = [

        'id'                => 'integer',

        'created_at'        => 'timestamp',
        'updated_at'        => 'timestamp',
        'hidden_at'         => 'timestamp',

        'course_date_id'    => 'integer',
        'inst_id'           => 'integer',
        'student_id'        => 'integer',

        'body'              => 'string',

    ];

    protected $guarded      = ['id'];


    //
    // relationships
    //


    public function CourseDate()
    {
        return $this->belongsTo(CourseDate::class, 'course_date_id');
    }

    public function Inst()
    {
        return $this->belongsTo(User::class, 'inst_id');
    }

    public function Student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }


    //
    // incoming data filters
    //


    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = TextTk::Sanitize($value);
    }


    //
    // cache queries
    //


    public function GetInst(): ?User
    {
        return ($this->inst_id ? RCache::User($this->inst_id) : null);
    }

    public function GetStudent(): ?User
    {
        return ($this->student_id ? RCache::User($this->student_id) : null);
    }


    //
    // helpers
    //


    public function Hide(): void
    {
        $this->pgtouch('hidden_at');
    }

    public function UnHide(): void
    {
        $this->update(['hidden_at' => null]);
    }
}

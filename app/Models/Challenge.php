<?php

namespace App\Models;

/**
 * @file Challenge.php
 * @brief Model for challenges table.
 * @details This model represents challenges in the system, including attributes like student lesson ID,
 * completion status, and timestamps for creation, update, expiration, and completion.
 */

use Illuminate\Database\Eloquent\Model;

use App\Services\RCache;

use App\Models\StudentLesson;

use App\Traits\NoString;
use App\Traits\PgTimestamps;
use App\Traits\ExpirationTrait;
use App\Presenters\PresentsTimeStamps;


class Challenge extends Model
{

    use NoString;
    use ExpirationTrait, PgTimestamps, PresentsTimeStamps;


    protected $table        = 'challenges';
    protected $primaryKey   = 'id';
    public    $timestamps   = true;

    protected $casts        = [

        'id'                => 'integer',

        'student_lesson_id' => 'integer',

        'is_final'          => 'boolean',
        'is_eol'            => 'boolean',

        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'expires_at'        => 'datetime',
        'completed_at'      => 'datetime',
        'failed_at'         => 'datetime',

    ];

    protected $guarded      = ['id'];

    protected $attributes   = [

        'is_final'  => false,
        'is_eol'    => false,

    ];


    //
    // relationships
    //


    public function StudentLesson()
    {
        return $this->belongsTo(StudentLesson::class, 'student_lesson_id');
    }


    //
    // helpers
    //


    public function MarkCompleted(): void
    {

        //
        // overwrite failed_at if necessary
        //

        $this->update([
            'completed_at' => $this->freshTimestamp(),
            'failed_at'    => null,
        ]);
    }


    public function MarkFailed(): void
    {

        if ($this->completed_at) {
            logger("ChallengeID {$this->id} already completed_at");
            return;
        }

        if ($this->failed_at) {
            logger("ChallengeID {$this->id} already failed_at");
            return;
        }

        $this->pgtouch('failed_at');
    }
}

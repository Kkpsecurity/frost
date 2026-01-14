<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\NoString;
use App\Traits\PgTimestamps;
use App\Presenters\PresentsTimeStamps;

class InstLessonBreak extends Model
{
    use PgTimestamps, PresentsTimeStamps;
    use NoString;

    protected $table = 'inst_lesson_breaks';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = [
        'id' => 'integer',
        'inst_lesson_id' => 'integer',
        'break_number' => 'integer',
        'started_at' => 'datetime',
        'started_by' => 'integer',
        'ended_at' => 'datetime',
        'ended_by' => 'integer',
        'duration_seconds' => 'integer',
    ];

    protected $guarded = ['id'];

    public function InstLesson()
    {
        return $this->belongsTo(InstLesson::class, 'inst_lesson_id');
    }
}

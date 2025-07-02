<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use RCache;
use App\Models\InstUnit;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Traits\InstLesson\GetCourseUnitLesson;
use App\Models\Traits\InstLesson\InstCanClose;
use App\Presenters\PresentsTimeStamps;
use KKP\Laravel\ModelTraits\NoString;
use KKP\Laravel\ModelTraits\PgTimestamps;


class InstLesson extends Model
{

    use GetCourseUnitLesson, InstCanClose;
    use PgTimestamps, PresentsTimeStamps;
    use NoString;


    protected $table        = 'inst_lesson';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',
        'inst_unit_id'      => 'integer',
        'lesson_id'         => 'integer',

        'created_at'        => 'timestamp',
        'created_by'        => 'integer',
        'completed_at'      => 'timestamp',
        'completed_by'      => 'integer',

        'is_paused'         => 'boolean',

    ];

    protected $guarded      = [ 'id' ];

    protected $attributes = [

        'is_paused' => false,

    ];


    //
    // relationships
    //


    public function InstUnit()
    {
        return $this->belongsTo( InstUnit::class, 'inst_unit_id' );
    }

    public function Lesson()
    {
        return $this->belongsTo( Lesson::class, 'lesson_id' );
    }

    public function CreatedBy()
    {
        return $this->belongsTo( User::class, 'user_id' );
    }

    public function CompletedBy()
    {
        return $this->belongsTo( User::class, 'user_id' );
    }


    //
    // cache queries
    //


    public function GetLesson() : Lesson
    {
        return RCache::Lessons( $this->lesson_id );
    }

    public function GetCreatedBy() : User
    {
        return RCache::Admin( $this->created_by );
    }

    public function GetCompletedBy() : ?User
    {
        return RCache::Admin( $this->completed_by );
    }


    public function GetInstructor() : User
    {
        return $this->completed_by
             ? $this->GetCompletedBy()
             : $this->GetCreatedBy();
    }


}

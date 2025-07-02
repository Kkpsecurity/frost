<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use RCache;
use App\Models\CourseAuth;
use App\Models\Lesson;
use App\Presenters\PresentsTimeStamps;
use KKP\Laravel\ModelTraits\NoString;
use KKP\Laravel\ModelTraits\PgTimestamps;
use KKP\Laravel\ModelTraits\Observable;


class SelfStudyLesson extends Model
{

    use PgTimestamps, PresentsTimeStamps;
    use NoString, Observable;


    protected $table        = 'self_study_lessons';
    protected $primaryKey   = 'id';
    public    $timestamps   = true;

    protected $casts        = [

        'id'                => 'integer',

        'course_auth_id'    => 'integer',
        'lesson_id'         => 'integer',

        'created_at'        => 'timestamp',
        'updated_at'        => 'timestamp',
        'completed_at'      => 'timestamp',

        'seconds_viewed'    => 'integer',

    ];

    protected $guarded      = [ 'id' ];

    protected $attributes   = [

        'seconds_viewed'    => 0,

    ];


    //
    // relationships
    //


    public function CourseAuth()
    {
        return $this->belongsTo( CourseAuth::class, 'course_auth_id' );
    }

    public function Lesson()
    {
        return $this->belongsTo( Lesson::class, 'lesson_id' );
    }


    //
    // cache queries
    //


    public function GetLesson() : Lesson
    {
        return RCache::Lessons( $this->lesson_id );
    }


}

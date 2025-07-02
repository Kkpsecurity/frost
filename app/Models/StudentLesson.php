<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use RCache;
use App\Models\Challenge;
use App\Models\CourseUnitLesson;
use App\Models\InstLesson;
use App\Models\Lesson;
use App\Models\StudentUnit;
use App\Models\User;
use App\Models\Traits\StudentLesson\ClearDNC;
use App\Models\Traits\StudentLesson\SetUnitCompleted;
use App\Presenters\PresentsTimeStamps;
use KKP\Laravel\ModelTraits\NoString;
use KKP\Laravel\ModelTraits\Observable;
use KKP\Laravel\ModelTraits\PgTimestamps;
use KKP\Laravel\PgTk;


class StudentLesson extends Model
{

    use ClearDNC, SetUnitCompleted;
    use PgTimestamps, PresentsTimeStamps;
    use NoString, Observable;


    protected $table        = 'student_lesson';
    protected $primaryKey   = 'id';
    public    $timestamps   = true;

    protected $casts        = [

        'id'                => 'integer',

        'lesson_id'         => 'integer',

        'student_unit_id'   => 'integer',
        'inst_lesson_id'    => 'integer',

        'created_at'        => 'timestamp',
        'updated_at'        => 'timestamp',
        'dnc_at'            => 'timestamp',
        'completed_at'      => 'timestamp',
        'completed_by'      => 'integer',   // IF instructor override

    ];

    protected $guarded      = [ 'id' ];


    //
    // relationships
    //


    public function Lesson()
    {
        return $this->belongsTo( Lesson::class, 'lesson_id' );
    }

    public function StudentUnit()
    {
        return $this->belongsTo( StudentUnit::class, 'student_unit_id' );
    }

    public function InstLesson()
    {
        return $this->belongsTo( InstLesson::class, 'inst_lesson_id' );
    }

    public function CompletedBy()
    {
        return $this->belongsTo( User::class, 'user_id' );
    }

    public function Challenges()
    {
        return $this->hasMany( Challenge::class, 'student_lesson_id' );
    }


    //
    //
    //


    public function LatestChallenge()
    {
        return $this->hasOne( Challenge::class, 'student_lesson_id' )->latest();
    }


    //
    // cache queries
    //


    public function GetLesson() : Lesson
    {
        return RCache::Lessons( $this->lesson_id );
    }

    public function GetCompletedBy() : ?User
    {
        return RCache::Admin( $this->completed_by );
    }


    public function GetCourseUnitLesson() : CourseUnitLesson
    {
        return RCache::CourseUnitLessons()
            	     ->where( 'course_unit_id', $this->StudentUnit->course_unit_id )
            	     ->where( 'lesson_id',      $this->lesson_id )
                     ->first();
    }


    //
    // helpers
    //


    public function MarkDNC() : void
    {
        if ( ! $this->dnc_at && ! $this->completed_by )
        {
            logger( "Marking ({$this->id}) DNC" );
            $this->update([
                'dnc_at'       => $this->freshTimestamp(),
                'completed_at' => null,
            ]);
            $this->refresh();
        }
    }


    public function MarkCompleted() : void
    {
        if ( ! $this->completed_at && ! $this->dnc_at )
        {
            $this->pgtouch( 'completed_at' );
        }
    }


    public function MarkCompletedByInstructor( User $Instructor ) : void
    {
        $this->update([
            'dnc_at'        => null,
            'completed_at'  => $this->freshTimestamp(),
            'completed_by'  => $Instructor->id,
        ]);
    }


}

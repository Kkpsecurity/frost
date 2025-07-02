<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use RCache;
use App\Models\StudentLesson;
use App\Presenters\PresentsTimeStamps;
use App\Traits\ExpirationTrait;
use KKP\Laravel\ModelTraits\NoString;
use KKP\Laravel\ModelTraits\PgTimestamps;


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

        'created_at'        => 'timestamp',
        'updated_at'        => 'timestamp',
        'expires_at'        => 'timestamp',
        'completed_at'      => 'timestamp',
        'failed_at'         => 'timestamp',

    ];

    protected $guarded      = [ 'id' ];

    protected $attributes   = [

        'is_final'  => false,
        'is_eol'    => false,

    ];


    //
    // relationships
    //


    public function StudentLesson()
    {
        return $this->belongsTo( StudentLesson::class, 'student_lesson_id' );
    }


    //
    // helpers
    //


    public function MarkCompleted() : void
    {

        //
        // overwrite failed_at if necessary
        //

        $this->update([
            'completed_at' => $this->freshTimestamp(),
            'failed_at'    => null,
        ]);

    }


    public function MarkFailed() : void
    {

        if ( $this->completed_at )
        {
            logger( "ChallengeID {$this->id} already completed_at" );
            return;
        }

        if ( $this->failed_at )
        {
            logger( "ChallengeID {$this->id} already failed_at" );
            return;
        }

        $this->pgtouch( 'failed_at' );

    }


}

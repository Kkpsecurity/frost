<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use RCache;
use App\Models\ExamQuestionSpec;
use App\Models\Lesson;
use App\Models\User;
use App\Presenters\PresentsTimeStamps;
use KKP\Laravel\ModelTraits\PgTimestamps;
use KKP\TextTk;


class ExamQuestion extends Model
{

    use PgTimestamps, PresentsTimeStamps;


    protected $table        = 'exam_questions';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',

        'lesson_id'         => 'integer',
        'eq_spec_id'        => 'integer',

        'correct'           => 'integer',
        'question'          => 'string',  // text
        'answer_1'          => 'string',  // text
        'answer_2'          => 'string',  // text
        'answer_3'          => 'string',  // text
        'answer_4'          => 'string',  // text
        'answer_5'          => 'string',  // text

        'deact_at'          => 'timestamp',
        'deact_by'          => 'integer',

    ];

    protected $guarded      = [ 'id' ];

    public function __toString() { return $this->question; }


    //
    // relationships
    //


    public function DeactBy()
    {
        return $this->belongsTo( User::class, 'deact_by' );
    }

    public function ExamQuestionSpec()
    {
        return $this->belongsTo( ExamQuestionSpec::class, 'eq_spec_id' );
    }

    public function Lesson()
    {
        return $this->belongsTo( Lesson::class, 'lesson_id' );
    }


    //
    // incoming data filters
    //


    public function setQuestionAttribute( $value )
    {
        $this->attributes[ 'question' ] = TextTk::Sanitize( $value );
    }

    public function setAnswer1Attribute( $value )
    {
        $this->attributes[ 'answer_1' ] = TextTk::Sanitize( $value );
    }

    public function setAnswer2Attribute( $value )
    {
        $this->attributes[ 'answer_2' ] = TextTk::Sanitize( $value );
    }

    public function setAnswer3Attribute( $value )
    {
        $this->attributes[ 'answer_3' ] = TextTk::Sanitize( $value );
    }

    public function setAnswer4Attribute( $value )
    {
        $this->attributes[ 'answer_4' ] = TextTk::Sanitize( $value );
    }

    public function setAnswer5Attribute( $value )
    {
        $this->attributes[ 'answer_5' ] = TextTk::Sanitize( $value );
    }


    //
    // cache queries
    //


    public function GetDeactBy() : ?User
    {
        return RCache::Admin( $this->deact_by );
    }

    public function GetEQSpec() : ExamQuestionSpec
    {
        return RCache::ExamQuestionSpecs( $this->eq_spec_id );
    }

    public function GetLesson() : Lesson
    {
        return RCache::Lessons( $this->lesson_id );
    }


}

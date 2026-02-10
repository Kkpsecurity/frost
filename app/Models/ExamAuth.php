<?php

namespace App\Models;

/**
 * @file ExamAuth.php
 * @brief Model for exam_auths table.
 * @details This model represents exam authorizations, including attributes like course auth ID, UUID,
 * and relationships to courses and exams. It provides methods for managing exam attempts and scores.
 */

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use DB;
use Auth;
use Exception;

use App\Services\RCache;

use App\Models\User;
use App\Models\Exam;
use App\Models\Course;
use App\Models\CourseAuth;
use App\Models\ExamQuestion;

use App\Helpers\PgTk;
use App\Helpers\TextTk;
use App\Casts\JSONCast;
use App\Traits\NoString;
use App\Traits\Observable;
use App\Traits\PgTimestamps;
use App\Presenters\PresentsTimeStamps;


class ExamAuth extends Model
{

    use PgTimestamps, PresentsTimeStamps;
    use Observable, NoString;


    const EXPIRED_SCORE = 'Expired';


    protected $table        = 'exam_auths';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',

        'course_auth_id'    => 'integer',
        'uuid'              => 'string',

        'created_at'        => 'timestamp',
        'expires_at'        => 'timestamp',
        'next_attempt_at'   => 'timestamp',
        'completed_at'      => 'timestamp',
        'hidden_at'         => 'timestamp',
        'hidden_by'         => 'integer',

        'score'             => 'string',   // ie '47 / 50'
        'is_passed'         => 'boolean',

        'question_ids'      => JSONCast::class,  // simple array
        'answers'           => JSONCast::class,  // hash [ $exam_question_id => (int) $answer ]
        'incorrect'         => JSONCast::class,  // hash [ $lesson_id => # missed ]

        #'question_ids'      => 'array',  // simple array
        #'answers'           => 'array',  // hash [ $exam_question_id => (int) $answer ]
        #'incorrect'         => 'array',  // hash [ $lesson_id => # missed ]

    ];

    protected $guarded      = [

        'id',
        'uuid',
        'completed_at',
        'hidden_at',
        'score',
        'is_passed',

    ];


    //
    // relationships
    //


    public function CourseAuth()
    {
        return $this->belongsTo(CourseAuth::class, 'course_auth_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function course()
    {
        return $this->hasOneThrough(Course::class, CourseAuth::class, 'id', 'id', 'course_auth_id', 'course_id');
    }

    public function HiddenBy()
    {
        return $this->belongsTo(User::class, 'hidden_by');
    }


    //
    // incoming data filters
    //


    public function setScoreAttribute($value)
    {

        if (! ($value == self::EXPIRED_SCORE or preg_match('|^\d{1,3} / \d{1,3}$|', $value))) {
            throw new Exception("Invalid format '{$value}' - Required: 'nnn / nnn'");
            return false;
        }

        $this->attributes['score'] = $value;
    }


    //
    // cache queries
    //


    public function GetCourse(): Course
    {
        return RCache::Courses($this->CourseAuth->course_id);
    }

    public function GetExam(): Exam
    {
        return RCache::Exams($this->CourseAuth->GetCourse()->exam_id);
    }

    public function GetHiddenBy(): ?User
    {
        return RCache::Admin($this->hidden_by);
    }


    //
    // helpers
    //


    public function MarkHidden(): void
    {
        $this->forceFill([
            'hidden_at' => PgTk::now(),
            'hidden_by' => Auth::id(),
        ])->update();
    }


    public function MakeExpiresAt(): Carbon
    {

        $Exam = $this->GetExam();

        if (! $Exam->policy_expire_seconds) {
            return null;
        }

        if (! $this->created_at) {
            throw new \Exception('ExamAuth has no created_at');
        }

        return Carbon::parse($this->created_at)
            ->addSeconds($Exam->policy_expire_seconds);
    }


    public function MakeNextAttemptAt(): Carbon
    {

        $Exam = $this->GetExam();

        if (! $Exam->policy_wait_seconds) {
            return null;
        }

        if (! $this->created_at) {
            throw new \Exception('ExamAuth has no created_at');
        }

        return Carbon::parse($this->created_at)
            ->addSeconds($Exam->policy_wait_seconds)
            ->minute(0)->second(0);
    }


    public function IsExpired(): bool
    {

        if (! $this->expires_at) {
            return false;
        }

        return Carbon::now()->gt(Carbon::parse($this->expires_at));
    }


    public function ScorePercent(): ?int
    {

        if (! $this->score) {
            return null;
        }

        list($correct, $total) = explode(' / ', $this->score);

        return floor($correct / $total * 100);
    }


    //
    // ExamQuestions
    //

    //
    // ExamAuthObserver::creating
    //
    public function RandomQuestionIDs(): array
    {
        return PgTk::toSimple(
            DB::select(
                'SELECT * FROM sp_exam_auth_random_ids( :course_id )',
                [':course_id' => $this->GetCourse()->id]
            )
        );
    }


    //
    // retrieve ExamQuestions in $this->question_ids order
    //
    public function ExamQuestions(): Collection
    {
        return PgTk::toModels(
            ExamQuestion::class,
            DB::select(
                'SELECT * FROM sp_exam_auth_questions( :exam_auth_id )',
                [':exam_auth_id' => $this->id]
            )
        )->sortByIDArray($this->question_ids);
    }



    //
    // Exam Result page
    //

    public function IncorrectByLesson(): ?Collection
    {

        if (! $this->incorrect) {
            return null;
        }


        $MissedLessons = collect([]);

        foreach ($this->incorrect as $lesson_id => $count) {
            $MissedLessons->push(
                (object) [
                    'missed' => $count,
                    'title'  => RCache::Lessons($lesson_id)->title
                ]
            );
        }

        return $MissedLessons->sortBy('title')->sortByDesc('missed');
    }


    public function NeedsRangeSelect(): bool
    {

        if (! $this->is_passed) {
            return false;
        }

        return ($this->GetCourse()->needs_range && ! $this->CourseAuth->range_date_id);
    }
}

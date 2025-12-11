<?php
declare(strict_types=1);

namespace App\Classes\ExamAuthObj;

use stdClass;
use Illuminate\Http\Request;

use KKP\Laravel\PgTk;


trait Scoring
{


    public function Score( Request $Request ) : self
    {

        //
        // ExamAuthController handles pre-checks
        //

        //
        // score exam
        //

        $answers   = [];
        $correct   = 0;
        $incorrect = [];

        foreach ( $this->ExamQuestions as $ExamQuestion )
        {

            $answers[ $ExamQuestion->id ] = $Request->input( "answer_{$ExamQuestion->id}" ); // sets null

            if ( (int) $Request->input( "answer_{$ExamQuestion->id}" ) === (int) $ExamQuestion->correct )
            {
                $correct++;
            }
            else
            {
                if ( ! isset( $incorrect[ $ExamQuestion->lesson_id ] ) )
                {
                    $incorrect[ $ExamQuestion->lesson_id ] = 1;
                }
                else
                {
                    $incorrect[ $ExamQuestion->lesson_id ]++;
                }
            }

        }

        $is_passed = ( $correct >= $this->Exam->num_to_pass );


        //
        // save results
        //

        $this->ExamAuth->forceFill([

            'completed_at'  => PgTk::now(),
            'score'         => $correct . ' / ' . $this->Exam->num_questions,
            'is_passed'     => $is_passed,
            'answers'       => $answers,
            'incorrect'     => $incorrect,

        ])->update();

        $this->ExamAuth->refresh();


        //
        // handle result
        //

        if ( $is_passed )
        {
            return $this->_handlePassed();
        }
        else
        {
            return $this->_handleFailed();
        }

    }


    /*
    public function AnswerMarks() : stdClass
    {

        $answer_no_answer = '<i class="fa fa-map-marker exam_mark_correct"></i>';
        $answer_correct   = '<i class="fa fa-check exam_mark_correct"></i>';
        $answer_incorrect = '<i class="fa fa-remove exam_mark_incorrect"></i>';

        $AnswerMarks = new stdClass;

        foreach ( $this->ExamQuestions as $ExamQuestion )
        {

            $student_answer = ( $this->ExamAuth->answers[ $ExamQuestion->id ] ?? null ); // don't change this

            // always mark correct answer
            $AnswerMarks->{ "{$ExamQuestion->id}_{$ExamQuestion->correct}" } = $answer_correct;

            if ( ! $student_answer )
            {

                // no answer: overwrite correct mark
                $AnswerMarks->{ "{$ExamQuestion->id}_{$ExamQuestion->correct}" } = $answer_no_answer;

            }
            else if ( $student_answer != $ExamQuestion->correct )
            {

                // mark incorrect answer
                $AnswerMarks->{ "{$ExamQuestion->id}_{$student_answer}" } = $answer_incorrect;

            }

        }

        return $AnswerMarks;

    }
    */


}

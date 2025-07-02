<?php

namespace App\Observers;

use Exception;
use Illuminate\Support\Carbon;

use App\Models\ExamAuth;


class ExamAuthObserver
{


    public function creating( ExamAuth $ExamAuth )
    {

        if ( ! $ExamAuth->created_at )
        {
            $ExamAuth->created_at = Carbon::now();
        }

        $ExamAuth->expires_at      = $ExamAuth->MakeExpiresAt();
        $ExamAuth->next_attempt_at = $ExamAuth->MakeNextAttemptAt();

        if ( ! $ExamAuth->question_ids )
        {
            kkpdebug( 'Observer', 'Adding question_ids to ExamAuth' );
            $ExamAuth->question_ids = $ExamAuth->RandomQuestionIDs();
        }

    }


}

<?php

namespace App\Observers;

use Exception;
use App\Models\Lesson;


class LessonObserver
{

    public function deleting( Lesson $Lesson ) : bool
    {

		kkpdebug( 'Observer', __METHOD__ );

        if ( $Lesson->CourseUnitLessons )
        {
            throw new Exception( "Lesson '{$Lesson}' has CourseUnitLessons and cannot be deleted" );
            return false;
        }

        if ( $Lesson->ExamQuestions )
        {
            throw new Exception( "Lesson '{$Lesson}' has ExamQuestions and cannot be deleted" );
            return false;
        }

        return true;

    }

}

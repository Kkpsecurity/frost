<?php

namespace App\Observers;

use App\Models\SelfStudyLesson;


class SelfStudyLessonObserver
{

    public function saved( SelfStudyLesson $SelfStudyLesson )
    {

        kkpdebug( 'Observer', __METHOD__ );

        // $SelfStudyLesson->CourseAuth->PCLCache( true );

    }

}

<?php

namespace App\Observers;

use App\Models\StudentLesson;


class StudentLessonObserver
{

    public function saved( StudentLesson $StudentLesson )
    {

        kkpdebug( 'Observer', __METHOD__ );

        $StudentLesson->StudentUnit->CourseAuth->PCLCache( true );

        $StudentLesson->SetUnitCompleted();

    }

}

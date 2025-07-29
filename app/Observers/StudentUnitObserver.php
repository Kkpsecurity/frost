<?php

namespace App\Observers;

use App\Models\StudentUnit;


class StudentUnitObserver
{

    public function saved(StudentUnit $StudentUnit)
    {

        kkpdebug('Observer', __METHOD__);

        $StudentUnit->CourseAuth->PCLCache(true);
    }
}

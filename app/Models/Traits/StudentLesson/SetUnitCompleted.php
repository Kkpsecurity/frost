<?php

declare(strict_types=1);

namespace App\Models\Traits\StudentLesson;


trait SetUnitCompleted
{


    public function SetUnitCompleted(): void
    {

        if (! $this->completed_at or $this->StudentUnit->unit_completed) {
            return;
        }


        #$CompletedLessonIDs = array_keys( $StudentLesson->StudentUnit->CourseAuth->PCLCache() );
        $CompletedLessonIDs = array_keys($this->StudentUnit->CourseAuth->PCLCache());

        /*
        $UnitLessonIDs = $StudentLesson->StudentUnit
                                       ->GetCourseUnit()
                                       ->GetLessons()
                                       ->pluck( 'id' )
                                       ->toArray();
        */

        $UnitLessonIDs = $this->StudentUnit
            ->GetCourseUnit()
            ->GetLessons()
            ->pluck('id')
            ->toArray();


        foreach ($UnitLessonIDs as $lesson_id) {
            if (! in_array($lesson_id, $CompletedLessonIDs)) {
                return;
            }
        }


        $this->StudentUnit->update(['unit_completed' => true]);
    }
}

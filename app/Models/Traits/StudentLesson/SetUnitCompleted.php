<?php

declare(strict_types=1);

namespace App\Models\Traits\StudentLesson;

use App\Events\Progress\AllLessonsCompleted;
use App\Events\Progress\LessonMilestoneReached;

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


        // Fire lesson milestone events (25 / 50 / 75 %) before the all-done check.
        $completedCount = count($CompletedLessonIDs);
        $totalCount     = count($UnitLessonIDs);

        if ($totalCount > 0) {
            $pct = (int) floor(($completedCount / $totalCount) * 100);

            foreach ([25, 50, 75] as $milestone) {
                if ($pct >= $milestone) {
                    event(new LessonMilestoneReached(
                        $this->StudentUnit,
                        $milestone,
                        $completedCount,
                        $totalCount,
                    ));
                }
            }
        }


        foreach ($UnitLessonIDs as $lesson_id) {
            if (! in_array($lesson_id, $CompletedLessonIDs)) {
                return;
            }
        }


        $this->StudentUnit->update(['unit_completed' => true]);

        // All lessons in this unit are now complete — notify the student.
        event(new AllLessonsCompleted($this->StudentUnit));
    }
}

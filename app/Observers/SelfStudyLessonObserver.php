<?php

namespace App\Observers;

use App\Models\SelfStudyLesson;


class SelfStudyLessonObserver
{

    public function saved(SelfStudyLesson $SelfStudyLesson)
    {

        kkpdebug('Observer', __METHOD__);

        // Refresh the completed-lessons cache whenever a self-study lesson
        // is saved, so the rest of the system (progress bars, exam gates, etc.)
        // sees the updated state immediately.
        try {
            $SelfStudyLesson->CourseAuth?->PCLCache(true);
        } catch (\Throwable $e) {
            // Non-fatal — don't let cache failures break lesson saves.
        }
    }
}

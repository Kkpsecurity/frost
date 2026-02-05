<?php

namespace App\Observers;

use App\Models\StudentLesson;
use App\Models\StudentActivity;


class StudentLessonObserver
{

    public function created(StudentLesson $StudentLesson)
    {
        // Track lesson start (StudentLesson row created when student enters/starts the lesson)
        try {
            $studentUnit = $StudentLesson->StudentUnit;
            $courseAuth = $studentUnit?->CourseAuth;

            $userId = (int) ($courseAuth?->user_id ?? 0);
            if ($userId > 0) {
                StudentActivity::create([
                    'user_id' => $userId,
                    'course_auth_id' => (int) ($studentUnit?->course_auth_id ?? 0),
                    'course_date_id' => (int) ($studentUnit?->course_date_id ?? 0),
                    'student_unit_id' => (int) ($studentUnit?->id ?? 0),
                    'inst_unit_id' => (int) ($studentUnit?->inst_unit_id ?? 0),
                    'category' => StudentActivity::CATEGORY_INTERACTION,
                    'activity_type' => StudentActivity::lessonType(
                        StudentActivity::TYPE_LESSON_STARTED,
                        (int) $StudentLesson->lesson_id
                    ),
                    'description' => 'Lesson started',
                    'started_at' => $StudentLesson->created_at ?? now(),
                    'data' => [
                        'base_activity_type' => StudentActivity::TYPE_LESSON_STARTED,
                        'student_lesson_id' => (int) $StudentLesson->id,
                        'lesson_id' => (int) $StudentLesson->lesson_id,
                        'inst_lesson_id' => (int) ($StudentLesson->inst_lesson_id ?? 0),
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            // Never break lesson start if tracking fails.
        }
    }

    public function saved(StudentLesson $StudentLesson)
    {

        kkpdebug('Observer', __METHOD__);

        $StudentLesson->StudentUnit->CourseAuth->PCLCache(true);

        $StudentLesson->SetUnitCompleted();

        // Track lesson completion activity (only on first transition to completed).
        try {
            if (
                $StudentLesson->wasChanged('completed_at')
                && $StudentLesson->completed_at
                && empty($StudentLesson->getOriginal('completed_at'))
            ) {
                $studentUnit = $StudentLesson->StudentUnit;
                $courseAuth = $studentUnit?->CourseAuth;

                $userId = (int) ($courseAuth?->user_id ?? 0);
                if ($userId > 0) {
                    StudentActivity::create([
                        'user_id' => $userId,
                        'course_auth_id' => (int) ($studentUnit?->course_auth_id ?? 0),
                        'course_date_id' => (int) ($studentUnit?->course_date_id ?? 0),
                        'student_unit_id' => (int) ($studentUnit?->id ?? 0),
                        'inst_unit_id' => (int) ($studentUnit?->inst_unit_id ?? 0),
                        'category' => StudentActivity::CATEGORY_INTERACTION,
                        'activity_type' => StudentActivity::lessonType(
                            StudentActivity::TYPE_LESSON_COMPLETED,
                            (int) $StudentLesson->lesson_id
                        ),
                        'description' => 'Lesson completed',
                        'data' => [
                            'base_activity_type' => StudentActivity::TYPE_LESSON_COMPLETED,
                            'student_lesson_id' => (int) $StudentLesson->id,
                            'lesson_id' => (int) $StudentLesson->lesson_id,
                            'inst_lesson_id' => (int) ($StudentLesson->inst_lesson_id ?? 0),
                            'completed_at' => $StudentLesson->completed_at?->toIso8601String(),
                            'completed_by' => $StudentLesson->completed_by,
                            'dnc_at' => $StudentLesson->dnc_at?->toIso8601String(),
                        ],
                    ]);
                }
            }
        } catch (
            \Throwable $e
        ) {
            // Never break lesson completion if tracking fails.
        }
    }
}

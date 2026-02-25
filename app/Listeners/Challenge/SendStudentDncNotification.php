<?php

namespace App\Listeners\Challenge;

use App\Events\Challenge\StudentDnc;
use App\Notifications\Instructor\StudentDncNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Notifies the class instructor when one of their students is marked DNC.
 *
 * Relationship chain:
 *   StudentLesson → StudentUnit → InstUnit → GetCreatedBy() → instructor User
 *   StudentLesson → StudentUnit → CourseAuth → GetUser()     → student User
 */
class SendStudentDncNotification implements ShouldQueue
{
    /**
     * Handle the StudentDnc event.
     */
    public function handle(StudentDnc $event): void
    {
        $studentLesson = $event->studentLesson;

        // -------------------------------------------------------------------
        // Resolve relationships — bail safely at each nullable step.
        // -------------------------------------------------------------------
        $studentUnit = $studentLesson->StudentUnit;

        if (! $studentUnit) {
            Log::warning('StudentDnc: no StudentUnit for student_lesson_id=' . $studentLesson->id);
            return;
        }

        $instUnit = $studentUnit->InstUnit;

        if (! $instUnit) {
            Log::warning('StudentDnc: no InstUnit for student_unit_id=' . $studentUnit->id);
            return;
        }

        /** @var \App\Models\User|null $instructor */
        $instructor = $instUnit->GetCreatedBy();

        if (! $instructor) {
            Log::warning('StudentDnc: no instructor User for inst_unit_id=' . $instUnit->id);
            return;
        }

        // -------------------------------------------------------------------
        // Resolve student + lesson/course names.
        // -------------------------------------------------------------------
        $courseAuth = $studentUnit->CourseAuth;
        $student    = $courseAuth?->GetUser();

        if (! $student) {
            Log::warning('StudentDnc: no student User for student_unit_id=' . $studentUnit->id);
            return;
        }

        $studentName = trim(($student->fname ?? '') . ' ' . ($student->lname ?? '')) ?: ('Student #' . $student->id);
        $lessonName  = $studentLesson->Lesson?->title ?: ('Lesson #' . $studentLesson->lesson_id);
        $courseName  = $studentUnit->GetCourse()?->title ?? 'Unknown Course';

        // -------------------------------------------------------------------
        // Dispatch notification to the instructor.
        // -------------------------------------------------------------------
        try {
            $instructor->notify(
                new StudentDncNotification(
                    $studentLesson,
                    $student,
                    $studentName,
                    $lessonName,
                    $courseName,
                )
            );
        } catch (\Throwable $e) {
            Log::error('StudentDnc notification failed', [
                'instructor_id'     => $instructor->id,
                'student_lesson_id' => $studentLesson->id,
                'error'             => $e->getMessage(),
            ]);
        }
    }
}

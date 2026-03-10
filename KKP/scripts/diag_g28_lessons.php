<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Course;
use App\Services\RCache;
use App\Models\CourseAuth;

echo "=== G28 COURSE LESSON STRUCTURE DIAGNOSTIC ===\n\n";

// Find G28-ish courses
$courses = Course::where('title', 'like', '%G28%')
    ->orWhere('title_long', 'like', '%G28%')
    ->orWhere('title', 'like', '%G %')
    ->orWhere('title', 'like', '% G%')
    ->get(['id', 'title', 'title_long']);

if ($courses->isEmpty()) {
    echo "No G28/G courses found by title. Listing ALL courses:\n";
    $courses = Course::get(['id', 'title', 'title_long']);
}

foreach ($courses as $course) {
    $units   = RCache::Course_CourseUnits($course->id);
    $lessons = RCache::Course_Lessons($course->id);

    echo "Course ID={$course->id}  title=\"{$course->title}\"  title_long=\"{$course->title_long}\"\n";
    echo "  Course Units: {$units->count()}   Unique Lessons: {$lessons->count()}\n";

    foreach ($units as $unit) {
        $unitLessons = $unit->GetLessons();
        $ids         = $unitLessons->pluck('id')->implode(',');
        echo "    Unit ID={$unit->id}  \"{$unit->title}\"  lessons={$unitLessons->count()}  lesson_ids=[{$ids}]\n";
    }

    echo "\n";
}

// Also show active CourseAuths for G-type courses
echo "=== CourseAuths for G courses ===\n";
$gCourseIds = $courses->pluck('id')->toArray();
$auths = CourseAuth::whereIn('course_id', $gCourseIds)
    ->with('Course')
    ->get(['id', 'course_id', 'user_id', 'exam_admin_id', 'completed_at']);
foreach ($auths as $auth) {
    $course     = $auth->GetCourse();
    $lessonCnt  = $course ? $course->GetLessons()->count() : 0;
    $allDone    = $auth->AllLessonsCompleted();
    $examReady  = $auth->ClassroomExam()->is_ready;
    $adminNote  = $auth->exam_admin_id ? " EXAM_ADMIN={$auth->exam_admin_id}" : "";
    echo "  CourseAuth ID={$auth->id}  course_id={$auth->course_id}  user_id={$auth->user_id}"
        . "  lessons={$lessonCnt}  all_done=" . ($allDone ? 'YES' : 'no')
        . "  is_ready=" . ($examReady ? 'YES' : 'no')
        . $adminNote . "\n";

    // Show completed lesson count via CompletedLessons()
    try {
        $completed = count($auth->CompletedLessons());
        echo "    CompletedLessons()={$completed}\n";
    } catch (\Throwable $e) {
        echo "    CompletedLessons() ERROR: {$e->getMessage()}\n";
    }
}

<?php

require __DIR__ . '/bootstrap/app.php';

// Get the student and course data
$studentId = 1; // Replace with actual student ID
$courseAuthId = 128; // The course ID from screenshot

$student = \App\Models\User::find($studentId);

if (!$student) {
    die("Student not found\n");
}

echo "=== TESTING API RESPONSE ===\n\n";

// Simulate the API call
$courseAuth = \App\Models\CourseAuth::with(['Course', 'instructorUnit.instructor'])
    ->where('id', $courseAuthId)
    ->where('user_id', $studentId)
    ->first();

if (!$courseAuth) {
    die("CourseAuth not found\n");
}

// Get today's InstLessons
$todaysInstLessons = \App\Models\InstLesson::with(['lesson'])
    ->whereHas('instructorUnit', function ($query) use ($courseAuth) {
        $query->where('course_id', $courseAuth->course_id);
    })
    ->whereDate('created_at', today())
    ->get()
    ->keyBy('lesson_id');

echo "Today's InstLessons: " . $todaysInstLessons->count() . "\n";

// Find active lesson (no completed_at)
$activeLessonId = null;
foreach ($todaysInstLessons as $lessonId => $instLesson) {
    echo "  - InstLesson ID: {$instLesson->id}, Lesson ID: {$lessonId}, ";
    echo "Completed: " . ($instLesson->completed_at ? "YES" : "NO") . "\n";

    if (!$instLesson->completed_at) {
        $activeLessonId = $lessonId;
        echo "    ✅ ACTIVE LESSON FOUND: {$activeLessonId}\n";
        break;
    }
}

if (!$activeLessonId) {
    echo "  ✅ NO ACTIVE LESSON (all completed or none started)\n";
}

echo "\nActive Lesson ID that would be returned: " . ($activeLessonId ?? 'NULL') . "\n";

// Get the lessons from StudentUnit
$studentUnit = \App\Models\StudentUnit::where('course_auth_id', $courseAuthId)->first();

if ($studentUnit) {
    $lessons = \App\Models\Lesson::whereIn('id', $studentUnit->lesson_ids ?? [])
        ->orderBy('sequence')
        ->get();

    echo "\nLessons in StudentUnit:\n";
    foreach ($lessons as $lesson) {
        echo "  - Lesson {$lesson->id}: {$lesson->title}\n";

        // Check if this lesson has an InstLesson
        $instLesson = $todaysInstLessons->get($lesson->id);
        $isActive = $instLesson && !$instLesson->completed_at;

        // Check StudentLesson
        $studentLesson = \App\Models\StudentLesson::where('student_id', $studentId)
            ->where('lesson_id', $lesson->id)
            ->first();

        $isCompleted = $studentLesson && $studentLesson->completed_at;

        $status = 'incomplete';
        if ($isCompleted) {
            $status = 'completed';
        } elseif ($isActive) {
            $status = 'active_live';
        }

        echo "    Status: {$status}, isActive: " . ($isActive ? 'TRUE' : 'FALSE') . ", ";
        echo "isCompleted: " . ($isCompleted ? 'TRUE' : 'FALSE') . "\n";
    }
}

echo "\n=== API WOULD RETURN ===\n";
echo "active_lesson_id: " . ($activeLessonId ?? 'null') . "\n";
echo "lessons[0]: Lesson 6 (Patrolling), status=completed, is_active=false\n";
echo "lessons[1]: Lesson 7 (Interviewing Techniques), status=incomplete, is_active=false\n";

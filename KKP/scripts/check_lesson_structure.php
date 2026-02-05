<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\InstLesson;
use App\Models\StudentUnit;
use App\Models\StudentLesson;

$courseDateId = 10757;

echo "=== CHECKING COURSEDATE {$courseDateId} STRUCTURE ===\n\n";

$courseDate = CourseDate::with(['InstUnits.instLessons', 'StudentUnits'])->find($courseDateId);

if (!$courseDate) {
    echo "❌ CourseDate not found\n";
    exit;
}

echo "CourseDate: {$courseDate->id} | {$courseDate->starts_at->format('Y-m-d H:i')}\n";
echo "InstUnits: {$courseDate->InstUnits->count()}\n";
echo "StudentUnits: {$courseDate->StudentUnits->count()}\n\n";

// Check InstUnits and InstLessons
echo str_repeat('=', 80) . "\n";
echo "INSTRUCTOR UNITS & LESSONS:\n";
echo str_repeat('=', 80) . "\n\n";

foreach ($courseDate->InstUnits as $instUnit) {
    echo "InstUnit ID: {$instUnit->id}\n";
    echo "  Created: {$instUnit->created_at}\n";
    echo "  Completed: " . ($instUnit->completed_at ?? 'NULL') . "\n";
    echo "  Lessons: {$instUnit->instLessons->count()}\n\n";

    if ($instUnit->instLessons->isNotEmpty()) {
        echo "  INST LESSONS:\n";
        foreach ($instUnit->instLessons as $instLesson) {
            $status = $instLesson->completed_at ? 'COMPLETED' : ($instLesson->started_at ? 'ACTIVE' : 'PENDING');
            echo sprintf("    Lesson %d | %s | Started: %s | Completed: %s\n",
                $instLesson->lesson_id,
                $status,
                $instLesson->started_at ?? 'NULL',
                $instLesson->completed_at ?? 'NULL'
            );
        }
    }
    echo "\n";
}

// Check StudentUnits and StudentLessons
echo str_repeat('=', 80) . "\n";
echo "STUDENT UNITS & LESSONS:\n";
echo str_repeat('=', 80) . "\n\n";

foreach ($courseDate->StudentUnits as $studentUnit) {
    echo "StudentUnit ID: {$studentUnit->id} | Auth: {$studentUnit->course_auth_id}\n";

    $studentLessons = StudentLesson::where('student_unit_id', $studentUnit->id)->get();
    echo "  StudentLessons: {$studentLessons->count()}\n";

    if ($studentLessons->isNotEmpty()) {
        foreach ($studentLessons as $sl) {
            $status = $sl->completed_at ? 'COMPLETED' : 'PENDING';
            echo "    Lesson {$sl->lesson_id} | {$status}\n";
        }
    } else {
        echo "  ⚠️  NO STUDENT LESSONS CREATED\n";
    }
    echo "\n";
}

echo str_repeat('=', 80) . "\n";
echo "DIAGNOSIS:\n";
echo "- InstLessons exist: " . ($courseDate->InstUnits->first()->instLessons->count() > 0 ? 'YES' : 'NO') . "\n";
echo "- StudentLessons exist: " . (StudentLesson::whereIn('student_unit_id', $courseDate->StudentUnits->pluck('id'))->count() > 0 ? 'YES' : 'NO') . "\n";
echo "\n";
echo "⚠️  ISSUE: StudentLesson records need to be created when:\n";
echo "  1. StudentUnit is created (enrollment), OR\n";
echo "  2. InstLessons are created/started\n";

<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InstLesson;
use App\Models\StudentLesson;

$courseDateId = 10757;

echo "=== CLEANING UP - START FRESH ===\n\n";

// Delete all InstLessons for this InstUnit
$instUnit = \App\Models\CourseDate::find($courseDateId)?->InstUnit;

if ($instUnit) {
    $deletedInstLessons = InstLesson::where('inst_unit_id', $instUnit->id)->delete();
    echo "🗑️  Deleted {$deletedInstLessons} InstLesson records\n";
}

// Delete all StudentLessons for this CourseDate
$studentUnits = \App\Models\StudentUnit::where('course_date_id', $courseDateId)->pluck('id');
$deletedStudentLessons = StudentLesson::whereIn('student_unit_id', $studentUnits)->delete();
echo "🗑️  Deleted {$deletedStudentLessons} StudentLesson records\n";

echo "\n✅ READY TO START FRESH!\n\n";

echo "📋 CORRECT FLOW:\n";
echo "1. Instructor clicks 'Start Lesson' on lesson 1\n";
echo "   → Backend creates InstLesson\n";
echo "   → Backend creates StudentLesson for all enrolled students\n";
echo "   → StudentLesson.completed_at = NULL\n\n";

echo "2. Student classroom polls every 5 seconds\n";
echo "   → Detects StudentLesson exists for lesson 1\n";
echo "   → Shows lesson 1 as BLUE (active/in-progress)\n\n";

echo "3. Instructor clicks 'Complete' on lesson 1\n";
echo "   → Backend sets InstLesson.completed_at\n";
echo "   → Backend sets StudentLesson.completed_at for all students\n\n";

echo "4. Student classroom polls\n";
echo "   → Detects StudentLesson.completed_at is set\n";
echo "   → Shows lesson 1 as GREEN (completed)\n\n";

echo "5. Badge counter updates: 1 / 5 lessons\n\n";

echo "🚀 Now start lesson 1 from instructor dashboard!\n";

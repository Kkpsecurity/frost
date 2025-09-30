<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Course Card Data Analysis\n";
echo "=============================\n\n";

use App\Models\CourseDate;
use App\Models\StudentUnit;

$today = now()->format('Y-m-d');

// Get today's CourseDate
$courseDate = CourseDate::whereDate('starts_at', $today)
    ->where('is_active', true)
    ->with(['courseUnit.course', 'courseUnit.lessons', 'instUnit.createdBy', 'instUnit.assistant'])
    ->first();

if ($courseDate) {
    echo "📊 **CourseDate Analysis for ID {$courseDate->id}**:\n";
    echo "---------------------------------------------------\n";

    // 1. Start Time Analysis
    echo "🕐 **Start Time**:\n";
    echo "   • CourseDate starts_at: {$courseDate->starts_at}\n";
    echo "   • Formatted: " . \Carbon\Carbon::parse($courseDate->starts_at)->format('g:i A') . "\n";
    echo "   • CourseUnit details: " . ($courseDate->courseUnit ? 'Present' : 'Missing') . "\n\n";

    // 2. Lesson Count Analysis
    echo "📚 **Lesson Count Analysis**:\n";
    if ($courseDate->courseUnit) {
        $courseUnit = $courseDate->courseUnit;
        echo "   • CourseUnit ID: {$courseUnit->id}\n";
        echo "   • CourseUnit title: {$courseUnit->title}\n";
        echo "   • Course ID: {$courseUnit->course_id}\n";

        // Check lessons for this course unit
        $lessons = $courseUnit->lessons ?? collect();
        echo "   • Lessons in CourseUnit: " . $lessons->count() . "\n";

        // Check total course units for the course
        $totalCourseUnits = \App\Models\CourseUnit::where('course_id', $courseUnit->course_id)->count();
        echo "   • Total CourseUnits in Course: {$totalCourseUnits}\n";

        // What should lesson_count actually be?
        echo "   • **Should show**: {$totalCourseUnits} (total course units) or " . $lessons->count() . " (lessons in unit)\n";
    } else {
        echo "   • ❌ No CourseUnit found!\n";
    }
    echo "\n";

    // 3. Student Count Analysis
    echo "👥 **Student Count Analysis**:\n";
    $studentUnitsToday = StudentUnit::whereHas('instUnit', function($query) use ($courseDate) {
        $query->where('course_date_id', $courseDate->id);
    })->count();

    $studentUnitsForCourseDate = StudentUnit::where('course_date_id', $courseDate->id)->count();

    echo "   • StudentUnits for today's CourseDate: {$studentUnitsForCourseDate}\n";
    echo "   • StudentUnits via InstUnit: {$studentUnitsToday}\n";
    echo "   • **Should show**: 0 (since class hasn't started)\n\n";

    // 4. Instructor & Assistant Analysis
    echo "👨‍🏫 **Instructor & Assistant Analysis**:\n";
    if ($courseDate->instUnit) {
        $instUnit = $courseDate->instUnit;
        echo "   • InstUnit ID: {$instUnit->id}\n";
        echo "   • Created by: {$instUnit->created_by}\n";
        echo "   • Assistant ID: {$instUnit->assistant_id}\n";

        if ($instUnit->createdBy) {
            echo "   • Instructor: {$instUnit->createdBy->fname} {$instUnit->createdBy->lname}\n";
        }

        if ($instUnit->assistant) {
            echo "   • Assistant: {$instUnit->assistant->fname} {$instUnit->assistant->lname}\n";
        } else {
            echo "   • Assistant: None assigned\n";
        }
    } else {
        echo "   • ❌ No InstUnit found - class not started\n";
        echo "   • **Should show**: Instructor: Unassigned, Assistant: None\n";
    }

    echo "\n📝 **Corrections Needed**:\n";
    echo "==========================\n";
    echo "1. ✅ Start Time: Use CourseDate starts_at time\n";
    echo "2. ❌ Lesson Count: Should be total CourseUnits in course, not StudentUnit count\n";
    echo "3. ❌ Student Count: Should be 0 for unstarted class, not CourseAuth count\n";
    echo "4. ❌ Assistant: Need to add assistant display in React component\n";

} else {
    echo "❌ No active CourseDate found for today\n";
}

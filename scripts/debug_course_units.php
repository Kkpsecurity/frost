<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Debugging CourseUnits Issue\n";
echo "===============================\n\n";

use App\Models\Course;
use App\Models\CourseUnit;

try {
    $courses = Course::where('is_active', true)->with('CourseUnits')->get();

    foreach ($courses as $course) {
        echo "📚 Course {$course->id}: {$course->title}\n";
        echo "   CourseUnits count: " . $course->CourseUnits->count() . "\n";

        if ($course->CourseUnits->count() > 0) {
            echo "   CourseUnits:\n";
            foreach ($course->CourseUnits as $index => $unit) {
                echo "     [{$index}] ID: {$unit->id}, Title: {$unit->title}, Admin Title: {$unit->admin_title}\n";
            }
        } else {
            echo "   ⚠️  NO CourseUnits found!\n";
        }
        echo "\n";
    }

    // Also check if CourseUnits exist in the database
    echo "📊 Total CourseUnits in database: " . CourseUnit::count() . "\n";
    echo "📊 CourseUnits by course:\n";

    $unitsByCourse = CourseUnit::selectRaw('course_id, COUNT(*) as count')
        ->groupBy('course_id')
        ->get();

    foreach ($unitsByCourse as $stat) {
        $course = Course::find($stat->course_id);
        echo "   Course {$stat->course_id} ({$course->title}): {$stat->count} units\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
    exit(1);
}

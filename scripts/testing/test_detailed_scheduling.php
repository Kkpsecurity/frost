<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\Frost\Scheduling\CourseDateGeneratorService;
use Carbon\Carbon;

echo "🧪 Testing CourseDate Generation with Detailed Date Analysis\n";
echo "==========================================================\n\n";

$service = new CourseDateGeneratorService();

// Use reflection to access the private method
$reflection = new ReflectionClass($service);
$shouldCourseRunMethod = $reflection->getMethod('shouldCourseRunOnDate');
$shouldCourseRunMethod->setAccessible(true);

// Get courses
use App\Models\Course;
$courses = Course::where('is_active', true)->whereHas('CourseUnits')->get();

echo "📅 Date-by-Date Analysis for Sept 24 - Oct 3:\n";
echo "===============================================\n\n";

$startDate = Carbon::parse('2025-09-24');
$endDate = Carbon::parse('2025-10-03');
$currentDate = $startDate->copy();

while ($currentDate <= $endDate) {
    echo "📆 {$currentDate->format('Y-m-d (l)')}\n";

    foreach ($courses as $course) {
        $shouldRun = $shouldCourseRunMethod->invoke($service, $course, $currentDate);
        $status = $shouldRun ? '✅ YES' : '❌ NO';
        echo "  {$course->title}: {$status}\n";
    }
    echo "\n";

    $currentDate->addDay();
}

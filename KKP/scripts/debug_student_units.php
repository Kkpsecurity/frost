<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDate;

$courseDateId = 10757;

echo "=== CHECKING COURSEDATE RELATIONSHIPS ===\n\n";

// Load with StudentUnits
$courseDate = CourseDate::with(['StudentUnits'])->find($courseDateId);

if (!$courseDate) {
    echo "❌ CourseDate not found\n";
    exit;
}

echo "CourseDate ID: {$courseDate->id}\n";
echo "StudentUnits loaded: " . ($courseDate->relationLoaded('StudentUnits') ? 'YES' : 'NO') . "\n";
echo "StudentUnits count: " . $courseDate->StudentUnits->count() . "\n\n";

if ($courseDate->StudentUnits->isEmpty()) {
    echo "⚠️  StudentUnits collection is EMPTY\n";
    echo "This is why StudentLesson records were not created!\n\n";

    // Try to load manually
    echo "Trying direct query...\n";
    $studentUnits = \App\Models\StudentUnit::where('course_date_id', $courseDateId)->get();
    echo "Direct query found: {$studentUnits->count()} student units\n";

    foreach ($studentUnits as $su) {
        echo "  - StudentUnit ID: {$su->id}, Auth ID: {$su->course_auth_id}\n";
    }
} else {
    echo "✅ StudentUnits found:\n";
    foreach ($courseDate->StudentUnits as $su) {
        echo "  - StudentUnit ID: {$su->id}, Auth ID: {$su->course_auth_id}\n";
    }
}

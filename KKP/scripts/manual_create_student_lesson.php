<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDate;
use App\Models\StudentLesson;

$courseDateId = 10757;
$lessonId = 6; // Patrolling

echo "=== MANUALLY CREATING STUDENT LESSON RECORDS ===\n\n";

$courseDate = CourseDate::with(['StudentUnits'])->find($courseDateId);

if (!$courseDate) {
    echo "❌ CourseDate not found\n";
    exit;
}

$studentUnits = $courseDate->StudentUnits;
echo "Found {$studentUnits->count()} student(s)\n\n";

foreach ($studentUnits as $studentUnit) {
    echo "Processing StudentUnit ID: {$studentUnit->id}\n";

    // Create or find StudentLesson
    $studentLesson = StudentLesson::firstOrCreate(
        [
            'student_unit_id' => $studentUnit->id,
            'lesson_id' => $lessonId,
        ],
        [
            'completed_at' => null,
        ]
    );

    // Mark as completed
    $studentLesson->update(['completed_at' => now()]);

    echo "  ✅ StudentLesson ID: {$studentLesson->id} - COMPLETED\n";
}

echo "\n✅ DONE! Student should now see lesson as GREEN\n";

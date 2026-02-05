<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING INST_UNIT #10674 DEPENDENCIES ===\n\n";

// Check for StudentUnits
$studentUnits = DB::table('student_unit')
    ->where('inst_unit_id', 10674)
    ->get();

if ($studentUnits->isEmpty()) {
    echo "✅ No StudentUnits found\n";
} else {
    echo "⚠️ Found " . $studentUnits->count() . " StudentUnit(s):\n";
    foreach ($studentUnits as $unit) {
        echo "  - StudentUnit #{$unit->id}\n";
        echo "    Course Auth ID: {$unit->course_auth_id}\n";
        echo "    Course Date ID: {$unit->course_date_id}\n";
        echo "    InstUnit ID: {$unit->inst_unit_id}\n";
        echo "\n";
    }
}

// Check for InstLessons
$instLessons = DB::table('inst_lessons')
    ->where('inst_unit_id', 10674)
    ->get();

if ($instLessons->isEmpty()) {
    echo "✅ No InstLessons found\n";
} else {
    echo "⚠️ Found " . $instLessons->count() . " InstLesson(s):\n";
    foreach ($instLessons as $lesson) {
        echo "  - InstLesson #{$lesson->id}\n";
    }
}

echo "\n=== SOLUTION ===\n";
echo "Option 1: Set inst_unit_id to NULL in student_unit table (preserves student data)\n";
echo "Option 2: Delete student_unit records (removes student enrollment for this session)\n";
echo "Option 3: Delete the entire InstUnit chain (most aggressive)\n";

echo "\n";

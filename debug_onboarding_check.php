<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔬 DEBUG hasCompletedOnboarding() METHOD\n";
echo "=" . str_repeat("=", 70) . "\n\n";

$userId = 2; // Richard Clark
$studentUnitId = 84638;

echo "Inputs:\n";
echo "  user_id: $userId\n";
echo "  student_unit_id: $studentUnitId\n\n";

// Check what the query should return
echo "Raw Query Test:\n";
$count = DB::table('student_activity')
    ->where('user_id', $userId)
    ->where('student_unit_id', $studentUnitId)
    ->where('activity_type', 'onboarding_completed')
    ->count();

echo "  Count: $count\n";
echo "  Exists: " . ($count > 0 ? 'TRUE' : 'FALSE') . "\n\n";

// Check actual records
echo "Actual Records:\n";
$records = DB::table('student_activity')
    ->where('student_unit_id', $studentUnitId)
    ->where('activity_type', 'onboarding_completed')
    ->select('id', 'user_id', 'student_unit_id', 'activity_type', 'created_at')
    ->get();

foreach ($records as $record) {
    echo "  ID {$record->id}: user_id={$record->user_id}, student_unit_id={$record->student_unit_id}, type={$record->activity_type}\n";
}

if ($records->isEmpty()) {
    echo "  ❌ NO RECORDS FOUND\n\n";
    echo "Let's check if ANY records exist for this student_unit:\n";
    $anyRecords = DB::table('student_activity')
        ->where('student_unit_id', $studentUnitId)
        ->select('id', 'user_id', 'activity_type')
        ->get();
    foreach ($anyRecords as $r) {
        echo "  ID {$r->id}: user_id={$r->user_id}, type={$r->activity_type}\n";
    }
} else {
    echo "\n✅ Records exist but hasCompletedOnboarding() returns FALSE!\n";
    echo "This means the user_id doesn't match!\n\n";
    
    $actualUserId = $records->first()->user_id;
    echo "Expected user_id: $userId\n";
    echo "Actual user_id in records: $actualUserId\n";
    
    if ($actualUserId != $userId) {
        echo "\n❌ USER ID MISMATCH!\n";
        echo "The onboarding was completed by user $actualUserId\n";
        echo "But we're checking for user $userId\n\n";
        
        // Check which user ID should be used
        $courseAuthUserId = DB::table('course_auths')->where('id', 2)->value('user_id');
        echo "CourseAuth 2 belongs to user: $courseAuthUserId\n";
    }
}

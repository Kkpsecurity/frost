<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== REAL-TIME ONBOARDING MONITORING ===\n\n";

$studentUnitId = 84638;
$userId = 2;

// Count onboarding_completed records
$count = DB::table('student_activity')
    ->where('student_unit_id', $studentUnitId)
    ->where('activity_type', 'onboarding_completed')
    ->count();

echo "Current onboarding_completed records: {$count}\n\n";

// Get the latest one
$latest = DB::table('student_activity')
    ->where('student_unit_id', $studentUnitId)
    ->where('activity_type', 'onboarding_completed')
    ->orderByDesc('id')
    ->first();

if ($latest) {
    echo "Latest completion:\n";
    echo "  ID: {$latest->id}\n";
    echo "  Created: {$latest->created_at}\n";
    echo "  Data: {$latest->data}\n";
}

echo "\n--- Now checking what API will return ---\n\n";

// Simulate what hasCompletedOnboarding returns
$hasCompleted = DB::table('student_activity')
    ->where('user_id', $userId)
    ->where('student_unit_id', $studentUnitId)
    ->where('activity_type', 'onboarding_completed')
    ->exists();

echo "hasCompletedOnboarding() returns: " . ($hasCompleted ? "TRUE ✅" : "FALSE ❌") . "\n";

// Get the actual student_unit data that would be returned
$studentUnit = DB::table('student_unit')->where('id', $studentUnitId)->first();

echo "\nStudentUnit ID: {$studentUnit->id}\n";
echo "Course Auth ID: {$studentUnit->course_auth_id}\n";
echo "Course Date ID: {$studentUnit->course_date_id}\n";
echo "Inst Unit ID: {$studentUnit->inst_unit_id}\n";

echo "\n--- API Response Preview ---\n";
echo json_encode([
    'id' => $studentUnit->id,
    'course_auth_id' => (int)$studentUnit->course_auth_id,
    'course_date_id' => (int)$studentUnit->course_date_id,
    'onboarding_completed' => $hasCompleted,
], JSON_PRETTY_PRINT);

echo "\n";

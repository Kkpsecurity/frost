<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SIMULATING /classroom/class/data RESPONSE ===\n\n";

$userId = 2;
$courseAuthId = 2;
$studentUnitId = 84638;

// Check onboarding completion
$onboardingCompleted = DB::table('student_activity')
    ->where('user_id', $userId)
    ->where('student_unit_id', $studentUnitId)
    ->where('activity_type', 'onboarding_completed')
    ->exists();

echo "onboarding_completed check: " . ($onboardingCompleted ? "TRUE ✅" : "FALSE ❌") . "\n\n";

// Get student unit
$studentUnit = DB::table('student_unit')->where('id', $studentUnitId)->first();

// Simulate the API response studentUnit object
$response = [
    'id' => $studentUnit->id,
    'course_auth_id' => (int)$studentUnit->course_auth_id,
    'course_date_id' => (int)$studentUnit->course_date_id,
    'joined_at' => $studentUnit->created_at,
    'terms_accepted' => false,
    'rules_accepted' => DB::table('student_activity')
        ->where('user_id', $userId)
        ->where('student_unit_id', $studentUnitId)
        ->where('activity_type', 'rules_accepted')
        ->exists(),
    'onboarding_completed' => $onboardingCompleted,
    'verified' => false,
];

echo "API Response studentUnit object:\n";
echo json_encode($response, JSON_PRETTY_PRINT);

echo "\n\n";
echo "Frontend check: !studentUnit?.onboarding_completed\n";
echo "Result: !" . ($onboardingCompleted ? "true" : "false") . " = " . (!$onboardingCompleted ? "true (shows onboarding)" : "false (allows classroom)") . "\n";

if ($onboardingCompleted) {
    echo "\n✅ Student should be allowed into classroom\n";
} else {
    echo "\n❌ Student will be stuck in onboarding loop\n";
}

echo "\n";

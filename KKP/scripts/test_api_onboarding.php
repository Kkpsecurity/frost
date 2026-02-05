<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 TESTING onboarding_completed IN API RESPONSE\n";
echo "=" . str_repeat("=", 70) . "\n\n";

$studentUnitId = 84638;
$courseAuthId = 2;

// Get user_id through course_auth
$studentUnit = DB::table('student_unit')->where('id', $studentUnitId)->first();
$userId = DB::table('course_auths')->where('id', $studentUnit->course_auth_id)->value('user_id');

echo "StudentUnit: $studentUnitId\n";
echo "Course Auth: $courseAuthId\n";
echo "User: $userId\n\n";

// Simulate the hasCompletedOnboarding method
$hasCompleted = DB::table('student_activity')
    ->where('user_id', $userId)
    ->where('student_unit_id', $studentUnitId)
    ->where('activity_type', 'onboarding_completed')
    ->exists();

echo "Database check (hasCompletedOnboarding): " . ($hasCompleted ? 'TRUE' : 'FALSE') . "\n\n";

// Simulate the actual API response structure from getClassData() line 814
$studentUnit = DB::table('student_unit')->where('id', $studentUnitId)->first();

$apiResponse = [
    'studentUnit' => [
        'id' => $studentUnit->id,
        'course_auth_id' => (int) ($studentUnit->course_auth_id ?? 0),
        'course_date_id' => (int) ($studentUnit->course_date_id ?? 0),
        'joined_at' => $studentUnit->created_at,
        'terms_accepted' => false, // tracked in student_activity
        'rules_accepted' => DB::table('student_activity')
            ->where('user_id', $userId)
            ->where('student_unit_id', $studentUnit->id)
            ->where('activity_type', 'rules_accepted')
            ->exists(),
        'onboarding_completed' => $hasCompleted, // THIS IS THE KEY FIELD
        'verified' => (bool) ($studentUnit->verified ?? false),
    ],
];

echo "API RESPONSE:\n";
echo json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n\n";

echo "✅ onboarding_completed = " . ($apiResponse['studentUnit']['onboarding_completed'] ? 'TRUE' : 'FALSE') . "\n\n";

if ($apiResponse['studentUnit']['onboarding_completed']) {
    echo "🎉 Frontend SHOULD show classroom (onboarding complete)\n";
} else {
    echo "⚠️ Frontend WILL show onboarding screen (not complete)\n";
}

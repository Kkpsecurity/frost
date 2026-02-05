<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 TESTING /classroom/class/data API ENDPOINT\n";
echo "=" . str_repeat("=", 70) . "\n\n";

$userId = 33776; // Student ID (michael.d)
$courseAuthId = 20663; // D class

echo "USER: $userId\n";
echo "COURSE AUTH: $courseAuthId\n\n";

// Simulate API call to getClassData
$user = App\Models\User::find($userId);
if (!$user) {
    die("User not found\n");
}

echo "Found user: {$user->fname} {$user->lname}\n";

// Check auth
$courseAuth = App\Models\CourseAuth::where('id', $courseAuthId)
    ->where('user_id', $userId)
    ->first();

if (!$courseAuth) {
    die("CourseAuth not found\n");
}

echo "Found CourseAuth for course: {$courseAuth->course?->title}\n\n";

// Find today's course date
$today = now()->format('Y-m-d');
$courseDate = App\Models\CourseDate::with(['CourseUnit', 'InstUnit'])
    ->whereHas('CourseUnit', function($q) use ($courseAuth) {
        $q->where('course_id', $courseAuth->course_id);
    })
    ->whereDate('starts_at', $today)
    ->first();

if (!$courseDate) {
    die("No CourseDate found for today\n");
}

echo "Found CourseDate: {$courseDate->id}\n";
echo "Starts at: {$courseDate->starts_at}\n";
echo "InstUnit: " . ($courseDate->InstUnit ? $courseDate->InstUnit->id : 'NO') . "\n\n";

// Find StudentUnit
$studentUnit = App\Models\StudentUnit::where('course_auth_id', $courseAuth->id)
    ->where('course_date_id', $courseDate->id)
    ->first();

if (!$studentUnit) {
    die("No StudentUnit found\n");
}

echo "Found StudentUnit: {$studentUnit->id}\n\n";

// Check onboarding completion using the helper logic
$hasCompleted = App\Models\StudentActivity::where('user_id', $userId)
    ->where('student_unit_id', $studentUnit->id)
    ->where('activity_type', 'onboarding_completed')
    ->exists();

echo "Onboarding completed (from student_activity): " . ($hasCompleted ? 'TRUE' : 'FALSE') . "\n\n";

// Simulate the API response structure (from getClassData around line 807)
$apiResponse = [
    'studentUnit' => [
        'id' => $studentUnit->id,
        'course_auth_id' => (int) ($studentUnit->course_auth_id ?? 0),
        'course_date_id' => (int) ($studentUnit->course_date_id ?? 0),
        'joined_at' => $studentUnit->created_at,
        'terms_accepted' => false, // tracked in student_activity
        'rules_accepted' => App\Models\StudentActivity::where('user_id', $userId)
            ->where('student_unit_id', $studentUnit->id)
            ->where('activity_type', App\Models\StudentActivity::TYPE_RULES_ACCEPTED)
            ->exists(),
        'onboarding_completed' => $hasCompleted,
        'verified' => (bool) ($studentUnit->verified ?? false),
    ],
];

echo "API RESPONSE studentUnit:\n";
echo json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n\n";

echo "✅ onboarding_completed = " . ($apiResponse['studentUnit']['onboarding_completed'] ? 'TRUE' : 'FALSE') . "\n";
echo "\nIf TRUE, student should be able to enter classroom.\n";
echo "If FALSE, there's a bug in the API or frontend.\n";

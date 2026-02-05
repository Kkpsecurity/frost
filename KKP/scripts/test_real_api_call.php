<?php

// Test the ACTUAL API endpoint as the browser calls it
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate logged-in user
$user = App\Models\User::find(2); // User ID from the test
Auth::login($user);

echo "🌐 SIMULATING ACTUAL API CALL: /classroom/class/data?course_auth_id=2\n";
echo "=" . str_repeat("=", 70) . "\n\n";

// Create a request
$request = new Illuminate\Http\Request();
$request->merge(['course_auth_id' => 2]);

// Call the actual controller method
$controller = new App\Http\Controllers\Student\StudentDashboardController();
$response = $controller->getClassData($request);

$responseData = json_decode($response->getContent(), true);

echo "RESPONSE STATUS: " . $response->status() . "\n\n";

if (isset($responseData['data']['studentUnit'])) {
    echo "studentUnit object:\n";
    echo json_encode($responseData['data']['studentUnit'], JSON_PRETTY_PRINT) . "\n\n";
    
    $onboardingComplete = $responseData['data']['studentUnit']['onboarding_completed'] ?? 'KEY_MISSING';
    echo "onboarding_completed value: " . json_encode($onboardingComplete) . "\n\n";
    
    if ($onboardingComplete === true) {
        echo "✅ API is returning onboarding_completed = TRUE\n";
        echo "Frontend SHOULD show classroom\n";
    } elseif ($onboardingComplete === false) {
        echo "⚠️ API is returning onboarding_completed = FALSE\n";
        echo "Frontend WILL show onboarding screen\n";
    } else {
        echo "❌ API is NOT including onboarding_completed field!\n";
        echo "Frontend will treat as FALSE (undefined)\n";
    }
} else {
    echo "❌ No studentUnit in response!\n";
    echo "Full response:\n";
    echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
}

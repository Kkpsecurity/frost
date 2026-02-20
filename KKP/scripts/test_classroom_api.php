<?php

// Direct test of getClassData to find the 500 error
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 TESTING /classroom/class/data ENDPOINT\n";
echo "=" . str_repeat("=", 70) . "\n\n";

try {
    // Login as the student
    $user = App\Models\User::find(2);
    if (!$user) {
        die("User 2 not found\n");
    }

    Auth::login($user);
    echo "✓ Logged in as: {$user->fname} {$user->lname} (ID: {$user->id})\n\n";

    // Create request
    $request = Request::create('/classroom/class/data', 'GET', ['course_auth_id' => 2]);

    echo "📡 Calling: GET /classroom/class/data?course_auth_id=2\n\n";

    // Call controller
    $controller = new App\Http\Controllers\Frontend\Student\StudentDashboardController();
    $response = $controller->getClassData($request);

    echo "✅ STATUS: " . $response->status() . "\n\n";

    $data = json_decode($response->getContent(), true);

    if ($response->status() === 200 && isset($data['data']['studentUnit'])) {
        echo "studentUnit:\n";
        echo json_encode($data['data']['studentUnit'], JSON_PRETTY_PRINT) . "\n\n";

        $onboarding = $data['data']['studentUnit']['onboarding_completed'] ?? 'MISSING';
        echo "onboarding_completed: " . json_encode($onboarding) . "\n\n";

        if ($onboarding === true) {
            echo "✅ API SUCCESS - Returns onboarding_completed = TRUE\n";
        } else {
            echo "⚠️ API ISSUE - onboarding_completed = " . json_encode($onboarding) . "\n";
        }
    } else {
        echo "❌ ERROR RESPONSE:\n";
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
} catch (\Throwable $e) {
    echo "\n❌ EXCEPTION CAUGHT:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test Student Poll Endpoint ===\n\n";

// Authenticate as user 2
$user = \App\Models\User::find(2);
if (!$user) {
    die("User 2 not found\n");
}

\Illuminate\Support\Facades\Auth::login($user);

echo "✅ Authenticated as: {$user->fname} {$user->lname} (ID: {$user->id})\n\n";

// Create request
$request = \Illuminate\Http\Request::create('/classroom/student/poll', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user;
});

// Call the controller
$controller = new \App\Http\Controllers\Student\StudentDashboardController();
$response = $controller->getStudentPollData($request);

// Get the response data
$responseData = json_decode($response->getContent(), true);

echo "Response Status: " . $response->getStatusCode() . "\n\n";

if (isset($responseData['data']['challenges'])) {
    echo "✅ Challenges key exists in response\n";
    echo "   Challenges count: " . count($responseData['data']['challenges']) . "\n\n";

    if (count($responseData['data']['challenges']) > 0) {
        echo "Sample challenges:\n";
        foreach (array_slice($responseData['data']['challenges'], 0, 5) as $challenge) {
            echo "  - ID {$challenge['id']}: {$challenge['lesson_name']}\n";
            echo "    Type: {$challenge['type']}\n";
            echo "    Completed: " . ($challenge['completed_at'] ? 'YES' : 'NO') . "\n";
            echo "    Failed: " . ($challenge['failed_at'] ? 'YES' : 'NO') . "\n";
            echo "    Expired: " . ($challenge['expired_at'] ? 'YES' : 'NO') . "\n\n";
        }
    } else {
        echo "❌ Challenges array is empty\n";
    }
} else {
    echo "❌ Challenges key missing from response\n";
}

echo "\n📋 Full response structure:\n";
echo "Keys in response.data: " . implode(', ', array_keys($responseData['data'] ?? [])) . "\n";

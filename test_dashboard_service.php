<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing StudentDashboardService\n";
echo "===============================\n";

try {
    $user = \App\Models\User::find(2);

    if (!$user) {
        echo "User not found!\n";
        exit;
    }

    echo "User: {$user->email}\n";

    // Test StudentDashboardService directly
    $service = new \App\Services\StudentDashboardService($user);

    echo "\nTesting getCourseAuths method:\n";
    $courseAuths = $service->getCourseAuths();

    echo "Course auths returned: " . $courseAuths->count() . "\n";

    if ($courseAuths->count() > 0) {
        foreach ($courseAuths as $index => $courseAuth) {
            echo "Course Auth #{$index}:\n";
            echo "  ID: {$courseAuth->id}\n";
            echo "  User ID: {$courseAuth->user_id}\n";
            echo "  Course ID: {$courseAuth->course_id}\n";
            echo "  Created: {$courseAuth->created_at}\n";
            echo "\n";
        }
    } else {
        echo "No course auths found!\n";

        // Debug the individual queries
        echo "\nDebugging individual queries:\n";

        echo "ActiveCourseAuths direct test:\n";
        $active = $user->ActiveCourseAuths()->get();
        echo "Active count: " . $active->count() . "\n";

        echo "InActiveCourseAuths with completed_at filter:\n";
        $completed = $user->InActiveCourseAuths()->whereNotNull('completed_at')->get();
        echo "Completed count: " . $completed->count() . "\n";

        echo "Concat result:\n";
        $merged = $active->concat($completed);
        echo "Merged count: " . $merged->count() . "\n";

        echo "Values result:\n";
        $final = $merged->values();
        echo "Final count: " . $final->count() . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

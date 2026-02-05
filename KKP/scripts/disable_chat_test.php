<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Classes\ChatLogCache;

// Get course_date_id from command line or use default
$courseDateId = isset($argv[1]) ? (int)$argv[1] : null;

if (!$courseDateId) {
    echo "Usage: php disable_chat_test.php <course_date_id>\n";
    echo "Example: php disable_chat_test.php 123\n\n";

    // Try to find an active course_date_id
    $instUnit = \App\Models\InstUnit::orderBy('created_at', 'desc')->first();
    if ($instUnit) {
        $courseDateId = $instUnit->course_date_id;
        echo "Found active course_date_id: {$courseDateId}\n";
    } else {
        echo "No active InstUnit found.\n";
        exit(1);
    }
}

echo "Course Date ID: {$courseDateId}\n";
echo "Current chat status: " . (ChatLogCache::IsEnabled($courseDateId) ? "ENABLED" : "DISABLED") . "\n";

echo "\nDisabling chat...\n";
ChatLogCache::Disable($courseDateId);

echo "New chat status: " . (ChatLogCache::IsEnabled($courseDateId) ? "ENABLED" : "DISABLED") . "\n";

echo "\nDone! Refresh the student page to see the disabled chat.\n";

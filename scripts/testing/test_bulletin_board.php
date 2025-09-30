<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Bulletin Board Service ===\n\n";

try {
    $service = new \App\Services\Frost\Instructors\CourseDatesService();
    $result = $service->getBulletinBoardData();

    echo "✅ Bulletin Board Service Working\n";
    echo "Data keys: " . implode(', ', array_keys($result)) . "\n";

    if (isset($result['upcoming_courses'])) {
        echo "Upcoming courses count: " . count($result['upcoming_courses']) . "\n";
        if (count($result['upcoming_courses']) > 0) {
            $first = $result['upcoming_courses'][0];
            echo "First course module: " . ($first['module'] ?? 'N/A') . "\n";
        }
    }

} catch (\Exception $e) {
    echo "❌ Bulletin Board Service Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";

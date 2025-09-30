<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Carbon\Carbon;

echo "🗓️  Testing Course Scheduling Rules\n";
echo "===================================\n\n";

// Test dates for this week and next week
$testDates = [
    Carbon::parse('2025-09-23'), // Tuesday (today)
    Carbon::parse('2025-09-24'), // Wednesday
    Carbon::parse('2025-09-25'), // Thursday
    Carbon::parse('2025-09-26'), // Friday
    Carbon::parse('2025-09-29'), // Monday (next week)
    Carbon::parse('2025-09-30'), // Tuesday (next week)
    Carbon::parse('2025-10-01'), // Wednesday (next week)
];

echo "📅 Date Analysis:\n";
    foreach ($testDates as $date) {
    echo $date->format('Y-m-d (l)') . "\n";
    echo "  Week start: " . $date->copy()->startOfWeek()->format('Y-m-d') . "\n";

    // Test G class logic
    $referenceDate = Carbon::parse('2025-09-30'); // Known G class week (Monday)
    $weeksDifference = $date->copy()->startOfWeek()->diffInWeeks($referenceDate->copy()->startOfWeek());
    $isGWeek = $weeksDifference % 2 === 0;

    echo "  Weeks from reference: {$weeksDifference}\n";
    echo "  Is G week: " . ($isGWeek ? 'YES' : 'NO') . "\n";
    echo "  Day of week: " . $date->dayOfWeek . " (" . $date->format('l') . ")\n";
    echo "  G class eligible: " . (in_array($date->dayOfWeek, [1, 2, 3]) && $isGWeek ? 'YES' : 'NO') . "\n";
    echo "---\n";
}

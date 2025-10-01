<?php
/**
 * Debug timestamp issue
 */

require_once __DIR__ . '/vendor/autoload.php';

// Boot Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TIMESTAMP DEBUG ===\n";

$timestamp = 1743715322;
echo "Raw timestamp: {$timestamp}\n";
echo "Converted to date: " . date('Y-m-d H:i:s', $timestamp) . "\n";
echo "Carbon parse: " . \Carbon\Carbon::parse($timestamp)->format('Y-m-d H:i:s') . "\n";
echo "Today: " . now()->format('Y-m-d') . "\n";

// Check if they match
$completedDay = \Carbon\Carbon::parse($timestamp)->format('Y-m-d');
$todayDay = now()->format('Y-m-d');
echo "Do they match? " . ($completedDay === $todayDay ? 'YES' : 'NO') . "\n";
echo "Completed day: {$completedDay}\n";
echo "Today day: {$todayDay}\n";

echo "\n=== CHECKING INSTUNIT DIRECTLY ===\n";
$instUnit = \App\Models\InstUnit::find(10459);
if ($instUnit) {
    echo "InstUnit 10459 found\n";
    echo "created_at: {$instUnit->created_at}\n";
    echo "completed_at: {$instUnit->completed_at}\n";
    echo "created_at parsed: " . \Carbon\Carbon::parse($instUnit->created_at)->format('Y-m-d H:i:s') . "\n";
    echo "completed_at parsed: " . \Carbon\Carbon::parse($instUnit->completed_at)->format('Y-m-d H:i:s') . "\n";
} else {
    echo "InstUnit 10459 not found\n";
}

echo "\n=== END TIMESTAMP DEBUG ===\n";

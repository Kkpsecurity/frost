<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Completing Pending Challenge 1091035 ===\n\n";

$challenge = \App\Models\Challenge::find(1091035);

if (!$challenge) {
    echo "Challenge not found\n";
    exit;
}

echo "Challenge ID: {$challenge->id}\n";
echo "Created: {$challenge->created_at}\n";
echo "Expires: {$challenge->expires_at}\n";
echo "Completed: " . ($challenge->completed_at ?: 'NULL') . "\n";
echo "Failed: " . ($challenge->failed_at ?: 'NULL') . "\n\n";

if ($challenge->completed_at) {
    echo "Already completed!\n";
    exit;
}

echo "Marking as completed...\n";
$challenge->MarkCompleted();

echo "✅ Challenge marked as completed at: {$challenge->completed_at}\n";

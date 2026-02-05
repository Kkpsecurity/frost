<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Challenge;

echo "🔍 Checking Challenge Database Record\n";
echo "=====================================\n\n";

// Check if challenge 1091026 exists
$challenge = Challenge::find(1091026);

if ($challenge) {
    echo "✅ Challenge Found!\n";
    echo "  ID: {$challenge->id}\n";
    echo "  Student Lesson ID: {$challenge->student_lesson_id}\n";
    echo "  Student Unit ID: {$challenge->student_unit_id}\n";
    echo "  Created At: {$challenge->created_at}\n";
    echo "  Expires At: {$challenge->expires_at}\n";
    echo "  Completed At: " . ($challenge->completed_at ?? 'NULL') . "\n";
    echo "  Failed At: " . ($challenge->failed_at ?? 'NULL') . "\n";
    echo "  Response At: " . ($challenge->response_at ?? 'NULL') . "\n";
    echo "  Failure Count: " . ($challenge->failure_count ?? 0) . "\n";
    echo "\n";

    if ($challenge->completed_at) {
        echo "⚠️  Challenge is already completed!\n";
    } elseif ($challenge->failed_at) {
        echo "⚠️  Challenge is already failed!\n";
    } elseif ($challenge->expires_at < now()) {
        echo "⏰ Challenge has expired!\n";
        echo "  Expired: " . now()->diffForHumans($challenge->expires_at) . "\n";
    } else {
        echo "✅ Challenge is active and ready to display!\n";
        $timeLeft = now()->diffInSeconds($challenge->expires_at, false);
        echo "  Time Remaining: {$timeLeft} seconds\n";
    }
} else {
    echo "❌ Challenge 1091026 NOT FOUND in database!\n";
    echo "\nThis means Challenger::Ready() created the response object but didn't save the record.\n";

    // Check for any recent challenges
    echo "\n📊 Recent Challenges:\n";
    $recentChallenges = Challenge::where('student_unit_id', 100782)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    if ($recentChallenges->count() > 0) {
        foreach ($recentChallenges as $c) {
            echo "  ID: {$c->id}, Created: {$c->created_at}, Completed: " . ($c->completed_at ?? 'NULL') . "\n";
        }
    } else {
        echo "  No challenges found for this student\n";
    }
}

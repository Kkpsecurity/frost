<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Challenge;
use App\Models\StudentUnit;
use App\Models\StudentLesson;

echo "🔍 Checking Challenges for Student (User ID: 2)\n";
echo "==============================================\n\n";

// Find student units for user_id 2
$studentUnits = StudentUnit::whereHas('CourseAuth', function ($q) {
    $q->where('user_id', 2);
})->get();

if ($studentUnits->isEmpty()) {
    echo "❌ No student units found for user_id 2\n";
    exit;
}

echo "📊 Found " . $studentUnits->count() . " student unit(s) for user_id 2\n\n";

foreach ($studentUnits as $studentUnit) {
    echo "Student Unit ID: {$studentUnit->id}\n";
    echo "Course Date ID: {$studentUnit->course_date_id}\n";

    // Get all challenges for this student unit
    $challenges = Challenge::whereHas('StudentLesson', function ($q) use ($studentUnit) {
        $q->where('student_unit_id', $studentUnit->id);
    })->orderBy('created_at', 'desc')->get();

    echo "Total Challenges: " . $challenges->count() . "\n\n";

    if ($challenges->count() > 0) {
        echo "Recent Challenges:\n";
        echo str_repeat("-", 100) . "\n";
        printf(
            "%-10s %-20s %-20s %-20s %-20s %-8s %-8s\n",
            "ID",
            "Created",
            "Expires",
            "Completed",
            "Failed",
            "IsFinal",
            "IsEOL"
        );
        echo str_repeat("-", 100) . "\n";

        foreach ($challenges->take(10) as $challenge) {
            $createdAt = $challenge->created_at ? $challenge->created_at->format('Y-m-d H:i:s') : 'NULL';
            $expiresAt = $challenge->expires_at ? $challenge->expires_at->format('Y-m-d H:i:s') : 'NULL';
            $completedAt = $challenge->completed_at ? $challenge->completed_at->format('Y-m-d H:i:s') : 'NULL';
            $failedAt = $challenge->failed_at ? $challenge->failed_at->format('Y-m-d H:i:s') : 'NULL';
            $isFinal = $challenge->is_final ? 'Yes' : 'No';
            $isEOL = $challenge->is_eol ? 'Yes' : 'No';

            printf(
                "%-10s %-20s %-20s %-20s %-20s %-8s %-8s\n",
                $challenge->id,
                $createdAt,
                $expiresAt,
                $completedAt,
                $failedAt,
                $isFinal,
                $isEOL
            );
        }

        echo str_repeat("-", 100) . "\n\n";

        // Statistics
        $completedCount = $challenges->whereNotNull('completed_at')->count();
        $failedCount = $challenges->whereNotNull('failed_at')->count();
        $pendingCount = $challenges->whereNull('completed_at')->whereNull('failed_at')->count();

        echo "📈 Statistics:\n";
        echo "  Completed: {$completedCount}\n";
        echo "  Failed: {$failedCount}\n";
        echo "  Pending: {$pendingCount}\n";

        // Check for expired pending challenges
        $expiredPending = $challenges->filter(function ($c) {
            return is_null($c->completed_at) && is_null($c->failed_at) && $c->expires_at < now();
        })->count();

        if ($expiredPending > 0) {
            echo "  ⚠️  Expired (not marked failed): {$expiredPending}\n";
        }
    } else {
        echo "No challenges found for this student unit.\n";
    }

    echo "\n";
}

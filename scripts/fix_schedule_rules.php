<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧹 Fixing Course Schedule According to Rules\n";
echo "===========================================\n\n";

use App\Models\CourseDate;
use Carbon\Carbon;

try {
    // Rules:
    // This week (Sept 22-27): D classes only (NO G classes - off week)
    // Next week (Sept 29 - Oct 3): D classes Mon-Fri + G classes Mon-Wed

    $thisWeekStart = Carbon::parse('2025-09-22'); // Sunday
    $thisWeekEnd = Carbon::parse('2025-09-27');   // Friday

    echo "📅 This Week Rules (Sept 22-27): D classes only, NO G classes\n";
    echo "📅 Next Week Rules (Sept 29 - Oct 3): D classes Mon-Fri + G classes Mon-Wed\n\n";

    // Step 1: Remove G classes from this week (they shouldn't exist)
    echo "🗑️  Step 1: Removing G classes from this week (off-week)...\n";

    $thisWeekGClasses = CourseDate::whereBetween('starts_at', [$thisWeekStart, $thisWeekEnd])
        ->whereHas('courseUnit.course', function($query) {
            $query->where('title', 'like', '%G28%');
        })
        ->get();

    echo "   Found " . $thisWeekGClasses->count() . " G classes to remove\n";

    foreach ($thisWeekGClasses as $gClass) {
        $date = Carbon::parse($gClass->starts_at)->format('Y-m-d');
        $courseName = $gClass->courseUnit->course->title ?? 'Unknown';
        echo "   🗑️  Removing: {$courseName} on {$date} (ID: {$gClass->id})\n";
        $gClass->delete();
    }

    // Step 2: Check next week's G classes (should only be Mon-Wed)
    echo "\n📋 Step 2: Checking next week's G classes (should be Mon-Wed only)...\n";

    $nextWeekStart = Carbon::parse('2025-09-29'); // Monday
    $nextWeekEnd = Carbon::parse('2025-10-05');   // Next Sunday

    $nextWeekGClasses = CourseDate::whereBetween('starts_at', [$nextWeekStart, $nextWeekEnd])
        ->whereHas('courseUnit.course', function($query) {
            $query->where('title', 'like', '%G28%');
        })
        ->get();

    echo "   Found " . $nextWeekGClasses->count() . " G classes next week\n";

    $validGDays = ['2025-09-29', '2025-09-30', '2025-10-01']; // Mon, Tue, Wed

    foreach ($nextWeekGClasses as $gClass) {
        $date = Carbon::parse($gClass->starts_at)->format('Y-m-d');
        $courseName = $gClass->courseUnit->course->title ?? 'Unknown';

        if (in_array($date, $validGDays)) {
            echo "   ✅ Valid: {$courseName} on {$date} (Mon-Wed)\n";
        } else {
            echo "   🗑️  Invalid: {$courseName} on {$date} (not Mon-Wed) - Removing (ID: {$gClass->id})\n";
            $gClass->delete();
        }
    }

    echo "\n✅ Schedule cleanup complete!\n";
    echo "\nCurrent Schedule Should Be:\n";
    echo "========================\n";
    echo "This Week (Sept 22-27): D classes only\n";
    echo "Next Week (Sept 29 - Oct 3): D classes Mon-Fri + G classes Mon-Wed\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

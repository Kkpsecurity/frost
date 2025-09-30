<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking CourseDate records for Sept 22-27, 2025...\n";
echo "=================================================\n\n";

use App\Models\CourseDate;
use Carbon\Carbon;

$startDate = Carbon::parse('2025-09-22');
$endDate = Carbon::parse('2025-09-27');

$courseDates = CourseDate::whereBetween('starts_at', [$startDate, $endDate->endOfDay()])
    ->with('courseUnit.course')
    ->orderBy('starts_at')
    ->get();

echo "Found: " . $courseDates->count() . " CourseDate records\n\n";

if ($courseDates->count() > 0) {
    foreach ($courseDates as $cd) {
        $startsAt = $cd->starts_at instanceof Carbon ? $cd->starts_at : Carbon::parse($cd->starts_at);
        $endsAt = $cd->ends_at instanceof Carbon ? $cd->ends_at : Carbon::parse($cd->ends_at);

        echo sprintf(
            "ID: %d | Date: %s | Course: %s | Unit: %s | Time: %s - %s\n",
            $cd->id,
            $startsAt->format('Y-m-d (l)'),
            $cd->courseUnit->course->title ?? 'N/A',
            $cd->courseUnit->admin_title ?? 'N/A',
            $startsAt->format('H:i'),
            $endsAt->format('H:i')
        );
    }
} else {
    echo "❌ No CourseDate records found for this week!\n";
    echo "📅 This explains why the calendar is empty.\n\n";

    echo "💡 Solution: Generate CourseDate records for this week\n";
    echo "   Command: php artisan course:generate-dates --range=2025-09-22,2025-09-27\n";
}

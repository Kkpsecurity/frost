<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate the SupportController logic
$studentId = 2;
$courseId = 2;

$user = App\Models\User::find($studentId);
$courseAuth = $user->courseAuths()->where('id', $courseId)->first();

echo "Getting StudentUnits for course_auth_id: {$courseId}\n\n";

$studentUnits = $courseAuth->StudentUnits()
    ->with('CourseDate')
    ->get();

echo "Total StudentUnits: " . $studentUnits->count() . "\n\n";

// Create attendance map
$attendedDates = [];
foreach ($studentUnits as $studentUnit) {
    if ($studentUnit->CourseDate && $studentUnit->CourseDate->starts_at) {
        $date = \Carbon\Carbon::parse($studentUnit->CourseDate->starts_at)->format('Y-m-d');
        $attendedDates[$date] = [
            'course_date_id' => $studentUnit->course_date_id,
            'student_unit_id' => $studentUnit->id,
        ];
        echo "Found attendance for: {$date}\n";
    }
}

echo "\n\nGenerating week days:\n";
$monday = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
$weeklyAttendance = [];

for ($i = 0; $i < 5; $i++) {
    $date = $monday->copy()->addDays($i);
    $dateStr = $date->format('Y-m-d');

    $weeklyAttendance[] = [
        'date' => $dateStr,
        'dayName' => $date->format('l'),
        'isPresent' => isset($attendedDates[$dateStr]),
        'courseDateId' => $attendedDates[$dateStr]['course_date_id'] ?? null,
    ];

    $status = isset($attendedDates[$dateStr]) ? 'PRESENT' : 'ABSENT';
    echo "Day " . ($i+1) . ": {$date->format('l, M d')} ({$dateStr}) - {$status}\n";
}

echo "\n\nToday is: " . \Carbon\Carbon::now()->format('l, M d, Y (Y-m-d)') . "\n";

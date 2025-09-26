<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Creating test data for Assignment History Table\n\n";

// Create a few more course dates with different assignment states
$courseDates = [
    [
        'course_unit_id' => 2, // Day 2
        'date' => '2025-09-18', // Yesterday
        'start_time' => '08:00',
        'end_time' => '17:00',
        'create_instunit' => true,
        'complete_instunit' => true
    ],
    [
        'course_unit_id' => 3, // Day 3
        'date' => '2025-09-17', // Day before yesterday
        'start_time' => '08:00',
        'end_time' => '17:00',
        'create_instunit' => true,
        'complete_instunit' => false // Active InstUnit
    ],
    [
        'course_unit_id' => 4, // Day 4
        'date' => '2025-09-16', // 3 days ago
        'start_time' => '08:00',
        'end_time' => '17:00',
        'create_instunit' => false // No InstUnit (unassigned)
    ]
];

foreach ($courseDates as $index => $courseData) {
    echo "Creating CourseDate " . ($index + 1) . "...\n";

    // Create CourseDate
    $courseDateId = DB::table('course_dates')->insertGetId([
        'is_active' => true,
        'course_unit_id' => $courseData['course_unit_id'],
        'starts_at' => $courseData['date'] . ' ' . $courseData['start_time'] . ':00:00-04:00',
        'ends_at' => $courseData['date'] . ' ' . $courseData['end_time'] . ':00:00-04:00'
    ]);

    echo "  ✅ CourseDate created (ID: {$courseDateId})\n";

    if ($courseData['create_instunit']) {
        echo "  Creating InstUnit...\n";

        $instUnitData = [
            'course_date_id' => $courseDateId,
            'created_at' => $courseData['date'] . ' ' . $courseData['start_time'] . ':30:00-04:00',
            'created_by' => 13, // Admin user
            'completed_at' => null,
            'completed_by' => null,
            'assistant_id' => null
        ];

        if ($courseData['complete_instunit']) {
            $instUnitData['completed_at'] = $courseData['date'] . ' ' . $courseData['end_time'] . ':00:00-04:00';
            $instUnitData['completed_by'] = 13;
        }

        $instUnitId = DB::table('inst_unit')->insertGetId($instUnitData);
        echo "  ✅ InstUnit created (ID: {$instUnitId}) - " . ($courseData['complete_instunit'] ? 'COMPLETED' : 'ACTIVE') . "\n";
    } else {
        echo "  🟡 No InstUnit (UNASSIGNED state)\n";
    }

    echo "\n";
}

echo "🎯 Test data created! The Assignment History Table should now show:\n";
echo "  - Day 5 (today): UNASSIGNED (no InstUnit)\n";
echo "  - Day 2 (yesterday): COMPLETED (InstUnit with completed_at)\n";
echo "  - Day 3 (2 days ago): ASSIGNED (InstUnit without completed_at)\n";
echo "  - Day 4 (3 days ago): UNASSIGNED (no InstUnit)\n\n";

echo "Check the instructor dashboard at: https://frost.test/admin/instructors\n";

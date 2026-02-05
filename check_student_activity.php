<?php

/**
 * Check Student Activity Records
 *
 * Quick script to view today's student activity records
 * Run from command line: php check_student_activity.php
 * Or access via browser: http://frost.test/check_student_activity.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== Student Activity Records for Today ===\n";
echo "Date: " . date('Y-m-d') . "\n";
echo str_repeat("=", 80) . "\n\n";

try {
    $activities = DB::table('student_activity')
        ->whereDate('created_at', today())
        ->orderBy('created_at', 'desc')
        ->get([
            'id',
            'user_id',
            'course_auth_id',
            'course_date_id',
            'student_unit_id',
            'inst_unit_id',
            'category',
            'activity_type',
            'description',
            'created_at'
        ]);

    if ($activities->isEmpty()) {
        echo "No activities recorded today.\n\n";
    } else {
        echo "Total Activities: " . $activities->count() . "\n\n";

        foreach ($activities as $activity) {
            echo "ID: {$activity->id}\n";
            echo "User ID: {$activity->user_id}\n";
            echo "Course Auth: " . ($activity->course_auth_id ?? 'null') . "\n";
            echo "Course Date: " . ($activity->course_date_id ?? 'null') . "\n";
            echo "Student Unit: {$activity->student_unit_id}\n";
            echo "Inst Unit: " . ($activity->inst_unit_id ?? 'null') . "\n";
            echo "Category: {$activity->category}\n";
            echo "Activity Type: {$activity->activity_type}\n";
            echo "Description: {$activity->description}\n";
            echo "Time: {$activity->created_at}\n";
            echo str_repeat("-", 80) . "\n\n";
        }
    }

    // Distinct course_date_id values for today
    $distinctCourseDates = DB::table('student_activity')
        ->whereDate('created_at', today())
        ->select('course_date_id', DB::raw('COUNT(*) as count'))
        ->groupBy('course_date_id')
        ->orderBy('count', 'desc')
        ->get();

    if ($distinctCourseDates->isNotEmpty()) {
        echo "\n=== Course Date Summary (Today) ===\n";
        foreach ($distinctCourseDates as $row) {
            $label = $row->course_date_id ?? 'null';
            echo sprintf("%-30s : %d\n", (string) $label, $row->count);
        }
        echo "\n";
    }

    // Summary by activity type
    $summary = DB::table('student_activity')
        ->whereDate('created_at', today())
        ->select('activity_type', DB::raw('COUNT(*) as count'))
        ->groupBy('activity_type')
        ->orderBy('count', 'desc')
        ->get();

    if ($summary->isNotEmpty()) {
        echo "\n=== Activity Summary ===\n";
        foreach ($summary as $item) {
            echo sprintf("%-30s : %d\n", $item->activity_type, $item->count);
        }
        echo "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "Done.\n\n";

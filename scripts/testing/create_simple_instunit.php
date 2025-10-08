<?php

// Simple script to create test InstUnit
echo "Creating test InstUnit...\n";

// Create InstUnit with just instructor (no assistant)
$instUnit = new stdClass();
$instUnit->course_date_id = 10539;  // Use existing course date
$instUnit->created_by = 3;          // Craig as instructor
$instUnit->created_at = date('Y-m-d H:i:s');
$instUnit->assistant_id = null;     // No assistant

echo "Test InstUnit data prepared:\n";
echo "Course Date ID: {$instUnit->course_date_id}\n";
echo "Instructor ID: {$instUnit->created_by}\n";
echo "Assistant ID: " . ($instUnit->assistant_id ?? 'NULL') . "\n";
echo "Created At: {$instUnit->created_at}\n";

echo "\nTo actually insert into database, use SQL:\n";
echo "INSERT INTO inst_unit (course_date_id, created_by, created_at) VALUES ({$instUnit->course_date_id}, {$instUnit->created_by}, '{$instUnit->created_at}');\n";

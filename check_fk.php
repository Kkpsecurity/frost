<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Foreign Key Reference Check\n";
echo "===========================\n";

// Check users
$userCount = DB::selectOne('SELECT COUNT(*) as count FROM users')->count;
echo "Users in database: $userCount\n";

// Check specific user IDs from the sample data
$testUsers = [20077, 27585, 17422, 10019, 17736, 11127, 10];
foreach ($testUsers as $userId) {
    $exists = DB::selectOne('SELECT COUNT(*) as count FROM users WHERE id = ?', [$userId])->count;
    echo "User $userId: " . ($exists ? 'EXISTS' : 'MISSING') . "\n";
}

echo "\n";

// Check courses
$courseCount = DB::selectOne('SELECT COUNT(*) as count FROM courses')->count;
echo "Courses in database: $courseCount\n";

// Check specific course IDs
$testCourses = [1, 3];
foreach ($testCourses as $courseId) {
    $exists = DB::selectOne('SELECT COUNT(*) as count FROM courses WHERE id = ?', [$courseId])->count;
    echo "Course $courseId: " . ($exists ? 'EXISTS' : 'MISSING') . "\n";
}

echo "\n";

// Check ranges of IDs to understand the data scope
$userIdRange = DB::selectOne('SELECT MIN(id) as min_id, MAX(id) as max_id FROM users');
echo "User ID range: {$userIdRange->min_id} to {$userIdRange->max_id}\n";

$courseIdRange = DB::selectOne('SELECT MIN(id) as min_id, MAX(id) as max_id FROM courses');
echo "Course ID range: {$courseIdRange->min_id} to {$courseIdRange->max_id}\n";

// Check if foreign key constraints are enabled
$constraints = DB::select("
    SELECT
        tc.constraint_name,
        tc.table_name,
        kcu.column_name,
        ccu.table_name AS foreign_table_name,
        ccu.column_name AS foreign_column_name
    FROM information_schema.table_constraints AS tc
    JOIN information_schema.key_column_usage AS kcu
        ON tc.constraint_name = kcu.constraint_name
        AND tc.table_schema = kcu.table_schema
    JOIN information_schema.constraint_column_usage AS ccu
        ON ccu.constraint_name = tc.constraint_name
        AND ccu.table_schema = tc.table_schema
    WHERE tc.constraint_type = 'FOREIGN KEY'
        AND tc.table_name = 'course_auths'
");

echo "\nForeign key constraints on course_auths:\n";
foreach ($constraints as $constraint) {
    echo "- {$constraint->column_name} -> {$constraint->foreign_table_name}.{$constraint->foreign_column_name}\n";
}

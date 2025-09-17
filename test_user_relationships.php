<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing User Relationship Methods\n";
echo "=================================\n";

try {
    $user = \App\Models\User::find(2);

    if (!$user) {
        echo "User not found!\n";
        exit;
    }

    echo "User: {$user->email}\n";
    echo "Has ActiveCourseAuths method: " . (method_exists($user, 'ActiveCourseAuths') ? 'YES' : 'NO') . "\n";
    echo "Has InActiveCourseAuths method: " . (method_exists($user, 'InActiveCourseAuths') ? 'YES' : 'NO') . "\n";
    echo "Has CourseAuths relationship: " . (method_exists($user, 'CourseAuths') ? 'YES' : 'NO') . "\n";

    // Test basic CourseAuths relationship
    echo "\nBasic CourseAuths relationship:\n";
    $allCourseAuths = $user->CourseAuths()->count();
    echo "Total course auths: {$allCourseAuths}\n";

    // Test ActiveCourseAuths
    echo "\nActiveCourseAuths test:\n";
    try {
        $activeCount = $user->ActiveCourseAuths()->count();
        echo "Active course auths: {$activeCount}\n";

        if ($activeCount > 0) {
            $firstActive = $user->ActiveCourseAuths()->first();
            echo "First active course auth ID: {$firstActive->id}\n";
            echo "Course ID: {$firstActive->course_id}\n";
            echo "Created at: {$firstActive->created_at}\n";
            echo "Expire date: " . ($firstActive->expire_date ?? 'NULL') . "\n";
            echo "Completed at: " . ($firstActive->completed_at ?? 'NULL') . "\n";
            echo "Disabled at: " . ($firstActive->disabled_at ?? 'NULL') . "\n";
        }
    } catch (Exception $e) {
        echo "Error with ActiveCourseAuths: " . $e->getMessage() . "\n";
    }

    // Test InActiveCourseAuths
    echo "\nInActiveCourseAuths test:\n";
    try {
        $inactiveCount = $user->InActiveCourseAuths()->count();
        echo "Inactive course auths: {$inactiveCount}\n";
    } catch (Exception $e) {
        echo "Error with InActiveCourseAuths: " . $e->getMessage() . "\n";
    }

    // Test direct CourseAuth query
    echo "\nDirect CourseAuth query:\n";
    $directCount = \App\Models\CourseAuth::where('user_id', 2)->count();
    echo "Direct query count: {$directCount}\n";

    if ($directCount > 0) {
        $firstDirect = \App\Models\CourseAuth::where('user_id', 2)->first();
        echo "First direct course auth:\n";
        echo "  ID: {$firstDirect->id}\n";
        echo "  Course ID: {$firstDirect->course_id}\n";
        echo "  Created: {$firstDirect->created_at}\n";
        echo "  Expire date: " . ($firstDirect->expire_date ?? 'NULL') . "\n";
        echo "  Completed at: " . ($firstDirect->completed_at ?? 'NULL') . "\n";
        echo "  Disabled at: " . ($firstDirect->disabled_at ?? 'NULL') . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

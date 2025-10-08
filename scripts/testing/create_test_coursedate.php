<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Creating CourseDate for today (October 8, 2025)...\n";

$courseDate = App\Models\CourseDate::create([
    'course_id' => 1,
    'course_unit_id' => 1,
    'starts_at' => now()->startOfDay()->addHours(9), // 9 AM today
    'ends_at' => now()->startOfDay()->addHours(17),   // 5 PM today
    'description' => 'Test class for attendance detection - October 8, 2025'
]);echo "✅ Created CourseDate ID: {$courseDate->id}\n";
echo "📅 Date: {$courseDate->starts_at->toDateString()}\n";
echo "⏰ Time: {$courseDate->starts_at->toTimeString()} - {$courseDate->ends_at->toTimeString()}\n";
echo "📝 Description: {$courseDate->description}\n";

// Verify it was created
$todayCount = App\Models\CourseDate::whereDate('starts_at', now())->count();
echo "\n📊 Total CourseDate records for today: {$todayCount}\n";

echo "\n🎯 Ready to test attendance detection at: http://frost.test\n";

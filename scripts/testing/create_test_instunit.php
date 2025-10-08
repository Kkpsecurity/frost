<?php
/**
 * Simple script to create an InstUnit record in the database for testing
 */

// Laravel bootstrap
$app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDate;
use App\Models\User;
use App\Models\InstUnit;

echo "=== CREATING INSTUNIT FOR TESTING ===\n\n";

// Find a course date that doesn't have an InstUnit yet
$courseDate = CourseDate::whereDoesntHave('InstUnit')
    ->whereDate('starts_at', '>=', now()->subDays(7))
    ->whereDate('starts_at', '<=', now()->addDays(7))
    ->first();

if (!$courseDate) {
    // If no free course dates, use any course date
    $courseDate = CourseDate::first();
    echo "⚠️  Using any available CourseDate (may already have InstUnit)\n";
}

echo "Selected CourseDate:\n";
echo "  ID: {$courseDate->id}\n";
echo "  Course: " . ($courseDate->GetCourse()->title ?? 'Unknown') . "\n";
echo "  Date: {$courseDate->starts_at}\n\n";

// Find instructor and assistant users
$instructor = User::whereIn('role_id', [1, 2, 3, 4])
    ->whereNotNull('fname')
    ->first();

$assistant = User::whereIn('role_id', [1, 2, 3, 4])
    ->whereNotNull('fname')
    ->where('id', '!=', $instructor->id)
    ->first();

echo "Selected Users:\n";
echo "  Instructor: {$instructor->fname} {$instructor->lname} (ID: {$instructor->id})\n";
echo "  Assistant: {$assistant->fname} {$assistant->lname} (ID: {$assistant->id})\n\n";

// Check if InstUnit already exists
$existingInstUnit = $courseDate->InstUnit;
if ($existingInstUnit) {
    echo "⚠️  InstUnit already exists for this CourseDate: {$existingInstUnit->id}\n";
    echo "   Deleting existing InstUnit...\n";
    $existingInstUnit->delete();
}

// Create the InstUnit
echo "Creating InstUnit...\n";
$instUnit = InstUnit::create([
    'course_date_id' => $courseDate->id,
    'created_by' => $instructor->id,
    'assistant_id' => $assistant->id,
    'created_at' => now(),
]);

echo "\n✅ INSTUNIT CREATED SUCCESSFULLY!\n";
echo "   InstUnit ID: {$instUnit->id}\n";
echo "   CourseDate ID: {$instUnit->course_date_id}\n";
echo "   Instructor: {$instructor->fname} {$instructor->lname} (ID: {$instUnit->created_by})\n";
echo "   Assistant: {$assistant->fname} {$assistant->lname} (ID: {$instUnit->assistant_id})\n";
echo "   Created At: {$instUnit->created_at}\n";

echo "\n🎯 DATABASE RECORD ADDED FOR TESTING!\n";

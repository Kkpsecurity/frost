<?php

// Test script to check course_auths table structure
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "📊 Checking course_auths Table Structure\n";
echo "=========================================\n\n";

// Check if table exists
if (Schema::hasTable('course_auths')) {
    echo "✅ course_auths table exists\n\n";

    // Get column information
    $columns = Schema::getColumnListing('course_auths');
    echo "Columns in course_auths table:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }

    echo "\nSample data from course_auths:\n";
    $sample = DB::table('course_auths')->limit(3)->get();
    foreach ($sample as $row) {
        echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
    }

} else {
    echo "❌ course_auths table does not exist\n";
}

echo "\n=== Checking for D License course auths ===\n";
$dLicenseAuths = DB::table('course_auths')
    ->where('course_id', 1)
    ->get();

echo "Found " . $dLicenseAuths->count() . " course_auths for course_id 1 (D License):\n";
foreach ($dLicenseAuths as $auth) {
    echo "- User ID: {$auth->user_id}, Course ID: {$auth->course_id}\n";
}

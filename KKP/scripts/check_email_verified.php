<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking email verification status:\n\n";

// Check users table columns
$columns = DB::getSchemaBuilder()->getColumnListing('users');
echo "Users table columns:\n";
print_r($columns);

echo "\n\nEmail verification statistics:\n";

// Count unverified emails
$unverified = DB::table('users')
    ->where('role_id', '>=', 5)
    ->whereNull('email_verified_at')
    ->count();

$verified = DB::table('users')
    ->where('role_id', '>=', 5)
    ->whereNotNull('email_verified_at')
    ->count();

$total = DB::table('users')
    ->where('role_id', '>=', 5)
    ->count();

echo "Total Students: {$total}\n";
echo "Email Verified: {$verified}\n";
echo "Pending Verification (email not verified): {$unverified}\n";

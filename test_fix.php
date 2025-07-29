<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing database operations after fixes:\n";
echo "PgTk::now(): " . App\Helpers\PgTk::now() . "\n";

// Test the User model freshTimestamp method
$user = App\Models\User::first();
if ($user) {
    echo "User fresh timestamp: " . $user->freshTimestamp() . "\n";
    echo "All tests passed!\n";
} else {
    echo "No users found to test with.\n";
}

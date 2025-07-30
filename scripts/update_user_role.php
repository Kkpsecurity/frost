<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== Update User Role to SysAdmin ===\n";

// Find your user (change the email to match yours)
$user = User::where('email', 'sgundry@s2institute.com')->first(); // Change this email

if ($user) {
    echo "Found user: {$user->email}\n";
    echo "Current role ID: {$user->role_id}\n";

    // Update to SysAdmin (role_id = 1)
    $user->role_id = 1;
    $user->save();

    echo "Updated role ID to: {$user->role_id}\n";
    echo "User is now a System Administrator!\n";
} else {
    echo "User not found. Please check the email address.\n";
}

echo "\n=== End Update ===\n";

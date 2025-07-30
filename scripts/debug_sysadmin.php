<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;

echo "=== User Role Debug Test ===\n";

// Get the current authenticated user
$user = Auth::user();

if ($user) {
    echo "User ID: " . $user->id . "\n";
    echo "User Email: " . $user->email . "\n";
    echo "Role ID: " . $user->role_id . "\n";
    echo "Role ID Type: " . gettype($user->role_id) . "\n";

    // Test role methods
    echo "\nRole Methods:\n";
    echo "IsSysAdmin(): " . ($user->IsSysAdmin() ? 'true' : 'false') . "\n";
    echo "IsAdministrator(): " . ($user->IsAdministrator() ? 'true' : 'false') . "\n";
    echo "IsSupport(): " . ($user->IsSupport() ? 'true' : 'false') . "\n";
    echo "IsInstructor(): " . ($user->IsInstructor() ? 'true' : 'false') . "\n";
    echo "IsStudent(): " . ($user->IsStudent() ? 'true' : 'false') . "\n";
    echo "IsAnyAdmin(): " . ($user->IsAnyAdmin() ? 'true' : 'false') . "\n";

    // Check if Auth::check() works
    echo "\nAuth Check:\n";
    echo "Auth::check(): " . (Auth::check() ? 'true' : 'false') . "\n";

    // Test the actual BladeServiceProvider condition
    echo "\nBladeServiceProvider Test:\n";
    echo "Auth::check() && Auth::user()->IsSysAdmin(): " .
         ((Auth::check() && Auth::user()->IsSysAdmin()) ? 'true' : 'false') . "\n";

    // Check Role relationship
    if ($user->role) {
        echo "\nRole Relationship:\n";
        echo "Role Name: " . $user->role->name . "\n";
        echo "Role ID: " . $user->role->id . "\n";
    } else {
        echo "\nNo Role relationship found!\n";
    }

} else {
    echo "No authenticated user found.\n";
    echo "Please make sure you're logged in.\n";
}

echo "\n=== End Debug Test ===\n";

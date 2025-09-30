<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;

echo "=== User Menu Debug ===\n\n";

// Check authentication
$user = Auth::guard('admin')->user();
echo "Admin user authenticated: " . ($user ? 'YES' : 'NO') . "\n";

if ($user) {
    echo "User name: " . $user->name . "\n";
    echo "User ID: " . $user->id . "\n";

    // Check if user has adminlte_image method
    if (method_exists($user, 'adminlte_image')) {
        echo "User has adminlte_image method: YES\n";
        echo "User image: " . $user->getAvatar() . "\n";
    } else {
        echo "User has adminlte_image method: NO\n";
    }
}

// Check user menu settings
echo "\nUser menu settings:\n";
echo "  usermenu_enabled: " . config('adminlte.usermenu_enabled', 'NOT SET') . "\n";
echo "  usermenu_header: " . config('adminlte.usermenu_header', 'NOT SET') . "\n";
echo "  usermenu_image: " . config('adminlte.usermenu_image', 'NOT SET') . "\n";
echo "  usermenu_desc: " . config('adminlte.usermenu_desc', 'NOT SET') . "\n";

echo "\nThe user menu should show if user is authenticated AND usermenu_enabled is true.\n";

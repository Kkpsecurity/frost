<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "=== Database Debug ===\n";

// Check Roles
echo "Available Roles:\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "ID: {$role->id}, Name: {$role->name}\n";
}

echo "\nUsers and their Roles:\n";
$users = User::with('role')->limit(10)->get();
foreach ($users as $user) {
    $roleName = $user->role ? $user->role->name : 'N/A';
    echo "ID: {$user->id}, Email: {$user->email}, Role ID: {$user->role_id}, Role Name: {$roleName}\n";
}

echo "\n=== End Debug ===\n";

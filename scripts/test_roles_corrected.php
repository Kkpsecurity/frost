<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Support\RoleManager;

echo "=== UPDATED ROLEMANAGER WITH CORRECT ROLE STRUCTURE ===\n";

echo "\nRole Structure:\n";
echo "1 - SysAdmin\n";
echo "2 - Administrator\n";
echo "3 - Support\n";
echo "4 - Instructor\n";
echo "5 - Student\n";

echo "\nAdmin Role IDs: " . json_encode(RoleManager::getAdminRoleIds()) . "\n";
echo "Admin Role Names: " . json_encode(RoleManager::getAdminRoleNames()) . "\n";

echo "\nRole Options for Dropdown:\n";
foreach (RoleManager::getAdminRoleOptions() as $value => $label) {
    echo "- {$value} => {$label}\n";
}

echo "\nTesting role utilities:\n";
echo "Display name for 'sys_admin': " . RoleManager::getDisplayName('sys_admin') . "\n";
echo "Display name for 'admin': " . RoleManager::getDisplayName('admin') . "\n";
echo "Badge class for 'support': " . RoleManager::getRoleBadgeClass('support') . "\n";
echo "Role ID for 'instructor': " . RoleManager::getRoleId('instructor') . "\n";
echo "Role name for ID 3: " . RoleManager::getRoleName(3) . "\n";

echo "\nTesting database roles:\n";
$roles = App\Models\Role::all();
foreach ($roles as $role) {
    $displayName = RoleManager::getDisplayName($role->name);
    $badgeClass = RoleManager::getRoleBadgeClass($role->name);
    echo "ID: {$role->id}, Name: {$role->name}, Display: {$displayName}, Badge: {$badgeClass}\n";
}

echo "\nAll tests completed successfully!\n";

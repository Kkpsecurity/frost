<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Support\RoleManager;

echo "=== TESTING ROLEMANAGER ===\n";

echo "\nAdmin Role IDs: " . json_encode(RoleManager::getAdminRoleIds()) . "\n";
echo "Admin Role Names: " . json_encode(RoleManager::getAdminRoleNames()) . "\n";
echo "Role Display Map: " . json_encode(RoleManager::getRoleDisplayMap()) . "\n";
echo "Admin Role Options: " . json_encode(RoleManager::getAdminRoleOptions()) . "\n";

echo "\nTesting role utilities:\n";
echo "Display name for 'sys_admin': " . RoleManager::getDisplayName('sys_admin') . "\n";
echo "Badge class for 'admin': " . RoleManager::getRoleBadgeClass('admin') . "\n";
echo "Role ID for 'instructor': " . RoleManager::getRoleId('instructor') . "\n";
echo "Role name for ID 1: " . RoleManager::getRoleName(1) . "\n";

echo "\nTesting privilege hierarchy:\n";
echo "Sys Admin (1) has higher privileges than Admin (2): " . (RoleManager::hasHigherOrEqualPrivileges(1, 2) ? 'Yes' : 'No') . "\n";
echo "Support (4) has higher privileges than Admin (2): " . (RoleManager::hasHigherOrEqualPrivileges(4, 2) ? 'Yes' : 'No') . "\n";

echo "\n=== TESTING ADMIN MODEL WITH ROLEMANAGER ===\n";
$admins = App\Models\Admin::with('Role')->limit(3)->get();
foreach ($admins as $admin) {
    echo "Admin: {$admin->fname} {$admin->lname}\n";
    echo "- Role ID: {$admin->role_id}\n";
    echo "- Role Name: " . ($admin->Role->name ?? 'N/A') . "\n";
    echo "- Display Name: {$admin->role_display_name}\n";
    echo "- Badge Class: {$admin->role_badge_class}\n";
    echo "- Has 'admin' role: " . ($admin->hasRoleName('admin') ? 'Yes' : 'No') . "\n";
    echo "---\n";
}

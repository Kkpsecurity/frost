<?php

// Test final role structure corrections
require_once 'vendor/autoload.php';
require_once 'app/Support/RoleManager.php';

use App\Support\RoleManager;

echo "=== Final Role Structure Test ===\n\n";

echo "Role Constants:\n";
echo "SYS_ADMIN_ID = " . RoleManager::SYS_ADMIN_ID . " (" . RoleManager::SYS_ADMIN_NAME . " -> " . RoleManager::SYS_ADMIN_DISPLAY . ")\n";
echo "ADMIN_ID = " . RoleManager::ADMIN_ID . " (" . RoleManager::ADMIN_NAME . " -> " . RoleManager::ADMIN_DISPLAY . ")\n";
echo "INSTRUCTOR_ID = " . RoleManager::INSTRUCTOR_ID . " (" . RoleManager::INSTRUCTOR_NAME . " -> " . RoleManager::INSTRUCTOR_DISPLAY . ")\n";
echo "SUPPORT_ID = " . RoleManager::SUPPORT_ID . " (" . RoleManager::SUPPORT_NAME . " -> " . RoleManager::SUPPORT_DISPLAY . ")\n";
echo "STUDENT_ID = " . RoleManager::STUDENT_ID . " (" . RoleManager::STUDENT_NAME . " -> " . RoleManager::STUDENT_DISPLAY . ")\n";
echo "GUEST_ID = " . RoleManager::GUEST_ID . " (" . RoleManager::GUEST_NAME . " -> " . RoleManager::GUEST_DISPLAY . ")\n\n";

echo "Admin Role IDs: " . implode(', ', RoleManager::getAdminRoleIds()) . "\n";
echo "Admin Role Names: " . implode(', ', RoleManager::getAdminRoleNames()) . "\n\n";

echo "Role Hierarchy:\n";
foreach (RoleManager::getRoleHierarchy() as $roleId => $level) {
    $roleName = RoleManager::getRoleName($roleId);
    $displayName = RoleManager::getDisplayName($roleName);
    echo "Level $level: Role ID $roleId ($roleName -> $displayName)\n";
}

echo "\nAdmin Role Options for Dropdown:\n";
foreach (RoleManager::getAdminRoleOptions() as $value => $label) {
    echo "'$value' => '$label'\n";
}

echo "\nCreation Role Options:\n";
foreach (RoleManager::getCreationRoleOptions() as $value => $label) {
    echo "'$value' => '$label'\n";
}

echo "\nRole ID <-> Name Mapping Test:\n";
$testRoles = ['sys_admin', 'admin', 'instructor', 'support', 'student', 'guest'];
foreach ($testRoles as $roleName) {
    $roleId = RoleManager::getRoleId($roleName);
    $retrievedName = RoleManager::getRoleName($roleId);
    $displayName = RoleManager::getDisplayName($roleName);
    echo "$roleName -> ID $roleId -> $retrievedName (" . ($roleName === $retrievedName ? 'MATCH' : 'ERROR') . ") Display: $displayName\n";
}

echo "\nMax Admin Role ID: " . RoleManager::getMaxAdminRoleId() . "\n";
echo "Default Admin Role ID: " . RoleManager::getDefaultAdminRoleId() . "\n";

echo "\n=== Test Complete ===\n";

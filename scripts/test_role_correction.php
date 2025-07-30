<?php

// Test corrected role structure: Support=3, Instructor=4
require_once 'vendor/autoload.php';
require_once 'app/Support/RoleManager.php';

use App\Support\RoleManager;

echo "=== Corrected Role Structure Test ===\n\n";

echo "Role Constants (CORRECTED):\n";
echo "SYS_ADMIN_ID = " . RoleManager::SYS_ADMIN_ID . " (" . RoleManager::SYS_ADMIN_NAME . " -> " . RoleManager::SYS_ADMIN_DISPLAY . ")\n";
echo "ADMIN_ID = " . RoleManager::ADMIN_ID . " (" . RoleManager::ADMIN_NAME . " -> " . RoleManager::ADMIN_DISPLAY . ")\n";
echo "SUPPORT_ID = " . RoleManager::SUPPORT_ID . " (" . RoleManager::SUPPORT_NAME . " -> " . RoleManager::SUPPORT_DISPLAY . ")\n";
echo "INSTRUCTOR_ID = " . RoleManager::INSTRUCTOR_ID . " (" . RoleManager::INSTRUCTOR_NAME . " -> " . RoleManager::INSTRUCTOR_DISPLAY . ")\n";
echo "STUDENT_ID = " . RoleManager::STUDENT_ID . " (" . RoleManager::STUDENT_NAME . " -> " . RoleManager::STUDENT_DISPLAY . ")\n";
echo "GUEST_ID = " . RoleManager::GUEST_ID . " (" . RoleManager::GUEST_NAME . " -> " . RoleManager::GUEST_DISPLAY . ")\n\n";

echo "Expected vs Actual:\n";
echo "Support should be 3: " . RoleManager::SUPPORT_ID . " (" . (RoleManager::SUPPORT_ID === 3 ? 'CORRECT' : 'ERROR') . ")\n";
echo "Instructor should be 4: " . RoleManager::INSTRUCTOR_ID . " (" . (RoleManager::INSTRUCTOR_ID === 4 ? 'CORRECT' : 'ERROR') . ")\n\n";

echo "Admin Role IDs: " . implode(', ', RoleManager::getAdminRoleIds()) . "\n";
echo "Max Admin Role ID: " . RoleManager::getMaxAdminRoleId() . " (should be 4)\n\n";

echo "Role Creation Options:\n";
foreach (RoleManager::getCreationRoleOptions() as $value => $label) {
    echo "ID $value => '$label'\n";
}

echo "\n=== Test Complete ===\n";

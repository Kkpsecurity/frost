<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Support\RoleManager;

// Check if user is authenticated
if (!Auth::check()) {
    echo "❌ User is not authenticated\n";
    exit(1);
}

$user = Auth::user();

echo "=== USER ROLE DEBUG INFO ===\n";
echo "User ID: " . $user->id . "\n";
echo "User Email: " . $user->email . "\n";
echo "Role ID: " . $user->role_id . "\n";
echo "Role ID Type: " . gettype($user->role_id) . "\n";
echo "\n";

echo "=== ROLEMANAGER CONSTANTS ===\n";
echo "SYS_ADMIN_ID: " . RoleManager::SYS_ADMIN_ID . " (type: " . gettype(RoleManager::SYS_ADMIN_ID) . ")\n";
echo "ADMIN_ID: " . RoleManager::ADMIN_ID . " (type: " . gettype(RoleManager::ADMIN_ID) . ")\n";
echo "\n";

echo "=== ROLE CHECKS ===\n";
echo "IsSysAdmin(): " . ($user->IsSysAdmin() ? 'true' : 'false') . "\n";
echo "IsAdministrator(): " . ($user->IsAdministrator() ? 'true' : 'false') . "\n";
echo "IsAnyAdmin(): " . ($user->IsAnyAdmin() ? 'true' : 'false') . "\n";
echo "\n";

echo "=== DIRECT COMPARISON ===\n";
echo "role_id == SYS_ADMIN_ID: " . ($user->role_id == RoleManager::SYS_ADMIN_ID ? 'true' : 'false') . "\n";
echo "role_id === SYS_ADMIN_ID: " . ($user->role_id === RoleManager::SYS_ADMIN_ID ? 'true' : 'false') . "\n";
echo "\n";

echo "=== ROLEMANAGER METHODS ===\n";
echo "isAdminRole(\$user->role_id): " . (RoleManager::isAdminRole($user->role_id) ? 'true' : 'false') . "\n";
echo "getRoleName(\$user->role_id): " . RoleManager::getRoleName($user->role_id) . "\n";
echo "getDisplayName(getRoleName): " . RoleManager::getDisplayName(RoleManager::getRoleName($user->role_id)) . "\n";

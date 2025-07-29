# RoleManager Support Class

The `RoleManager` class is a centralized utility for managing user roles and their properties throughout the application. It provides constants, methods, and utilities for handling user roles consistently.

## Features

### Role Constants
- **Role IDs**: `SYS_ADMIN_ID`, `ADMIN_ID`, `INSTRUCTOR_ID`, `SUPPORT_ID`, `STUDENT_ID`
- **Role Names**: `SYS_ADMIN_NAME`, `ADMIN_NAME`, `INSTRUCTOR_NAME`, `SUPPORT_NAME`, `STUDENT_NAME`
- **Display Names**: `SYS_ADMIN_DISPLAY`, `ADMIN_DISPLAY`, `INSTRUCTOR_DISPLAY`, `SUPPORT_DISPLAY`, `STUDENT_DISPLAY`

### Key Methods

#### Role Collection Methods
- `getAdminRoleIds()` - Returns array of admin role IDs [1,2,3,4]
- `getAdminRoleNames()` - Returns array of admin role names ['sys_admin','admin','instructor','support']
- `getAdminRoleOptions()` - Returns dropdown options for admin roles with display names

#### Role Utility Methods
- `getDisplayName(string $roleName)` - Get user-friendly display name for a role
- `getRoleId(string $roleName)` - Get role ID by role name
- `getRoleName(int $roleId)` - Get role name by role ID
- `isAdminRole(int $roleId)` - Check if role ID is an admin role
- `isAdminRoleName(string $roleName)` - Check if role name is an admin role

#### UI Helper Methods
- `getRoleBadgeClass(string $roleName)` - Get Bootstrap badge class for role display
- `getRolePermissions(string $roleName)` - Get permissions array for a role (extensible)

#### Privilege Methods
- `hasHigherOrEqualPrivileges(int $roleIdA, int $roleIdB)` - Compare role privileges
- `getRoleHierarchy()` - Get role hierarchy levels (lower number = higher privilege)

## Usage Examples

### In Controllers
```php
use App\Support\RoleManager;

// Get admin roles for dropdowns
$roles = Role::whereIn('id', RoleManager::getAdminRoleIds())->get();

// Check if user has admin privileges
if (RoleManager::isAdminRole($user->role_id)) {
    // Admin access granted
}
```

### In Blade Templates
```php
// Using the role filter component
<x-role-filter />

// Manual dropdown
@foreach(RoleManager::getAdminRoleOptions() as $value => $label)
    <option value="{{ $value }}">{{ $label }}</option>
@endforeach
```

### In Models
```php
// Admin model uses RoleManager for scoping
static::addGlobalScope('admin', function (Builder $builder) {
    $builder->whereIn('role_id', RoleManager::getAdminRoleIds());
});

// Get role display information
$admin->role_display_name; // Uses RoleManager::getDisplayName()
$admin->role_badge_class;  // Uses RoleManager::getRoleBadgeClass()
```

### DataTables Integration
```php
->addColumn('role_badge', function ($admin) {
    $roleName = $admin->Role->name ?? null;
    if ($roleName) {
        $badgeClass = RoleManager::getRoleBadgeClass($roleName);
        $displayName = RoleManager::getDisplayName($roleName);
        return '<span class="badge ' . $badgeClass . '">' . $displayName . '</span>';
    }
    return '<span class="badge badge-light">N/A</span>';
})
```

## Role Hierarchy

The system uses a numerical hierarchy where lower numbers represent higher privileges:

1. **System Admin** (role_id: 1) - Highest privileges, system management
2. **Admin** (role_id: 2) - User management, content management
3. **Instructor** (role_id: 3) - Course management, student management
4. **Support** (role_id: 4) - Support tickets, user assistance
5. **Student** (role_id: 5) - Basic user access (not included in admin scope)

## Badge Classes

The system uses Bootstrap badge classes for visual role representation:

- **System Admin**: `badge-danger` (Red)
- **Admin**: `badge-primary` (Blue)
- **Instructor**: `badge-info` (Light Blue)
- **Support**: `badge-warning` (Yellow)
- **Student**: `badge-secondary` (Gray)

## Benefits

1. **Centralized Management**: All role-related logic is in one place
2. **Type Safety**: Constants prevent typos in role names/IDs
3. **Consistency**: Uniform role handling across the application
4. **Maintainability**: Easy to modify role structure or add new roles
5. **Extensibility**: Easy to add new role-related functionality
6. **UI Consistency**: Standardized display names and badge classes

## Integration Points

- **Admin Model**: Uses RoleManager for scoping and attribute accessors
- **AdminUserController**: Uses RoleManager for role filtering and display
- **Blade Components**: Role filter component uses RoleManager options
- **DataTables**: Role badges and display names use RoleManager
- **Form Creation**: Role options for user creation forms

This centralized approach ensures consistent role management throughout the application while providing flexibility for future enhancements.

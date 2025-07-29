# Role Model

**File:** `app/Models/Role.php`  
**Table:** `roles`  
**Primary Key:** `id`  
**Timestamps:** No

## Overview

The Role model represents user roles in the Frost Learning Management System. It's a static reference model that defines different user types and permissions. The model uses the `StaticModel` trait, indicating it contains relatively unchanging reference data.

## Attributes

### Database Fields

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| `id` | integer | No | Primary key (manually assigned) |
| `name` | varchar(16) | No | Role name/identifier |

### Mass Assignment
**Fillable:** `[]` (empty - static model, no mass assignment)

**Note:** This table does not have an auto-incrementing ID sequence. IDs are manually assigned to maintain consistency across environments.

## Type Casting

```php
protected $casts = [
    'id' => 'integer',
    'name' => 'string',  // Max 16 characters
];
```

## Traits Used

- `StaticModel` - Custom trait for read-only reference data models

## Relationships

### Has Many
- **Users()** → `User` - Users assigned to this role

## Methods

### Magic Methods

#### `__toString(): string`
Returns the role name when the model is cast to string.

```php
$role = Role::find(5);
echo $role; // Outputs: "Student" (or whatever the role name is)
```

## Role Structure

Based on typical role systems and the default user role_id of 5, the roles table likely contains:

| ID | Name | Description |
|----|------|-------------|
| 1 | Admin | System administrators |
| 2 | Instructor | Course instructors |
| 3 | Manager | Content managers |
| 4 | Support | Support staff |
| 5 | Student | Default student role |

*Note: Actual role structure may vary based on business requirements.*

## Usage Examples

### Retrieving Roles
```php
// Get all roles
$roles = Role::all();

// Get specific role
$studentRole = Role::find(5);
echo $studentRole->name; // "Student"

// Get role by name
$adminRole = Role::where('name', 'Admin')->first();
```

### Working with Users
```php
// Get all users with a specific role
$students = Role::find(5)->Users;

// Count users by role
$studentCount = Role::find(5)->Users->count();

// Check user's role
$user = User::find(1);
$roleName = $user->role->name;
```

### Role-based Logic
```php
// Check if user is admin
$user = User::find(1);
$isAdmin = $user->role_id === 1;

// Get role name for display
$user = User::find(1);
$displayRole = (string) $user->role; // Uses __toString()
```

## Static Model Characteristics

As a `StaticModel`, this model:
- Contains reference data that rarely changes
- Has empty `$fillable` array to prevent mass assignment
- Typically populated via database seeders
- IDs are manually assigned for consistency
- No timestamps to reduce overhead

## Database Schema

```sql
CREATE TABLE roles (
    id INTEGER PRIMARY KEY,  -- Manually assigned, no auto-increment
    name VARCHAR(16) NOT NULL UNIQUE
);
```

## Seeding Data

Roles are typically seeded during database setup:

```php
// In DatabaseSeeder or RoleSeeder
Role::insert([
    ['id' => 1, 'name' => 'Admin'],
    ['id' => 2, 'name' => 'Instructor'],
    ['id' => 3, 'name' => 'Manager'],
    ['id' => 4, 'name' => 'Support'],
    ['id' => 5, 'name' => 'Student'],
]);
```

## Security Considerations

- Static nature prevents accidental role modification
- Consistent IDs across environments ensure proper access control
- Role-based access control (RBAC) foundation

## Performance Considerations

- Small, static dataset ideal for caching
- No timestamps reduce storage overhead
- Integer IDs for efficient joins and comparisons
- Can be cached in application memory due to static nature

## Integration Points

### User Registration
New users default to role_id 5 (Student) as defined in the User model:

```php
// In User model
protected $attributes = [
    'role_id' => 5, // Default: student
];
```

### Authorization
Roles integrate with Laravel's authorization system:

```php
// In policies or middleware
if ($user->role_id === 1) {
    // Admin access
}
```

## Related Models

- [User](User.md) - Users assigned to roles
- [Admin](Admin.md) - Administrative user extensions

---

*Last updated: July 28, 2025*

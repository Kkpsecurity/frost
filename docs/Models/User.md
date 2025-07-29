# User Model

**File:** `app/Models/User.php`  
**Table:** `users`  
**Primary Key:** `id`  
**Timestamps:** Yes

## Overview

The User model represents the core user entity in the Frost Learning Management System. It extends Laravel's `Authenticatable` class and handles user authentication, profile management, and relationships with other system components.

## Attributes

### Database Fields

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| `id` | bigint | No | Auto | Primary key |
| `is_active` | boolean | No | true | User account status |
| `role_id` | smallint | No | 5 | Foreign key to roles table (default: student) |
| `lname` | varchar(255) | No | - | Last name |
| `fname` | varchar(255) | No | - | First name |
| `email` | varchar(255) | No | - | Email address (unique) |
| `password` | varchar(100) | Yes | - | Hashed password |
| `remember_token` | varchar(100) | Yes | - | Laravel remember token |
| `created_at` | timestamptz | No | Current | Record creation time |
| `updated_at` | timestamptz | No | Current | Last update time |
| `email_verified_at` | timestamptz | Yes | - | Email verification timestamp |
| `avatar` | varchar(255) | Yes | - | Avatar file path |
| `use_gravatar` | boolean | No | false | Use Gravatar for avatar |
| `student_info` | json | Yes | - | Additional student information |
| `email_opt_in` | boolean | No | false | Email marketing consent |

### Mass Assignment

**Fillable:**
- `is_active`, `role_id`, `lname`, `fname`, `email`, `password`
- `avatar`, `use_gravatar`, `student_info`, `email_opt_in`, `email_verified_at`

**Guarded:**
- `id`, `is_active`, `role_id`, `zoom_creds_id`

**Hidden (from serialization):**
- `password`, `remember_token`

## Type Casting

```php
protected $casts = [
    'id' => 'integer',
    'is_active' => 'boolean',
    'role_id' => 'integer',
    'lname' => 'string',
    'fname' => 'string',
    'email' => 'string',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'email_verified_at' => 'datetime',
    'password' => 'string',
    'remember_token' => 'string',
    'avatar' => 'string',
    'use_gravatar' => 'boolean',
    'student_info' => 'array', // JSON to array
    'email_opt_in' => 'boolean',
];
```

## Traits Used

- `HasApiTokens` - Laravel Sanctum API authentication
- `HasFactory` - Model factory support
- `Notifiable` - Laravel notification system

## Relationships

### Has Many
- **preferences()** → `UserPref` - User preference settings

### Belongs To
- **role()** → `Role` - User role assignment

## Methods

### Preference Management

#### `getPreference(string $prefName, string $default = null): ?string`
Retrieves a specific user preference value.

**Parameters:**
- `$prefName` - The preference key to retrieve
- `$default` - Default value if preference not found

**Returns:** The preference value or default

#### `setPreference(string $prefName, string $prefValue): UserPref`
Sets or updates a user preference.

**Parameters:**
- `$prefName` - The preference key
- `$prefValue` - The preference value

**Returns:** The UserPref model instance

## Default Values

```php
protected $attributes = [
    'is_active' => true,
    'role_id' => 5, // Student role
    'email_opt_in' => false,
];
```

## Security Features

- Password hashing handled by Laravel
- Remember token for "remember me" functionality
- Mass assignment protection via `$fillable` and `$guarded`
- Sensitive fields hidden from JSON serialization

## Usage Examples

### Creating a User
```php
$user = User::create([
    'fname' => 'John',
    'lname' => 'Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password'),
]);
```

### Managing Preferences
```php
// Set a preference
$user->setPreference('theme', 'dark');

// Get a preference
$theme = $user->getPreference('theme', 'light');

// Get all preferences
$preferences = $user->preferences;
```

### Role Checking
```php
$user = User::find(1);
$role = $user->role;
$isStudent = $user->role_id === 5;
```

## Related Models

- [Role](Role.md) - User role definitions
- [UserPref](UserPref.md) - User preference storage
- [UserBrowser](UserBrowser.md) - Browser tracking
- [Course](Course.md) - Course enrollments
- [Order](Order.md) - Purchase history

---

*Last updated: July 28, 2025*

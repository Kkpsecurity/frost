# UserPref Model

**File:** `app/Models/UserPref.php`  
**Table:** `user_prefs`  
**Primary Key:** Composite `['user_id', 'pref_name']`  
**Timestamps:** No

## Overview

The UserPref model manages user preferences in a key-value pair structure. It uses a composite primary key and provides input sanitization for both preference names and values. This model enables flexible per-user configuration storage.

## Attributes

### Database Fields

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| `user_id` | bigint | No | Foreign key to users table |
| `pref_name` | varchar(64) | No | Preference key/name |
| `pref_value` | varchar(255) | No | Preference value |

### Composite Primary Key
The model uses a composite primary key consisting of `['user_id', 'pref_name']`, allowing multiple preferences per user while preventing duplicate preference names for the same user.

### Mass Assignment
**Guarded:** `[]` (empty - all fields are mass assignable)

## Type Casting

```php
protected $casts = [
    'user_id' => 'integer',
    'pref_name' => 'string',   // Max 64 characters
    'pref_value' => 'string',  // Max 255 characters
];
```

## Traits Used

- `HasCompositePrimaryKey` - Custom trait for composite primary key support

## Constants

```php
const ALLOW_HTML_KEY = false;    // Disallow HTML in preference names
const ALLOW_HTML_VALUE = false;  // Disallow HTML in preference values
```

## Relationships

### Belongs To
- **User()** → `User` - The user who owns this preference

## Methods

### Magic Methods

#### `__toString(): string`
Returns the preference value as a string when the model is cast to string.

### Mutators (Input Sanitization)

#### `setPrefNameAttribute($value)`
Sanitizes the `pref_name` attribute before saving to database.
- Uses `TextTk::Sanitize()` with HTML stripping based on `ALLOW_HTML_KEY` constant
- Ensures preference names are clean and safe

#### `setPrefValueAttribute($value)`
Sanitizes the `pref_value` attribute before saving to database.
- Uses `TextTk::Sanitize()` with HTML stripping based on `ALLOW_HTML_VALUE` constant
- Ensures preference values are clean and safe

### Cache Methods

#### `GetUser(): User`
Retrieves the associated user via caching system.
- Uses `RCache::Users()` for optimized user lookup
- Returns cached User model instance

## Security Features

### Input Sanitization
- Automatic HTML filtering on both key and value fields
- Configurable via class constants
- Uses `TextTk::Sanitize()` utility

### Data Integrity
- Composite primary key prevents duplicate preferences per user
- Foreign key constraint ensures valid user references
- No timestamps to prevent unnecessary overhead

## Usage Examples

### Creating Preferences
```php
// Direct creation
UserPref::create([
    'user_id' => 1,
    'pref_name' => 'theme',
    'pref_value' => 'dark'
]);

// Via User model (recommended)
$user = User::find(1);
$user->setPreference('language', 'en');
```

### Retrieving Preferences
```php
// Get specific preference
$pref = UserPref::where('user_id', 1)
    ->where('pref_name', 'theme')
    ->first();

echo $pref; // Outputs: "dark" (via __toString)

// Get all user preferences
$prefs = UserPref::where('user_id', 1)->get();

// Via User model (recommended)
$user = User::find(1);
$theme = $user->getPreference('theme', 'light');
```

### Updating Preferences
```php
// Update or create
UserPref::updateOrCreate(
    ['user_id' => 1, 'pref_name' => 'theme'],
    ['pref_value' => 'light']
);
```

## Database Schema

```sql
CREATE TABLE user_prefs (
    user_id BIGINT NOT NULL,
    pref_name VARCHAR(64) NOT NULL,
    pref_value VARCHAR(255) NOT NULL,
    PRIMARY KEY (user_id, pref_name),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Common Preference Keys

Based on typical usage patterns, common preference keys might include:
- `theme` - UI theme (light/dark)
- `language` - Interface language
- `timezone` - User's timezone
- `notifications` - Notification settings
- `layout` - Dashboard layout preferences

## Performance Considerations

- Composite primary key enables efficient lookups
- No timestamps reduce storage overhead
- Caching via `RCache` improves read performance
- Index on `user_id` for efficient user-based queries

## Related Models

- [User](User.md) - Parent user model
- [SiteConfig](SiteConfig.md) - Global site configuration

---

*Last updated: July 28, 2025*

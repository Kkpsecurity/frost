# Site Configs to Settings Migration

## Overview
This migration moves data from the old `site_configs` table to the new `settings` table, converting the format and structure to use the modern Laravel Settings package.

## Migration Details

### Old Structure (`site_configs`)
```sql
- id (auto-increment)
- cast_to (string: 'text', 'int', 'longtext')
- config_name (string: e.g., 'site_company_name')
- config_value (text: serialized format like '%%SER%%s:32:"Florida Online Security Training";')
```

### New Structure (`settings`) 
```sql
- id (auto-increment)
- key (string: dot notation like 'site.company_name')
- value (text: plain text value)
```

## Data Mapping

### Site Settings
- `site_company_name` → `site.company_name`
- `site_company_address` → `site.company_address`
- `site_contact_email` → `site.contact_email`
- `site_support_email` → `site.support_email`
- `site_support_phone` → `site.support_phone`
- `site_support_phone_hours` → `site.support_phone_hours`
- `site_google_map_url` → `site.google_map_url`

### Class Settings
- `classtimes_starts_soon_seconds` → `class.starts_soon_seconds`
- `classtimes_is_ended_seconds` → `class.is_ended_seconds`
- `classtimes_zoom_duration_minutes` → `class.zoom_duration_minutes`

### Student Settings
- `student_lesson_complete_seconds` → `student.lesson_complete_seconds`
- `student_poll_seconds` → `student.poll_seconds`
- `student_join_lesson_seconds` → `student.join_lesson_seconds`

### Instructor Settings
- `instructor_close_lesson_minutes` → `instructor.close_lesson_minutes`
- `instructor_pre_start_minutes` → `instructor.pre_start_minutes`
- `instructor_post_end_minutes` → `instructor.post_end_minutes`
- `instructor_next_lesson_seconds` → `instructor.next_lesson_seconds`
- `instructor_close_unit_seconds` → `instructor.close_unit_seconds`

### Chat Settings
- `chat_log_last` → `chat.log_last`

## Running the Migration

### Method 1: Using the Custom Command (Recommended)
```bash
# Verify migration without running
php artisan migrate:site-configs --verify

# Run the migration
php artisan migrate:site-configs
```

### Method 2: Using Laravel Migrate
```bash
# Run the specific migration file
php artisan migrate --path=database/migrations/2025_08_07_120000_migrate_site_configs_to_settings.php
```

## Files Created

1. **Migration**: `database/migrations/2025_08_07_120000_migrate_site_configs_to_settings.php`
   - Creates proper site_configs structure
   - Inserts data from your dump
   - Migrates data to settings table
   - Handles serialization parsing

2. **Service**: `app/Services/SiteConfigService.php`
   - Provides methods to access settings by category
   - Type casting for numeric values
   - Clean API for updating settings

3. **Command**: `app/Console/Commands/MigrateSiteConfigsCommand.php`
   - Custom command to run and verify migration
   - Shows before/after comparison
   - Validates data integrity

## Usage After Migration

### Using the Service Class
```php
use App\Services\SiteConfigService;

$siteConfigService = new SiteConfigService();

// Get all site settings
$siteSettings = $siteConfigService->getSiteSettings();
echo $siteSettings['company_name']; // "Florida Online Security Training"

// Get specific category settings
$classSettings = $siteConfigService->getClassSettings();
echo $classSettings['starts_soon_seconds']; // 14400

// Update settings
$siteConfigService->setSiteSetting('company_name', 'New Company Name');
```

### Using the Settings Facade Directly
```php
use Akaunting\Setting\Facade as Setting;

// Get a setting
$companyName = Setting::get('site.company_name', 'Default Company');

// Set a setting
Setting::set('site.company_name', 'New Company Name');
```

### In Blade Templates
```blade
<!-- Using the setting helper -->
<h1>{{ setting('site.company_name', 'Default Company') }}</h1>

<!-- Contact info -->
<p>Email: {{ setting('site.contact_email') }}</p>
<p>Phone: {{ setting('site.support_phone') }}</p>
```

## Rollback Process

If you need to rollback the migration:

```bash
php artisan migrate:rollback --path=database/migrations/2025_08_07_120000_migrate_site_configs_to_settings.php
```

This will:
- Remove migrated settings from the settings table
- Restore the original empty site_configs structure

## Verification

After migration, verify the data:

1. Check settings count: `SELECT COUNT(*) FROM settings WHERE key LIKE 'site.%' OR key LIKE 'class.%'`
2. Test a few specific values
3. Use the verification command: `php artisan migrate:site-configs --verify`

## Notes

- The migration preserves all original data while converting formats
- Serialized values are properly parsed and stored as plain text
- Dot notation provides better organization and Laravel compatibility
- The old site_configs table is preserved for reference
- All numeric values are properly type-cast when accessed through the service

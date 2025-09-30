# Laravel Development Scripts

This directory contains helpful scripts for Laravel development and maintenance, organized into logical categories.

## 📁 Directory Structure

Scripts are now organized into the following categories:

- **`analysis/`** - System analysis and investigation tools
- **`database/`** - Database management and synchronization
- **`deployment/`** - Deployment and environment setup
- **`testing/`** - Testing and validation scripts  
- **`debugging/`** - Debugging and diagnostic tools
- **`maintenance/`** - System maintenance and cleanup
- **`course-management/`** - Course-specific administration
- **`config/`** - Configuration management

## 📖 Complete Inventory

See **[SCRIPTS_INVENTORY.md](SCRIPTS_INVENTORY.md)** for a comprehensive list of all available scripts organized by category.

## 🚀 Quick Start

### 🧹 Cache Clearing Scripts (in `maintenance/`)

#### `maintenance/clear-cache.ps1` (PowerShell)
Comprehensive PowerShell script that clears all Laravel caches and recreates optimized caches.

**Usage:**
```powershell
.\scripts\maintenance\clear-cache.ps1
```

**Features:**
- Clears all major Laravel caches
- Recreates optimized caches
- Colored output with progress indicators
- Error handling for each operation

#### `maintenance/clear-cache.bat` (Batch)
Simple batch file version for Windows Command Prompt.

**Usage:**
```cmd
scripts\maintenance\clear-cache.bat
```

**Features:**
- Clears all major Laravel caches
- Recreates optimized caches
- Basic error checking
- Works in Command Prompt

## 🎯 Common Use Cases

### During Development
- **Cache Issues:** Use `maintenance/clear-cache.*`
- **Route Problems:** Use `debugging/checkRoutes.*`
- **Configuration Changes:** Use `config/check_*.php`
- **Testing Features:** Use `testing/test_*.php`

### Database Operations
- **Data Sync:** Use `database/sync-*.sh`
- **Backup/Copy:** Use `database/copy_database.*`
- **Validation:** Use `database/validate-*.php`

### Course Management
- **Daily Setup:** Use `course-management/create_today_class*.php`
- **Date Management:** Use `course-management/add_course_dates.php`
- **Issue Fixes:** Use `course-management/fix_course_*.php`

### System Analysis
- **Investigation:** Use `analysis/analyze_*.php`
- **Debugging:** Use `debugging/debug_*.php`
- **Performance:** Use `testing/test_*.php`

## Laravel Artisan Commands Reference

These scripts use the following Laravel artisan commands:

| Command | Purpose |
|---------|---------|
| `cache:clear` | Clear application cache |
| `config:clear` | Clear configuration cache |
| `config:cache` | Cache configuration files |
| `route:clear` | Clear route cache |
| `route:cache` | Cache routes for faster routing |
| `view:clear` | Clear compiled view files |
| `clear-compiled` | Remove compiled class file |
| `event:clear` | Clear cached events (Laravel 8+) |
| `schedule:clear-cache` | Clear schedule cache (Laravel 8+) |

## Notes

- Always run these scripts from the Laravel project root directory
- The PowerShell script provides more detailed output and error handling
- Both scripts will recreate necessary caches for optimal performance
- Use these scripts whenever you make changes that might be cached

## Execution Policy (PowerShell)

If you get an execution policy error with PowerShell, run this command as Administrator:

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

## 📝 Adding New Scripts

When adding new scripts:

1. **Choose the right category** (or create a new one if needed)
2. **Follow naming conventions:** `action_component.php` (e.g., `test_dashboard.php`, `fix_courses.php`)
3. **Add documentation** in the script header
4. **Update SCRIPTS_INVENTORY.md** when adding new categories
5. **Test thoroughly** before committing

## 🔗 Related Documentation

- [Complete Scripts Inventory](SCRIPTS_INVENTORY.md) - Detailed list of all scripts
- [Project Documentation](../docs/) - Overall project documentation
- [Laravel Artisan Commands](https://laravel.com/docs/artisan) - Laravel CLI reference

## 🤝 Contributing

Feel free to add more development scripts to this directory as needed for the project. Follow the organization structure and naming conventions outlined above.

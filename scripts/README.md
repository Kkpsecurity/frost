# Laravel Development Scripts

This directory contains helpful scripts for Laravel development and maintenance.

## Available Scripts

### 🧹 Cache Clearing Scripts

#### `clear-cache.ps1` (PowerShell)
Comprehensive PowerShell script that clears all Laravel caches and recreates optimized caches.

**Usage:**
```powershell
.\scripts\clear-cache.ps1
```

**Features:**
- Clears application cache
- Clears configuration cache
- Clears route cache
- Clears view cache
- Clears compiled services
- Clears event cache (Laravel 8+)
- Clears schedule cache (Laravel 8+)
- Recreates optimized caches
- Colored output with progress indicators
- Error handling for each operation

#### `clear-cache.bat` (Batch)
Simple batch file version for Windows Command Prompt.

**Usage:**
```cmd
scripts\clear-cache.bat
```

**Features:**
- Clears all major Laravel caches
- Recreates optimized caches
- Basic error checking
- Works in Command Prompt

## When to Use These Scripts

### During Development
- After changing configuration files
- After modifying routes
- When views aren't updating
- After installing new packages
- When testing new features

### For RingCentral Privacy Policy Implementation
- After updating privacy policy content
- Before testing form submissions
- After modifying routes or controllers
- When testing the privacy policy page access

### Troubleshooting
- When experiencing caching issues
- When routes aren't working as expected
- When configuration changes aren't taking effect
- When views show old content

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

## Contributing

Feel free to add more development scripts to this directory as needed for the project.

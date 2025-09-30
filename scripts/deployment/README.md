# Deployment Scripts

Automated deployment scripts for the Frost Laravel application with Git hooks integration.

## 🎯 Auto-Deployment Setup

### Quick Setup
```bash
# Run the setup script to install Git hooks
.\scripts\deployment\setup-git-hooks.bat
```

### Manual Setup
```powershell
# Install Git hooks for auto-deployment
.\scripts\deployment\setup-git-hooks.ps1 -Install

# Test the configuration
.\scripts\deployment\setup-git-hooks.ps1 -Test

# Uninstall if needed
.\scripts\deployment\setup-git-hooks.ps1 -Uninstall
```

## 🚀 How Auto-Deployment Works

### Trigger
- **Automatic:** Deploys after every commit
- **Target:** `\\atlas\webroot\frost-staging`
- **Branches:** main, master, copilot/*

### Skip Deployment
Add `[skip deploy]` or `[no deploy]` to your commit message:
```bash
git commit -m "Work in progress [skip deploy]"
git commit -m "Documentation update [no deploy]"
```

### Deployment Process
1. **Backup Creation** - Backs up existing staging deployment
2. **File Synchronization** - Copies files with exclusions
3. **Post-Deployment Tasks** - Clears Laravel caches
4. **Logging** - Records all activities in `deployment-staging.log`

## 📁 Available Scripts

### Core Deployment Scripts
- `deploy-to-staging.ps1` - Main staging deployment script
- `setup-git-hooks.ps1` - Git hooks configuration
- `setup-git-hooks.bat` - Quick setup batch file

### Legacy Scripts
- `deploy-simple.ps1` - Simple deployment utility
- `deploy-to-laragon.ps1` - Local Laragon deployment
- `deploy-student-activity-tracking.*` - Feature-specific deployment

## 🔧 Manual Deployment

### Deploy to Staging
```powershell
# Interactive deployment
.\scripts\deployment\deploy-to-staging.ps1

# Auto-confirm deployment
.\scripts\deployment\deploy-to-staging.ps1 -AutoConfirm

# Verbose output
.\scripts\deployment\deploy-to-staging.ps1 -Verbose

# Skip backup (not recommended)
.\scripts\deployment\deploy-to-staging.ps1 -SkipBackup
```

### Deployment Options
- `-Environment` - Target environment (default: staging)
- `-AutoConfirm` - Skip confirmation prompt
- `-Verbose` - Detailed output
- `-SkipTests` - Skip test execution
- `-SkipBackup` - Skip backup creation

## 📋 File Exclusions

The deployment automatically excludes:

### Directories
- `.git` - Git repository data
- `node_modules` - Node.js dependencies  
- `vendor` - Composer dependencies (may need manual install)
- `docs/` - Documentation (development only)
- `scripts/` - Development scripts and tools
- `.vscode` - VS Code settings
- `storage/logs` - Log files
- `storage/framework/cache` - Cache files
- `storage/framework/sessions` - Session files

### Files
- `.env*` - Environment configuration
- `*.log` - Log files
- `deployment*.log` - Deployment logs
- `.phpunit.result.cache` - PHPUnit cache

## 🗄️ Backup System

### Automatic Backups
- **Location:** `\\atlas\webroot\frost-staging-backups`
- **Naming:** `frost-staging-backup-YYYYMMDD-HHMMSS`
- **Retention:** Managed manually (clean up old backups periodically)

### Manual Backup
```powershell
# Create backup before deployment
.\scripts\deployment\deploy-to-staging.ps1 -Verbose
```

## 📊 Monitoring & Logs

### Log Files
- `deployment-staging.log` - Staging deployment activities
- `deployment.log` - General deployment activities

### Log Format
```
[2025-09-30 14:30:25] [INFO] Starting staging deployment process
[2025-09-30 14:30:26] [GIT] Git Branch: main
[2025-09-30 14:30:26] [GIT] Git Commit: abc1234
[2025-09-30 14:30:27] [BACKUP] Creating backup: frost-staging-backup-20250930-143027
[2025-09-30 14:30:35] [DEPLOY] Files deployed successfully
[2025-09-30 14:30:40] [POST-DEPLOY] ✅ php artisan cache:clear
[2025-09-30 14:30:41] [SUCCESS] Staging deployment completed successfully
```

## 🔒 Security & Best Practices

### Access Control
- Ensure proper permissions for `\\atlas\webroot\frost-staging`
- Use service accounts for automated deployments
- Regularly review deployment logs

### Environment Variables
- Never deploy `.env` files
- Set staging-specific environment variables on the server
- Use separate database credentials for staging

### Testing
```powershell
# Test deployment access
.\scripts\deployment\setup-git-hooks.ps1 -Test

# Test deployment without committing
.\scripts\deployment\deploy-to-staging.ps1 -Verbose
```

## 🚨 Troubleshooting

### Common Issues

#### "Cannot access staging server"
- Check network connectivity to `\\atlas\webroot\frost-staging`
- Verify permissions for the target directory
- Ensure the network path is accessible

#### "Deployment failed with exit code: X"
- Check `deployment-staging.log` for detailed error messages
- Verify target directory has write permissions
- Check available disk space on staging server

#### "PowerShell not found"
- Install PowerShell or PowerShell Core
- Update system PATH if necessary
- Use absolute path to PowerShell executable

### Debug Mode
```powershell
# Run with verbose output
.\scripts\deployment\deploy-to-staging.ps1 -Verbose

# Test Git hooks configuration
.\scripts\deployment\setup-git-hooks.ps1 -Test
```

## ⚙️ Configuration

Edit `deploy-config.conf` to customize:
- Deployment paths
- Backup settings
- File exclusions
- Post-deployment tasks

### Key Settings
```ini
STAGING_PATH="\\atlas\webroot\frost-staging"
AUTO_DEPLOY_BRANCHES=("main" "master" "copilot/*")
SKIP_DEPLOY_KEYWORDS=("[skip deploy]" "[no deploy]")
AUTO_BACKUP_ON_DEPLOY=true
```

## 🎯 Next Steps

1. **Install Git Hooks:** Run `setup-git-hooks.bat`
2. **Test Deployment:** Make a test commit
3. **Verify Staging:** Check `\\atlas\webroot\frost-staging`
4. **Monitor Logs:** Review deployment logs
5. **Configure Environment:** Set up staging `.env` file

---
*Category: Deployment | Last Updated: September 30, 2025*

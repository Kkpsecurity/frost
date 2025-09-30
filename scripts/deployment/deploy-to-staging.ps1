# Deploy to Staging Server Script
# Deploys the current Laravel application to staging server
# Target: \\atlas\webroot\frost-staging

param(
    [string]$Environment = "staging",
    [switch]$AutoConfirm = $false,
    [switch]$Verbose = $false,
    [switch]$SkipTests = $false,
    [switch]$SkipBackup = $false
)

# Configuration
$RepoRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
$StagingPath = "\\atlas\webroot\frost-staging"
$BackupPath = "\\atlas\webroot\frost-staging-backups"
$LogFile = Join-Path $RepoRoot "deployment-staging.log"

# Colors for output
$Colors = @{
    Info = "Blue"
    Success = "Green"
    Warning = "Yellow"
    Error = "Red"
    Highlight = "Cyan"
}

# Function to write colored output and log
function Write-DeployLog {
    param(
        [string]$Message,
        [string]$Color = "White",
        [string]$Prefix = "INFO"
    )

    $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $LogEntry = "[$Timestamp] [$Prefix] $Message"

    Write-Host $LogEntry -ForegroundColor $Color
    Add-Content -Path $LogFile -Value $LogEntry
}

# Function to create backup
function New-StagingBackup {
    if ($SkipBackup) {
        Write-DeployLog "Backup skipped by user request" -Color $Colors.Warning -Prefix "BACKUP"
        return $true
    }

    if (-not (Test-Path $StagingPath)) {
        Write-DeployLog "No existing staging deployment to backup" -Color $Colors.Info -Prefix "BACKUP"
        return $true
    }

    $BackupName = "frost-staging-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"
    $BackupFullPath = Join-Path $BackupPath $BackupName

    try {
        Write-DeployLog "Creating backup: $BackupName" -Color $Colors.Info -Prefix "BACKUP"

        if (-not (Test-Path $BackupPath)) {
            New-Item -ItemType Directory -Path $BackupPath -Force | Out-Null
        }

        # Use robocopy for efficient copying (excluding vendor and node_modules)
        $RobocopyArgs = @($StagingPath, $BackupFullPath, "/E", "/XD", ".git", "node_modules", "vendor", "/R:3", "/W:10", "/NFL", "/NDL")
        $Result = & robocopy @RobocopyArgs

        if ($LASTEXITCODE -le 1) {
            Write-DeployLog "Backup created successfully: $BackupFullPath" -Color $Colors.Success -Prefix "BACKUP"
            return $true
        }
        else {
            Write-DeployLog "Backup failed with exit code: $LASTEXITCODE" -Color $Colors.Error -Prefix "BACKUP"
            return $false
        }
    }
    catch {
        Write-DeployLog "Backup failed: $($_.Exception.Message)" -Color $Colors.Error -Prefix "BACKUP"
        return $false
    }
}

# Function to deploy files
function Deploy-Files {
    try {
        Write-DeployLog "Starting file deployment to staging..." -Color $Colors.Info -Prefix "DEPLOY"

        # Ensure staging directory exists
        if (-not (Test-Path $StagingPath)) {
            New-Item -ItemType Directory -Path $StagingPath -Force | Out-Null
            Write-DeployLog "Created staging directory: $StagingPath" -Color $Colors.Info -Prefix "DEPLOY"
        }

        # Check if rsync is available
        $RsyncAvailable = $false
        try {
            $null = Get-Command rsync -ErrorAction Stop
            $RsyncAvailable = $true
            Write-DeployLog "Using rsync for file synchronization" -Color $Colors.Info -Prefix "DEPLOY"
        }
        catch {
            Write-DeployLog "rsync not found, falling back to robocopy" -Color $Colors.Warning -Prefix "DEPLOY"
        }

        if ($RsyncAvailable) {
            # Use rsync for deployment
            $ExcludePatterns = @(
                ".git/",
                "node_modules/",
                "vendor/",
                ".vscode/",
                "storage/logs/",
                "storage/framework/cache/",
                "storage/framework/sessions/",
                "storage/framework/views/",
                "*.env*",
                "*.log",
                "deployment*.log",
                ".phpunit.result.cache"
            )

            # Build rsync command
            $RsyncArgs = @("-av", "--delete")

            # Add exclusions
            foreach ($Pattern in $ExcludePatterns) {
                $RsyncArgs += "--exclude=$Pattern"
            }

            # Add verbose flag if requested
            if ($Verbose) {
                $RsyncArgs += "-v"
                Write-DeployLog "Rsync command: rsync $($RsyncArgs -join ' ') $RepoRoot/ $StagingPath/" -Color $Colors.Info -Prefix "DEPLOY"
            }
            else {
                $RsyncArgs += "-q"
            }

            # Convert Windows paths for rsync
            $SourcePath = $RepoRoot.Replace('\', '/').Replace('C:', '/c') + '/'
            $TargetPath = $StagingPath.Replace('\', '/').Replace(':', '')

            # Execute rsync
            Write-DeployLog "Synchronizing files with rsync..." -Color $Colors.Info -Prefix "DEPLOY"
            $RsyncArgs += @($SourcePath, $TargetPath)

            & rsync @RsyncArgs
            $RsyncExitCode = $LASTEXITCODE

            if ($RsyncExitCode -eq 0) {
                Write-DeployLog "Files deployed successfully with rsync" -Color $Colors.Success -Prefix "DEPLOY"
                return $true
            }
            else {
                Write-DeployLog "Rsync deployment failed (exit code: $RsyncExitCode)" -Color $Colors.Error -Prefix "DEPLOY"
                return $false
            }
        }
        else {
            # Fallback to robocopy
            $ExcludeDirs = @(".git", "node_modules", "vendor", ".vscode", "storage/logs", "storage/framework/cache", "storage/framework/sessions", "storage/framework/views")
            $ExcludeFiles = @("*.env*", "*.log", "deployment*.log", ".phpunit.result.cache")

            # Build robocopy command
            $RobocopyArgs = @($RepoRoot, $StagingPath, "/E", "/PURGE")

            # Add directory exclusions
            foreach ($Dir in $ExcludeDirs) {
                $RobocopyArgs += "/XD"
                $RobocopyArgs += $Dir
            }

            # Add file exclusions
            foreach ($File in $ExcludeFiles) {
                $RobocopyArgs += "/XF"
                $RobocopyArgs += $File
            }

            # Add other options
            $RobocopyArgs += @("/R:3", "/W:10", "/MT:8")

            if ($Verbose) {
                Write-DeployLog "Robocopy command: robocopy $($RobocopyArgs -join ' ')" -Color $Colors.Info -Prefix "DEPLOY"
            }
            else {
                $RobocopyArgs += @("/NFL", "/NDL")
            }

            # Execute deployment
            Write-DeployLog "Copying files with robocopy..." -Color $Colors.Info -Prefix "DEPLOY"
            $Result = & robocopy @RobocopyArgs

            # Robocopy exit codes: 0-1 are success, 2+ are warnings/errors
            if ($LASTEXITCODE -le 1) {
                Write-DeployLog "Files deployed successfully with robocopy" -Color $Colors.Success -Prefix "DEPLOY"
                return $true
            }
            elseif ($LASTEXITCODE -le 7) {
                Write-DeployLog "Files deployed with warnings (exit code: $LASTEXITCODE)" -Color $Colors.Warning -Prefix "DEPLOY"
                return $true
            }
            else {
                Write-DeployLog "File deployment failed (exit code: $LASTEXITCODE)" -Color $Colors.Error -Prefix "DEPLOY"
                return $false
            }
        }
    }
    catch {
        Write-DeployLog "File deployment failed: $($_.Exception.Message)" -Color $Colors.Error -Prefix "DEPLOY"
        return $false
    }
}

# Function to run post-deployment tasks
function Invoke-PostDeploymentTasks {
    try {
        Write-DeployLog "Running post-deployment tasks..." -Color $Colors.Info -Prefix "POST-DEPLOY"

        Push-Location $StagingPath

        # Check if this is a Laravel application
        if (-not (Test-Path "artisan")) {
            Write-DeployLog "Not a Laravel application - skipping Laravel-specific tasks" -Color $Colors.Warning -Prefix "POST-DEPLOY"
            return $true
        }

        Write-DeployLog "Clearing Laravel caches..." -Color $Colors.Info -Prefix "POST-DEPLOY"

        # Clear caches
        $ArtisanCommands = @(
            "config:clear",
            "route:clear",
            "view:clear",
            "cache:clear"
        )

        foreach ($Command in $ArtisanCommands) {
            try {
                $Output = php artisan $Command 2>&1
                if ($LASTEXITCODE -eq 0) {
                    Write-DeployLog "✅ php artisan $Command" -Color $Colors.Success -Prefix "POST-DEPLOY"
                }
                else {
                    Write-DeployLog "⚠️  php artisan $Command failed: $Output" -Color $Colors.Warning -Prefix "POST-DEPLOY"
                }
            }
            catch {
                Write-DeployLog "⚠️  php artisan $Command failed: $($_.Exception.Message)" -Color $Colors.Warning -Prefix "POST-DEPLOY"
            }
        }

        # Set proper permissions for storage directories
        $StorageDirs = @("storage/logs", "storage/framework/cache", "storage/framework/sessions", "storage/framework/views")
        foreach ($Dir in $StorageDirs) {
            if (Test-Path $Dir) {
                try {
                    icacls $Dir /grant "IIS_IUSRS:(OI)(CI)F" /T /Q 2>$null
                }
                catch {
                    # Permissions setting might fail, but continue
                }
            }
        }

        Write-DeployLog "Post-deployment tasks completed" -Color $Colors.Success -Prefix "POST-DEPLOY"
        return $true
    }
    catch {
        Write-DeployLog "Post-deployment tasks failed: $($_.Exception.Message)" -Color $Colors.Error -Prefix "POST-DEPLOY"
        return $false
    }
    finally {
        Pop-Location
    }
}

# Main deployment process
function Start-Deployment {
    Write-Host ""
    Write-Host "🚀 Laravel Staging Deployment" -ForegroundColor $Colors.Highlight
    Write-Host "==============================" -ForegroundColor $Colors.Highlight
    Write-Host "Source: $RepoRoot" -ForegroundColor $Colors.Info
    Write-Host "Target: $StagingPath" -ForegroundColor $Colors.Info
    Write-Host ""

    Write-DeployLog "Starting staging deployment process" -Color $Colors.Info -Prefix "START"

    # Get Git information
    try {
        $GitBranch = git branch --show-current
        $GitCommit = git rev-parse --short HEAD
        $GitAuthor = git log -1 --pretty=%an

        Write-DeployLog "Git Branch: $GitBranch" -Color $Colors.Info -Prefix "GIT"
        Write-DeployLog "Git Commit: $GitCommit" -Color $Colors.Info -Prefix "GIT"
        Write-DeployLog "Git Author: $GitAuthor" -Color $Colors.Info -Prefix "GIT"
    }
    catch {
        Write-DeployLog "Could not retrieve Git information" -Color $Colors.Warning -Prefix "GIT"
    }

    # Confirmation
    if (-not $AutoConfirm) {
        Write-Host "Do you want to continue with the deployment? [Y/N]: " -NoNewline -ForegroundColor $Colors.Warning
        $Confirmation = Read-Host
        if ($Confirmation -notmatch '^[Yy]') {
            Write-DeployLog "Deployment cancelled by user" -Color $Colors.Warning -Prefix "CANCELLED"
            return $false
        }
    }

    # Step 1: Create backup
    Write-Host "📋 Step 1: Creating backup..." -ForegroundColor $Colors.Info
    if (-not (New-StagingBackup)) {
        Write-DeployLog "Deployment failed: Backup creation failed" -Color $Colors.Error -Prefix "FAILED"
        return $false
    }

    # Step 2: Deploy files
    Write-Host "📦 Step 2: Deploying files..." -ForegroundColor $Colors.Info
    if (-not (Deploy-Files)) {
        Write-DeployLog "Deployment failed: File deployment failed" -Color $Colors.Error -Prefix "FAILED"
        return $false
    }

    # Step 3: Post-deployment tasks
    Write-Host "🔧 Step 3: Running post-deployment tasks..." -ForegroundColor $Colors.Info
    if (-not (Invoke-PostDeploymentTasks)) {
        Write-DeployLog "Deployment completed with warnings: Post-deployment tasks failed" -Color $Colors.Warning -Prefix "WARNING"
    }

    Write-Host ""
    Write-Host "🎉 Staging deployment completed successfully!" -ForegroundColor $Colors.Success
    Write-Host "📍 Staging URL: $StagingPath" -ForegroundColor $Colors.Highlight
    Write-Host ""

    Write-DeployLog "Staging deployment completed successfully" -Color $Colors.Success -Prefix "SUCCESS"
    return $true
}

# Run deployment
try {
    $Success = Start-Deployment
    if ($Success) {
        exit 0
    }
    else {
        exit 1
    }
}
catch {
    Write-DeployLog "Deployment failed with exception: $($_.Exception.Message)" -Color $Colors.Error -Prefix "EXCEPTION"
    exit 1
}

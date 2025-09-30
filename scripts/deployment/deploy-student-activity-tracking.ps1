#!/usr/bin/env powershell
<#
.SYNOPSIS
Student Activity Tracking System - Deployment Script

.DESCRIPTION
Deploys the Student Activity Tracking system from development to staging environment.
This script handles file deployment, backup creation, and post-deployment configuration.

.PARAMETER Environment
Target environment (staging, production). Default: staging

.PARAMETER SkipBackup
Skip creating backup of existing files

.PARAMETER DryRun
Preview deployment without making changes

.PARAMETER Force
Force deployment without confirmation prompts

.EXAMPLE
.\deploy-student-activity-tracking.ps1
Deploy to staging with backup and confirmation

.EXAMPLE
.\deploy-student-activity-tracking.ps1 -Environment staging -DryRun
Preview staging deployment

.EXAMPLE
.\deploy-student-activity-tracking.ps1 -Environment staging -Force -SkipBackup
Fast deployment without backup or prompts

.NOTES
Author: FROST Development Team
Version: 1.0
Created: September 30, 2025
#>

param(
    [Parameter(HelpMessage="Target environment (staging/production)")]
    [ValidateSet("staging", "production")]
    [string]$Environment = "staging",

    [Parameter(HelpMessage="Skip creating backup of existing files")]
    [switch]$SkipBackup = $false,

    [Parameter(HelpMessage="Preview deployment without making changes")]
    [switch]$DryRun = $false,

    [Parameter(HelpMessage="Force deployment without confirmation prompts")]
    [switch]$Force = $false,

    [Parameter(HelpMessage="Verbose output for debugging")]
    [switch]$Verbose = $false
)

# Script configuration
$Config = @{
    SourcePath = "\\develc\webroot\frost-rclark"
    Environments = @{
        staging = "\\atlas\webroot\frost-staging"
        production = "\\atlas\webroot\frost-production"  # Placeholder for future
    }
    LogFile = Join-Path $PSScriptRoot "logs\deploy-$(Get-Date -Format 'yyyyMMdd-HHmmss').log"
}

# Ensure logs directory exists
$LogDir = Split-Path $Config.LogFile -Parent
if (!(Test-Path $LogDir)) {
    New-Item -ItemType Directory -Path $LogDir -Force | Out-Null
}

# Logging function
function Write-Log {
    param(
        [string]$Message,
        [ValidateSet("INFO", "WARN", "ERROR", "SUCCESS", "DEBUG")]
        [string]$Level = "INFO",
        [switch]$NoConsole
    )

    $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $LogEntry = "[$Timestamp] [$Level] $Message"

    # Write to log file
    Add-Content -Path $Config.LogFile -Value $LogEntry

    # Write to console with colors
    if (!$NoConsole) {
        $Color = switch ($Level) {
            "INFO" { "White" }
            "WARN" { "Yellow" }
            "ERROR" { "Red" }
            "SUCCESS" { "Green" }
            "DEBUG" { "Gray" }
        }

        Write-Host "[$($Timestamp.Split(' ')[1])] $Message" -ForegroundColor $Color
    }
}

function Show-Banner {
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║              FROST - Student Activity Tracking              ║" -ForegroundColor Cyan
    Write-Host "║                    Deployment Script                        ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
}

function Test-Prerequisites {
    Write-Log "Checking deployment prerequisites..." "INFO"

    $Issues = @()

    # Check source path
    if (!(Test-Path $Config.SourcePath)) {
        $Issues += "Source path does not exist: $($Config.SourcePath)"
    }

    # Check target path
    $TargetPath = $Config.Environments[$Environment]
    if (!(Test-Path $TargetPath)) {
        $Issues += "Target path does not exist: $TargetPath"
    }

    # Check if Laravel application
    $ArtisanPath = Join-Path $Config.SourcePath "artisan"
    if (!(Test-Path $ArtisanPath)) {
        $Issues += "Not a Laravel application (artisan not found)"
    }

    # Check if target is Laravel application
    $TargetArtisan = Join-Path $TargetPath "artisan"
    if (!(Test-Path $TargetArtisan)) {
        $Issues += "Target is not a Laravel application (artisan not found)"
    }

    # Check PowerShell version
    if ($PSVersionTable.PSVersion.Major -lt 5) {
        $Issues += "PowerShell 5.0 or higher required"
    }

    if ($Issues.Count -gt 0) {
        Write-Log "Prerequisites check failed:" "ERROR"
        foreach ($Issue in $Issues) {
            Write-Log "  - $Issue" "ERROR"
        }
        return $false
    }

    Write-Log "Prerequisites check passed" "SUCCESS"
    return $true
}

function Get-DeploymentFiles {
    return @(
        # Core system files
        @{
            Source = "database/migrations/2025_09_30_000000_create_student_activities_table.php"
            Critical = $true
            Description = "Database migration for student activities table"
        },
        @{
            Source = "app/Models/StudentActivity.php"
            Critical = $true
            Description = "Eloquent model for student activities"
        },
        @{
            Source = "app/Services/StudentActivityService.php"
            Critical = $true
            Description = "Service class for activity tracking business logic"
        },
        @{
            Source = "app/Http/Middleware/TrackStudentActivity.php"
            Critical = $true
            Description = "Middleware for automatic activity tracking"
        },
        @{
            Source = "app/Http/Controllers/Api/StudentActivityController.php"
            Critical = $true
            Description = "API controller for activity tracking endpoints"
        },
        @{
            Source = "routes/api/student_activity_routes.php"
            Critical = $true
            Description = "API routes for student activity tracking"
        },

        # Documentation files
        @{
            Source = "docs/architecture/student-activity-tracking.md"
            Critical = $false
            Description = "System architecture documentation"
        },
        @{
            Source = "docs/deployment/student-activity-tracking-implementation.md"
            Critical = $false
            Description = "Implementation guide"
        },
        @{
            Source = "docs/deployment/staging-deployment-guide.md"
            Critical = $false
            Description = "Staging deployment guide"
        },

        # Deployment scripts
        @{
            Source = "KKP/scripts/deploy-student-activity-tracking.ps1"
            Target = "KKP/scripts/deploy-student-activity-tracking.ps1"
            Critical = $false
            Description = "Deployment script (this file)"
        }
    )
}

function Create-BackupDirectory {
    param([string]$TargetPath)

    if ($SkipBackup) {
        Write-Log "Skipping backup creation (SkipBackup flag set)" "WARN"
        return $null
    }

    $BackupDir = Join-Path $TargetPath "backups\student-activity-tracking\$(Get-Date -Format 'yyyyMMdd-HHmmss')"

    if (!$DryRun) {
        try {
            New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
            Write-Log "Created backup directory: $BackupDir" "SUCCESS"
        } catch {
            Write-Log "Failed to create backup directory: $($_.Exception.Message)" "ERROR"
            return $null
        }
    } else {
        Write-Log "Would create backup directory: $BackupDir" "DEBUG"
    }

    return $BackupDir
}

function Deploy-Files {
    param(
        [string]$SourcePath,
        [string]$TargetPath,
        [string]$BackupDir
    )

    $Files = Get-DeploymentFiles
    $Results = @{
        Deployed = 0
        BackedUp = 0
        Skipped = 0
        Errors = @()
    }

    Write-Log "Starting file deployment..." "INFO"
    Write-Log "Source: $SourcePath" "INFO"
    Write-Log "Target: $TargetPath" "INFO"

    foreach ($FileInfo in $Files) {
        $SourceFile = Join-Path $SourcePath $FileInfo.Source
        $TargetFile = if ($FileInfo.Target) {
            Join-Path $TargetPath $FileInfo.Target
        } else {
            Join-Path $TargetPath $FileInfo.Source
        }

        try {
            # Check if source file exists
            if (!(Test-Path $SourceFile)) {
                if ($FileInfo.Critical) {
                    $Error = "CRITICAL: Source file not found: $($FileInfo.Source)"
                    $Results.Errors += $Error
                    Write-Log $Error "ERROR"
                    continue
                } else {
                    Write-Log "Optional file not found, skipping: $($FileInfo.Source)" "WARN"
                    $Results.Skipped++
                    continue
                }
            }

            # Create target directory
            $TargetDir = Split-Path $TargetFile -Parent
            if (!(Test-Path $TargetDir)) {
                if (!$DryRun) {
                    New-Item -ItemType Directory -Path $TargetDir -Force | Out-Null
                }
                Write-Log "Created directory: $($TargetDir.Replace($TargetPath, ''))" "INFO"
            }

            # Backup existing file
            if (!$SkipBackup -and (Test-Path $TargetFile) -and $BackupDir) {
                $BackupFile = Join-Path $BackupDir $FileInfo.Source
                $BackupFileDir = Split-Path $BackupFile -Parent

                if (!$DryRun) {
                    New-Item -ItemType Directory -Path $BackupFileDir -Force | Out-Null
                    Copy-Item $TargetFile $BackupFile -Force
                }
                $Results.BackedUp++
                Write-Log "Backed up: $($FileInfo.Source)" "INFO"
            }

            # Deploy file
            if (!$DryRun) {
                Copy-Item $SourceFile $TargetFile -Force
            }
            $Results.Deployed++

            $Status = if ($DryRun) { "Would deploy" } else { "Deployed" }
            Write-Log "$Status: $($FileInfo.Source)" "SUCCESS"

            if ($Verbose) {
                Write-Log "  Description: $($FileInfo.Description)" "DEBUG"
            }

        } catch {
            $Error = "Failed to deploy $($FileInfo.Source): $($_.Exception.Message)"
            $Results.Errors += $Error
            Write-Log $Error "ERROR"
        }
    }

    return $Results
}

function Show-PostDeploymentTasks {
    Write-Log "`nPost-deployment configuration required:" "WARN"

    $Tasks = @(
        @{
            File = "app/Http/Kernel.php"
            Action = "Add middleware to 'web' group"
            Code = "\App\Http\Middleware\TrackStudentActivity::class"
        },
        @{
            File = "routes/api.php"
            Action = "Include activity routes"
            Code = "require __DIR__ . '/api/student_activity_routes.php';"
        },
        @{
            File = "app/Providers/AppServiceProvider.php"
            Action = "Register service in register() method"
            Code = "\$this->app->singleton(\App\Services\StudentActivityService::class);"
        }
    )

    foreach ($Task in $Tasks) {
        Write-Log "  ✓ Update $($Task.File):" "INFO"
        Write-Log "    $($Task.Action)" "INFO"
        Write-Log "    Add: $($Task.Code)" "DEBUG"
        Write-Log "" "INFO"
    }

    Write-Log "Database Migration:" "INFO"
    Write-Log "  cd $($Config.Environments[$Environment])" "DEBUG"
    Write-Log "  php artisan migrate" "DEBUG"
    Write-Log "" "INFO"

    Write-Log "Clear Caches:" "INFO"
    Write-Log "  php artisan route:clear" "DEBUG"
    Write-Log "  php artisan config:clear" "DEBUG"
    Write-Log "  php artisan cache:clear" "DEBUG"
}

function Show-RollbackInstructions {
    param([string]$BackupDir)

    if ($SkipBackup -or !$BackupDir) {
        return
    }

    Write-Log "`nROLLBACK INSTRUCTIONS:" "WARN"
    Write-Log "If you need to rollback this deployment:" "INFO"
    Write-Log "  1. Restore files: Copy-Item '$BackupDir\*' '$($Config.Environments[$Environment])\' -Recurse -Force" "DEBUG"
    Write-Log "  2. Rollback migration: php artisan migrate:rollback --step=1" "DEBUG"
}

function Confirm-Deployment {
    if ($Force) {
        return $true
    }

    Write-Host ""
    Write-Host "Deployment Summary:" -ForegroundColor Yellow
    Write-Host "  Environment: $Environment" -ForegroundColor White
    Write-Host "  Source: $($Config.SourcePath)" -ForegroundColor White
    Write-Host "  Target: $($Config.Environments[$Environment])" -ForegroundColor White
    Write-Host "  Backup: $(if ($SkipBackup) { 'No' } else { 'Yes' })" -ForegroundColor White
    Write-Host "  Dry Run: $(if ($DryRun) { 'Yes' } else { 'No' })" -ForegroundColor White
    Write-Host ""

    $Response = Read-Host "Continue with deployment? (y/N)"
    return ($Response -eq 'y' -or $Response -eq 'Y' -or $Response -eq 'yes')
}

# Main execution
try {
    Show-Banner

    Write-Log "Starting Student Activity Tracking deployment" "INFO"
    Write-Log "Environment: $Environment" "INFO"
    Write-Log "Dry Run: $DryRun" "INFO"
    Write-Log "Skip Backup: $SkipBackup" "INFO"
    Write-Log "Log file: $($Config.LogFile)" "INFO"

    # Check prerequisites
    if (!(Test-Prerequisites)) {
        Write-Log "Deployment aborted due to failed prerequisites" "ERROR"
        exit 1
    }

    # Get confirmation
    if (!(Confirm-Deployment)) {
        Write-Log "Deployment cancelled by user" "WARN"
        exit 0
    }

    $TargetPath = $Config.Environments[$Environment]

    # Create backup directory
    $BackupDir = Create-BackupDirectory -TargetPath $TargetPath

    # Deploy files
    $Results = Deploy-Files -SourcePath $Config.SourcePath -TargetPath $TargetPath -BackupDir $BackupDir

    # Show results
    Write-Log "`n" + "="*60 "INFO"
    Write-Log "DEPLOYMENT SUMMARY" "INFO"
    Write-Log "="*60 "INFO"

    Write-Log "Files deployed: $($Results.Deployed)" "SUCCESS"
    if (!$SkipBackup) {
        Write-Log "Files backed up: $($Results.BackedUp)" "INFO"
    }
    if ($Results.Skipped -gt 0) {
        Write-Log "Files skipped: $($Results.Skipped)" "WARN"
    }

    if ($Results.Errors.Count -gt 0) {
        Write-Log "Errors encountered: $($Results.Errors.Count)" "ERROR"
        foreach ($Error in $Results.Errors) {
            Write-Log "  - $Error" "ERROR"
        }
        Write-Log "`nDeployment completed with errors!" "ERROR"
        exit 1
    } else {
        Write-Log "`nDeployment completed successfully!" "SUCCESS"
    }

    if (!$DryRun) {
        Show-PostDeploymentTasks
        Show-RollbackInstructions -BackupDir $BackupDir
    }

    Write-Log "Deployment log saved to: $($Config.LogFile)" "INFO"

} catch {
    Write-Log "Deployment failed with exception: $($_.Exception.Message)" "ERROR"
    Write-Log "Stack trace: $($_.ScriptStackTrace)" "ERROR"
    exit 1
}

# Student Activity Tracking - Simple Deployment Script
param(
    [string]$SourcePath = "\\develc\webroot\frost-rclark",
    [string]$TargetPath = "\\atlas\webroot\frost-staging",
    [switch]$SkipBackup = $false
)

function Write-Status {
    param([string]$Message, [string]$Type = "INFO")
    $Color = "White"
    if ($Type -eq "SUCCESS") { $Color = "Green" }
    if ($Type -eq "ERROR") { $Color = "Red" }
    if ($Type -eq "WARN") { $Color = "Yellow" }
    if ($Type -eq "INFO") { $Color = "Cyan" }
    
    $Time = Get-Date -Format "HH:mm:ss"
    Write-Host "[$Time] $Message" -ForegroundColor $Color
}

# Files to deploy
$FilesToDeploy = @(
    "database/migrations/2025_09_30_000000_create_student_activities_table.php",
    "app/Models/StudentActivity.php",
    "app/Services/StudentActivityService.php",
    "app/Http/Middleware/TrackStudentActivity.php",
    "app/Http/Controllers/Api/StudentActivityController.php",
    "routes/api/student_activity_routes.php",
    "docs/architecture/student-activity-tracking.md",
    "docs/deployment/student-activity-tracking-implementation.md",
    "docs/deployment/staging-deployment-guide.md",
    "KKP/scripts/deploy-student-activity-tracking.ps1",
    "KKP/scripts/deploy-student-activity-tracking.sh",
    "KKP/scripts/deploy-launcher.bat",
    "KKP/scripts/deploy-config.conf",
    "KKP/scripts/README.md",
    "KKP/scripts/test-staging-simple.ps1",
    "KKP/scripts/dry-run-deploy.ps1"
)

Write-Host ""
Write-Host "=============================================" -ForegroundColor Green
Write-Host "  Student Activity Tracking - DEPLOYMENT" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green
Write-Host ""

Write-Status "Starting deployment..." "INFO"
Write-Status "Source: $SourcePath" "INFO"
Write-Status "Target: $TargetPath" "INFO"
Write-Host ""

# Confirmation
Write-Host "Deployment Summary:" -ForegroundColor Yellow
Write-Host "  Files to deploy: $($FilesToDeploy.Count)" -ForegroundColor White
Write-Host "  Source: $SourcePath" -ForegroundColor White
Write-Host "  Target: $TargetPath" -ForegroundColor White
Write-Host "  Backup: $(if ($SkipBackup) { 'No' } else { 'Yes' })" -ForegroundColor White
Write-Host ""

$Response = Read-Host "Continue with deployment? (y/N)"
if ($Response -ne 'y' -and $Response -ne 'Y' -and $Response -ne 'yes') {
    Write-Status "Deployment cancelled by user" "WARN"
    exit 0
}

# Create backup directory if needed
$BackupDir = $null
if (!$SkipBackup) {
    $BackupDir = Join-Path $TargetPath "backups\student-activity-tracking\$(Get-Date -Format 'yyyyMMdd-HHmmss')"
    try {
        New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
        Write-Status "Created backup directory: $BackupDir" "SUCCESS"
    } catch {
        Write-Status "Warning: Could not create backup directory: $($_.Exception.Message)" "WARN"
        $BackupDir = $null
    }
}

# Check prerequisites
Write-Status "Checking prerequisites..." "INFO"

if (!(Test-Path $SourcePath)) {
    Write-Status "ERROR: Source path does not exist: $SourcePath" "ERROR"
    exit 1
}

if (!(Test-Path $TargetPath)) {
    Write-Status "ERROR: Target path does not exist: $TargetPath" "ERROR"
    exit 1
}

Write-Status "Prerequisites validated" "SUCCESS"
Write-Host ""

# Deploy files
Write-Status "Deploying files..." "INFO"
$DeployedCount = 0
$BackedUpCount = 0
$Errors = @()

foreach ($File in $FilesToDeploy) {
    $SourceFile = Join-Path $SourcePath $File
    $TargetFile = Join-Path $TargetPath $File
    $TargetDir = Split-Path $TargetFile -Parent
    
    try {
        # Check if source file exists
        if (!(Test-Path $SourceFile)) {
            Write-Status "WARNING: Source file not found: $File" "WARN"
            continue
        }
        
        # Create target directory if needed
        if (!(Test-Path $TargetDir)) {
            New-Item -ItemType Directory -Path $TargetDir -Force | Out-Null
            Write-Status "Created directory: $($TargetDir.Replace($TargetPath, ''))" "INFO"
        }
        
        # Backup existing file if it exists
        if (!$SkipBackup -and (Test-Path $TargetFile) -and $BackupDir) {
            $BackupFile = Join-Path $BackupDir $File
            $BackupFileDir = Split-Path $BackupFile -Parent
            New-Item -ItemType Directory -Path $BackupFileDir -Force | Out-Null
            Copy-Item $TargetFile $BackupFile -Force
            $BackedUpCount++
            Write-Status "Backed up: $File" "INFO"
        }
        
        # Copy the file
        Copy-Item $SourceFile $TargetFile -Force
        Write-Status "DEPLOYED: $File" "SUCCESS"
        $DeployedCount++
        
    } catch {
        $ErrorMsg = "Failed to deploy $File : $($_.Exception.Message)"
        $Errors += $ErrorMsg
        Write-Status $ErrorMsg "ERROR"
    }
}

Write-Host ""
Write-Status "=============================================" "INFO"
Write-Status "DEPLOYMENT SUMMARY" "INFO"
Write-Status "=============================================" "INFO"

Write-Status "Files deployed: $DeployedCount" "SUCCESS"
if (!$SkipBackup -and $BackedUpCount -gt 0) {
    Write-Status "Files backed up: $BackedUpCount" "INFO"
}

if ($Errors.Count -gt 0) {
    Write-Status "Errors encountered: $($Errors.Count)" "ERROR"
    foreach ($Error in $Errors) {
        Write-Status "  - $Error" "ERROR"
    }
} else {
    Write-Status "🎉 DEPLOYMENT COMPLETED SUCCESSFULLY!" "SUCCESS"
}

Write-Host ""
Write-Status "POST-DEPLOYMENT CONFIGURATION REQUIRED:" "WARN"
Write-Status "=============================================" "WARN"

Write-Status "1. UPDATE app/Http/Kernel.php" "INFO"
Write-Status "   Add to 'web' middleware group:" "INFO"
Write-Status "   \\App\\Http\\Middleware\\TrackStudentActivity::class," "WARN"
Write-Host ""

Write-Status "2. UPDATE routes/api.php" "INFO"
Write-Status "   Add this line:" "INFO"
Write-Status "   require __DIR__ . '/api/student_activity_routes.php';" "WARN"
Write-Host ""

Write-Status "3. UPDATE app/Providers/AppServiceProvider.php" "INFO"
Write-Status "   Add to register() method:" "INFO"
Write-Status "   \$this->app->singleton(\\App\\Services\\StudentActivityService::class);" "WARN"
Write-Host ""

Write-Status "4. RUN DATABASE MIGRATION" "INFO"
Write-Status "   cd $TargetPath" "INFO"
Write-Status "   php artisan migrate" "WARN"
Write-Host ""

Write-Status "5. CLEAR CACHES" "INFO"
Write-Status "   php artisan route:clear" "INFO"
Write-Status "   php artisan config:clear" "INFO"
Write-Status "   php artisan cache:clear" "WARN"
Write-Host ""

if ($BackupDir) {
    Write-Status "ROLLBACK INSTRUCTIONS (if needed):" "WARN"
    Write-Status "1. Restore files: Copy-Item '$BackupDir\\*' '$TargetPath\\' -Recurse -Force" "INFO"
    Write-Status "2. Rollback migration: php artisan migrate:rollback --step=1" "INFO"
    Write-Host ""
}

Write-Status "Deployment completed at $(Get-Date)" "SUCCESS"
Write-Status "Next: Follow the post-deployment configuration steps above" "INFO"

if ($Errors.Count -gt 0) {
    exit 1
} else {
    exit 0
}
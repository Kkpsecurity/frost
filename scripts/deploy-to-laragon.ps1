# Laravel Local Deployment Script for Laragon
# Usage: .\scripts\deploy-to-laragon.ps1

Write-Host "🚀 Laravel Local Deployment to Laragon" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan

# Configuration
$sourceDir = "\\develc\webroot\frost-patch"
$targetDir = "C:\laragon\www\frost-live-dev"
$excludePatterns = @(
    "node_modules",
    "vendor", 
    ".env",
    ".env.*",
    "storage\logs\*",
    "storage\framework\cache\*",
    "storage\framework\sessions\*",
    "storage\framework\views\*",
    ".git",
    ".gitignore",
    "npm-debug.log*",
    "yarn-debug.log*",
    "yarn-error.log*",
    "*.log",
    ".DS_Store",
    "Thumbs.db"
)

# Check if source directory exists
if (-not (Test-Path $sourceDir)) {
    Write-Host "❌ Error: Source directory not found: $sourceDir" -ForegroundColor Red
    exit 1
}

# Create target directory if it doesn't exist
if (-not (Test-Path $targetDir)) {
    Write-Host "Creating target directory: $targetDir" -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
}

Write-Host "Source: $sourceDir" -ForegroundColor Green
Write-Host "Target: $targetDir" -ForegroundColor Green
Write-Host ""

try {
    # Remove existing files in target (except .env, vendor, node_modules, and public)
    Write-Host "Cleaning target directory..." -ForegroundColor Yellow
    Write-Host "   Preserving: vendor, node_modules, public folders and .env files" -ForegroundColor Gray
    
    Get-ChildItem -Path $targetDir -Recurse | Where-Object { 
        $_.Name -notlike "vendor" -and 
        $_.Name -notlike ".env*" -and 
        $_.Name -notlike "node_modules" -and
        $_.Name -notlike "public"
    } | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue

    # Copy files using robocopy for better performance and exclusion handling
    Write-Host "Copying Laravel application files..." -ForegroundColor Green
    
    $robocopyArgs = @(
        $sourceDir,
        $targetDir,
        "/MIR",           # Mirror directory
        "/XD",            # Exclude directories
        "node_modules",
        "vendor",
        ".git",
        "storage\logs",
        "storage\framework\cache",
        "storage\framework\sessions", 
        "storage\framework\views",
        "/XF",            # Exclude files
        ".env",
        ".env.*",
        "*.log",
        ".DS_Store",
        "Thumbs.db",
        "/R:3",           # Retry 3 times
        "/W:1",           # Wait 1 second between retries
        "/NFL",           # No file list
        "/NDL",           # No directory list
        "/NP"             # No progress
    )
    
    & robocopy @robocopyArgs | Out-Null
    
    # Robocopy exit codes 0-7 are success
    if ($LASTEXITCODE -le 7) {
        Write-Host "Files copied successfully!" -ForegroundColor Green
    } else {
        throw "Robocopy failed with exit code: $LASTEXITCODE"
    }

    # Create necessary directories
    Write-Host "📁 Creating necessary directories..." -ForegroundColor Yellow
    $requiredDirs = @(
        "$targetDir\storage\logs",
        "$targetDir\storage\framework\cache",
        "$targetDir\storage\framework\sessions",
        "$targetDir\storage\framework\views",
        "$targetDir\bootstrap\cache"
    )
    
    foreach ($dir in $requiredDirs) {
        if (-not (Test-Path $dir)) {
            New-Item -ItemType Directory -Path $dir -Force | Out-Null
            Write-Host "   Created: $dir" -ForegroundColor Gray
        }
    }

    # Set proper permissions for storage and bootstrap/cache
    Write-Host "🔐 Setting directory permissions..." -ForegroundColor Yellow
    $permissionDirs = @(
        "$targetDir\storage",
        "$targetDir\bootstrap\cache"
    )
    
    foreach ($dir in $permissionDirs) {
        if (Test-Path $dir) {
            try {
                # Give full control to current user
                $acl = Get-Acl $dir
                $accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule(
                    [System.Security.Principal.WindowsIdentity]::GetCurrent().Name,
                    "FullControl",
                    "ContainerInherit,ObjectInherit",
                    "None",
                    "Allow"
                )
                $acl.SetAccessRule($accessRule)
                Set-Acl -Path $dir -AclObject $acl
                Write-Host "   Permissions set for: $dir" -ForegroundColor Gray
            } catch {
                Write-Host "   Warning: Could not set permissions for $dir" -ForegroundColor Yellow
            }
        }
    }

    # Summary
    Write-Host ""
    Write-Host "Deployment completed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Cyan
    Write-Host "   1. Navigate to: $targetDir" -ForegroundColor Gray
    Write-Host "   2. Run: composer install" -ForegroundColor Gray
    Write-Host "   3. Run: php artisan key:generate" -ForegroundColor Gray
    Write-Host "   4. Run: php artisan migrate (if needed)" -ForegroundColor Gray
    Write-Host "   5. Access: http://frost-live-dev.test" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Files excluded from deployment:" -ForegroundColor Yellow
    foreach ($pattern in $excludePatterns) {
        Write-Host "   - $pattern" -ForegroundColor Gray
    }

} catch {
    Write-Host "Error during deployment: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Deployment to Laragon completed!" -ForegroundColor Green

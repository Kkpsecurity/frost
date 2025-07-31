# Database Copy Script - PowerShell Version
# Copies data from frost-patch database to frost-devel database

param(
    [switch]$Force,
    [string]$SourceDb = "frost-patch",
    [string]$TargetDb = "frost-devel"
)

# Configuration
$Config = @{
    Host = "develc.hq.cisadmin.com"
    Port = "5432"
    Username = "frost"
    Password = "kj,L@-N%AyAFWxda"
    SourceDb = $SourceDb
    TargetDb = $TargetDb
}

function Write-ColorOutput($ForegroundColor) {
    $fc = $host.UI.RawUI.ForegroundColor
    $host.UI.RawUI.ForegroundColor = $ForegroundColor
    if ($args) {
        Write-Output $args
    } else {
        $input | Write-Output
    }
    $host.UI.RawUI.ForegroundColor = $fc
}

function Write-Header {
    Write-ColorOutput Yellow @"
Database Copy Script
====================
Source: $($Config.SourceDb)
Target: $($Config.TargetDb)
Host: $($Config.Host)

"@
}

function Test-PostgreSQLTools {
    Write-Host "🔍 Validating PostgreSQL tools..." -ForegroundColor Cyan

    try {
        $null = Get-Command pg_dump -ErrorAction Stop
        $null = Get-Command psql -ErrorAction Stop
        Write-ColorOutput Green "   ✅ PostgreSQL client tools found"
        return $true
    } catch {
        Write-ColorOutput Red "   ❌ PostgreSQL client tools not found. Please install PostgreSQL client tools."
        return $false
    }
}

function Confirm-Operation {
    if ($Force) {
        return $true
    }

    Write-ColorOutput Red @"
⚠️  WARNING: This operation will:
   1. Drop all tables in '$($Config.TargetDb)'
   2. Copy all data from '$($Config.SourceDb)' to '$($Config.TargetDb)'
   3. This action cannot be undone!

"@

    $response = Read-Host "Do you want to continue? (yes/no)"
    return ($response -eq "yes")
}

function Set-PostgreSQLEnvironment {
    $env:PGHOST = $Config.Host
    $env:PGPORT = $Config.Port
    $env:PGUSER = $Config.Username
    $env:PGPASSWORD = $Config.Password
}

function New-DatabaseBackup {
    Write-Host "1️⃣ Creating backup of target database..." -ForegroundColor Cyan

    $backupDir = Join-Path $PSScriptRoot "backups"
    if (-not (Test-Path $backupDir)) {
        New-Item -ItemType Directory -Path $backupDir | Out-Null
    }

    $timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
    $backupFile = Join-Path $backupDir "$($Config.TargetDb)_backup_$timestamp.sql"

    try {
        $process = Start-Process -FilePath "pg_dump" -ArgumentList @(
            "-d", $Config.TargetDb,
            "-f", $backupFile,
            "--no-password"
        ) -Wait -PassThru -NoNewWindow

        if ($process.ExitCode -eq 0) {
            Write-ColorOutput Green "   ✅ Backup created: $([System.IO.Path]::GetFileName($backupFile))"
        } else {
            Write-ColorOutput Yellow "   ⚠️ Backup failed (continuing anyway)"
        }
    } catch {
        Write-ColorOutput Yellow "   ⚠️ Backup failed: $($_.Exception.Message)"
    }
}

function Clear-TargetDatabase {
    Write-Host "2️⃣ Preparing target database..." -ForegroundColor Cyan

    $sql = "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"

    try {
        $process = Start-Process -FilePath "psql" -ArgumentList @(
            "-d", $Config.TargetDb,
            "-c", $sql,
            "--no-password"
        ) -Wait -PassThru -NoNewWindow

        if ($process.ExitCode -eq 0) {
            Write-ColorOutput Green "   ✅ Target database cleared"
        } else {
            throw "Failed to clear target database"
        }
    } catch {
        Write-ColorOutput Red "   ❌ Failed to clear target database: $($_.Exception.Message)"
        exit 1
    }
}

function Copy-DatabaseData {
    Write-Host "3️⃣ Copying data from source to target..." -ForegroundColor Cyan

    $tempFile = Join-Path $PSScriptRoot "temp_dump.sql"

    try {
        # Dump source database
        Write-Host "   📤 Dumping source database..." -ForegroundColor Gray
        $process = Start-Process -FilePath "pg_dump" -ArgumentList @(
            "-d", $Config.SourceDb,
            "-f", $tempFile,
            "--no-owner",
            "--no-privileges",
            "--no-password"
        ) -Wait -PassThru -NoNewWindow

        if ($process.ExitCode -ne 0) {
            throw "Failed to dump source database"
        }

        # Restore to target database
        Write-Host "   📥 Restoring to target database..." -ForegroundColor Gray
        $process = Start-Process -FilePath "psql" -ArgumentList @(
            "-d", $Config.TargetDb,
            "-f", $tempFile,
            "--no-password"
        ) -Wait -PassThru -NoNewWindow

        if ($process.ExitCode -eq 0) {
            Write-ColorOutput Green "   ✅ Data copied successfully"
        } else {
            throw "Failed to restore data to target database"
        }
    } catch {
        Write-ColorOutput Red "   ❌ Error copying data: $($_.Exception.Message)"
        exit 1
    } finally {
        # Clean up temp file
        if (Test-Path $tempFile) {
            Remove-Item $tempFile
        }
    }
}

function Invoke-LaravelMigrations {
    Write-Host "4️⃣ Running Laravel migrations..." -ForegroundColor Cyan

    $laravelDir = Split-Path $PSScriptRoot -Parent
    $originalLocation = Get-Location

    try {
        Set-Location $laravelDir

        $process = Start-Process -FilePath "php" -ArgumentList @(
            "artisan", "migrate", "--force"
        ) -Wait -PassThru -NoNewWindow

        if ($process.ExitCode -eq 0) {
            Write-ColorOutput Green "   ✅ Migrations completed"
        } else {
            Write-ColorOutput Yellow "   ⚠️ Migration warnings (check manually)"
        }
    } catch {
        Write-ColorOutput Yellow "   ⚠️ Migration error: $($_.Exception.Message)"
    } finally {
        Set-Location $originalLocation
    }
}

# Main execution
try {
    Write-Header

    if (-not (Test-PostgreSQLTools)) {
        exit 1
    }

    if (-not (Confirm-Operation)) {
        Write-Host "Operation cancelled." -ForegroundColor Yellow
        exit 0
    }

    Set-PostgreSQLEnvironment

    Write-Host "`n🚀 Starting database copy process...`n" -ForegroundColor Green

    New-DatabaseBackup
    Clear-TargetDatabase
    Copy-DatabaseData
    Invoke-LaravelMigrations

    Write-ColorOutput Green "`n✅ Database copy completed successfully!`n"

} catch {
    Write-ColorOutput Red "`n❌ Error: $($_.Exception.Message)`n"
    exit 1
}

if (-not $Force) {
    Read-Host "Press Enter to continue"
}

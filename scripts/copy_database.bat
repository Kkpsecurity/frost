@echo off
REM Database Copy Script - Windows Batch Version
REM Copies data from frost-patch database to frost-devel database

echo Database Copy Script
echo ====================
echo Source: frost-patch
echo Target: frost-devel
echo Host: develc.hq.cisadmin.com
echo.

REM Set connection parameters
set PGHOST=develc.hq.cisadmin.com
set PGPORT=5432
set PGUSER=frost
set PGPASSWORD=kj,L@-N%AyAFWxda

echo WARNING: This operation will:
echo    1. Drop all tables in 'frost-devel'
echo    2. Copy all data from 'frost-patch' to 'frost-devel'
echo    3. This action cannot be undone!
echo.

set /p confirm="Do you want to continue? (yes/no): "
if not "%confirm%"=="yes" (
    echo Operation cancelled.
    exit /b 0
)

echo.
echo Starting database copy process...
echo.

REM Create backup directory
if not exist "%~dp0backups" mkdir "%~dp0backups"

REM Step 1: Create backup of target database
echo 1. Creating backup of target database...
set timestamp=%date:~-4,4%-%date:~-10,2%-%date:~-7,2%_%time:~0,2%-%time:~3,2%-%time:~6,2%
set timestamp=%timestamp: =0%
set backupfile=%~dp0backups\frost-devel_backup_%timestamp%.sql

pg_dump -d frost-devel -f "%backupfile%" --no-password
if %errorlevel% equ 0 (
    echo    ✓ Backup created: frost-devel_backup_%timestamp%.sql
) else (
    echo    ⚠ Backup failed ^(continuing anyway^)
)

REM Step 2: Clear target database
echo 2. Preparing target database...
psql -d frost-devel -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;" --no-password
if %errorlevel% equ 0 (
    echo    ✓ Target database cleared
) else (
    echo    ✗ Failed to clear target database
    exit /b 1
)

REM Step 3: Copy data
echo 3. Copying data from source to target...
echo    Dumping source database...
pg_dump -d frost-patch -f "%~dp0temp_dump.sql" --no-owner --no-privileges --no-password
if %errorlevel% neq 0 (
    echo    ✗ Failed to dump source database
    exit /b 1
)

echo    Restoring to target database...
psql -d frost-devel -f "%~dp0temp_dump.sql" --no-password
if %errorlevel% equ 0 (
    echo    ✓ Data copied successfully
) else (
    echo    ✗ Failed to restore data to target database
    exit /b 1
)

REM Clean up temp file
if exist "%~dp0temp_dump.sql" del "%~dp0temp_dump.sql"

REM Step 4: Run Laravel migrations
echo 4. Running Laravel migrations...
cd /d "%~dp0.."
php artisan migrate --force
if %errorlevel% equ 0 (
    echo    ✓ Migrations completed
) else (
    echo    ⚠ Migration warnings ^(check manually^)
)

echo.
echo ✓ Database copy completed successfully!
echo.
pause

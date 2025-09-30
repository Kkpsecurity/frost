@echo off
REM FROST Database Sync - Windows Batch Wrapper
REM This script provides a Windows-friendly way to run the database sync

setlocal EnableDelayedExpansion

echo.
echo ==========================================
echo FROST Database Sync - Windows Wrapper
echo ==========================================
echo.

REM Check if Git Bash is available
where bash >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Git Bash not found in PATH
    echo.
    echo Please install Git for Windows or ensure bash.exe is in your PATH
    echo Download from: https://git-scm.com/download/win
    echo.
    pause
    exit /b 1
)

REM Get the directory of this batch file
set SCRIPT_DIR=%~dp0

REM Check if the improved sync script exists
if not exist "%SCRIPT_DIR%sync-db-improved.sh" (
    echo ERROR: sync-db-improved.sh not found
    echo Expected location: %SCRIPT_DIR%sync-db-improved.sh
    echo.
    pause
    exit /b 1
)

echo Found bash.exe: 
where bash
echo.

echo Script location: %SCRIPT_DIR%sync-db-improved.sh
echo.

REM Show available options
echo Available options:
echo   --dry-run, -d     Show what would be done without executing
echo   --force, -f       Skip confirmation prompts
echo   --backup, -b      Force create remote backup
echo   --no-backup       Skip remote backup
echo   --purge, -p       Force purge job tables
echo   --no-purge        Skip purging job tables
echo   --all, -a         Enable backup and purge options
echo   --help, -h        Show detailed help
echo.

REM Ask user for options
set "SYNC_OPTIONS="
set /p "USER_OPTIONS=Enter options (or press Enter for interactive mode): "

if not "!USER_OPTIONS!"=="" (
    set "SYNC_OPTIONS=!USER_OPTIONS!"
)

echo.
echo Running database sync with options: !SYNC_OPTIONS!
echo.

REM Run the sync script using Git Bash
bash "%SCRIPT_DIR%sync-db-improved.sh" !SYNC_OPTIONS!

REM Check the exit code
if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo Database sync completed successfully!
    echo ========================================
) else (
    echo.
    echo ========================================
    echo Database sync failed with error code: %errorlevel%
    echo ========================================
)

echo.
pause

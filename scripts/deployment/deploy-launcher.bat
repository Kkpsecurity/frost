@echo off
setlocal EnableDelayedExpansion

REM Student Activity Tracking - Deployment Launcher
REM This batch file provides an easy interface to run the deployment script

echo.
echo =====================================================
echo   FROST - Student Activity Tracking Deployment
echo =====================================================
echo.

REM Check if PowerShell is available
powershell.exe -Command "exit 0" >nul 2>&1
if errorlevel 1 (
    echo ERROR: PowerShell is not available or not in PATH
    echo Please install PowerShell or use the .sh script on Unix systems
    pause
    exit /b 1
)

REM Check if the PowerShell script exists
if not exist "%~dp0deploy-student-activity-tracking.ps1" (
    echo ERROR: PowerShell deployment script not found
    echo Expected: %~dp0deploy-student-activity-tracking.ps1
    pause
    exit /b 1
)

:menu
echo Select deployment option:
echo.
echo 1. Full deployment (with backup and confirmation)
echo 2. Dry run (preview only - no changes made)
echo 3. Force deployment (no confirmation, with backup)
echo 4. Quick deployment (no backup, no confirmation)
echo 5. Show help
echo 6. Exit
echo.
set /p "choice=Enter your choice (1-6): "

if "%choice%"=="1" goto full_deploy
if "%choice%"=="2" goto dry_run
if "%choice%"=="3" goto force_deploy
if "%choice%"=="4" goto quick_deploy
if "%choice%"=="5" goto show_help
if "%choice%"=="6" goto exit
echo Invalid choice. Please try again.
echo.
goto menu

:full_deploy
echo.
echo Running full deployment with backup and confirmation...
echo.
powershell.exe -ExecutionPolicy Bypass -File "%~dp0deploy-student-activity-tracking.ps1"
goto end

:dry_run
echo.
echo Running dry run (preview mode)...
echo.
powershell.exe -ExecutionPolicy Bypass -File "%~dp0deploy-student-activity-tracking.ps1" -DryRun
goto end

:force_deploy
echo.
echo Running forced deployment with backup...
echo.
powershell.exe -ExecutionPolicy Bypass -File "%~dp0deploy-student-activity-tracking.ps1" -Force
goto end

:quick_deploy
echo.
echo WARNING: Quick deployment will skip backup and confirmation!
set /p "confirm=Are you sure? (y/N): "
if /i not "%confirm%"=="y" goto menu
echo.
echo Running quick deployment...
echo.
powershell.exe -ExecutionPolicy Bypass -File "%~dp0deploy-student-activity-tracking.ps1" -Force -SkipBackup
goto end

:show_help
echo.
echo =====================================================
echo   Deployment Options Help
echo =====================================================
echo.
echo Full Deployment:
echo   - Creates backup of existing files
echo   - Asks for confirmation before proceeding
echo   - Safest option for production use
echo.
echo Dry Run:
echo   - Shows what would be deployed
echo   - No files are actually changed
echo   - Good for testing and verification
echo.
echo Force Deployment:
echo   - Creates backup of existing files
echo   - Skips confirmation prompts
echo   - Faster than full deployment
echo.
echo Quick Deployment:
echo   - No backup created
echo   - No confirmation prompts
echo   - Fastest option but risky
echo.
echo Manual PowerShell Options:
echo   -DryRun          Preview deployment
echo   -Force           Skip confirmations
echo   -SkipBackup      Don't create backups
echo   -Verbose         Show detailed output
echo.
echo Examples:
echo   deploy-student-activity-tracking.ps1 -DryRun -Verbose
echo   deploy-student-activity-tracking.ps1 -Force -SkipBackup
echo.
pause
goto menu

:exit
echo.
echo Deployment cancelled.
goto end

:end
echo.
echo =====================================================
if errorlevel 1 (
    echo   Deployment completed with errors!
    echo   Check the log file for details.
) else (
    echo   Deployment operation completed.
)
echo =====================================================
echo.
echo Press any key to exit...
pause >nul

:EOF

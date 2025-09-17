@echo off
REM Windows wrapper for validate-sync.sh
REM This allows running the validation script from Windows Command Prompt or PowerShell

echo Starting Database Sync Validation...
echo.

REM Check if we're in the scripts directory
if not exist "validate-sync.sh" (
    echo Error: validate-sync.sh not found in current directory
    echo Please navigate to the scripts folder first
    echo.
    pause
    exit /b 1
)

REM Run the bash script
bash validate-sync.sh %*

echo.
echo Validation complete.
pause

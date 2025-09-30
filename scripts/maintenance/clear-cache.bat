@echo off
REM Laravel Cache Clearing Script (Batch Version)
REM This script clears all Laravel caches for development and testing

echo.
echo =====================================
echo    Laravel Cache Clearing Script
echo =====================================
echo.

REM Check if artisan exists
if not exist "artisan" (
    echo ERROR: artisan file not found. Make sure you're in a Laravel project.
    pause
    exit /b 1
)

echo Clearing Laravel caches...
echo.

echo   • Clearing application cache...
php artisan cache:clear
if %errorlevel% equ 0 (
    echo   ✓ Application cache cleared
) else (
    echo   ✗ Failed to clear application cache
)

echo   • Clearing configuration cache...
php artisan config:clear
if %errorlevel% equ 0 (
    echo   ✓ Configuration cache cleared
) else (
    echo   ✗ Failed to clear configuration cache
)

echo   • Clearing route cache...
php artisan route:clear
if %errorlevel% equ 0 (
    echo   ✓ Route cache cleared
) else (
    echo   ✗ Failed to clear route cache
)

echo   • Clearing view cache...
php artisan view:clear
if %errorlevel% equ 0 (
    echo   ✓ View cache cleared
) else (
    echo   ✗ Failed to clear view cache
)

echo   • Clearing compiled services...
php artisan clear-compiled
if %errorlevel% equ 0 (
    echo   ✓ Compiled services cleared
) else (
    echo   ✗ Failed to clear compiled services
)

echo.
echo Recreating optimized caches for development...
echo.

echo   • Recreating configuration cache...
php artisan config:cache
if %errorlevel% equ 0 (
    echo   ✓ Configuration cache recreated
) else (
    echo   ✗ Failed to recreate configuration cache
)

echo   • Recreating route cache...
php artisan route:cache
if %errorlevel% equ 0 (
    echo   ✓ Route cache recreated
) else (
    echo   ✗ Failed to recreate route cache
)

echo.
echo ========================================
echo   Cache clearing completed!
echo   Your Laravel app is ready for testing.
echo ========================================
echo.
echo Useful commands:
echo   • Check routes: php artisan route:list
echo   • Check config: php artisan config:show
echo   • Serve locally: php artisan serve
echo.

pause

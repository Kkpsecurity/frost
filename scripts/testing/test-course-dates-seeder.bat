@echo off
REM Test script for CourseDatesSeeder
REM Run with: scripts\test-course-dates-seeder.bat

echo 🌱 Running CourseDatesSeeder...
echo.

REM Run the specific seeder
php artisan db:seed --class=CourseDatesSeeder

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ CourseDatesSeeder completed successfully!
    echo.
    echo 📊 Checking course dates count...
    php artisan tinker --execute="echo 'Total course dates: ' . DB::table('course_dates')->count();"
) else (
    echo.
    echo ❌ Error running seeder!
    exit /b 1
)

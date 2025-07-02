# Laravel Cache Clearing Script
# This script clears all Laravel caches for development and testing

Write-Host "🧹 Laravel Cache Clearing Script" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

# Get the project root directory (one level up from scripts)
$projectRoot = Split-Path -Parent $PSScriptRoot

# Change to project directory
Set-Location $projectRoot
Write-Host "📂 Working in: $projectRoot" -ForegroundColor Yellow

# Check if artisan exists
if (-not (Test-Path "artisan")) {
    Write-Host "❌ Error: artisan file not found. Make sure you're in a Laravel project." -ForegroundColor Red
    exit 1
}

Write-Host "`n🔄 Clearing Laravel caches..." -ForegroundColor Green

# Clear application cache
Write-Host "   • Clearing application cache..." -ForegroundColor Gray
try {
    php artisan cache:clear
    Write-Host "   ✅ Application cache cleared" -ForegroundColor Green
} catch {
    Write-Host "   ❌ Failed to clear application cache" -ForegroundColor Red
}

# Clear configuration cache
Write-Host "   • Clearing configuration cache..." -ForegroundColor Gray
try {
    php artisan config:clear
    Write-Host "   ✅ Configuration cache cleared" -ForegroundColor Green
} catch {
    Write-Host "   ❌ Failed to clear configuration cache" -ForegroundColor Red
}

# Clear route cache
Write-Host "   • Clearing route cache..." -ForegroundColor Gray
try {
    php artisan route:clear
    Write-Host "   ✅ Route cache cleared" -ForegroundColor Green
} catch {
    Write-Host "   ❌ Failed to clear route cache" -ForegroundColor Red
}

# Clear view cache
Write-Host "   • Clearing view cache..." -ForegroundColor Gray
try {
    php artisan view:clear
    Write-Host "   ✅ View cache cleared" -ForegroundColor Green
} catch {
    Write-Host "   ❌ Failed to clear view cache" -ForegroundColor Red
}

# Clear compiled services
Write-Host "   • Clearing compiled services..." -ForegroundColor Gray
try {
    php artisan clear-compiled
    Write-Host "   ✅ Compiled services cleared" -ForegroundColor Green
} catch {
    Write-Host "   ❌ Failed to clear compiled services" -ForegroundColor Red
}

# Clear event cache (Laravel 8+)
Write-Host "   • Clearing event cache..." -ForegroundColor Gray
try {
    php artisan event:clear
    Write-Host "   ✅ Event cache cleared" -ForegroundColor Green
} catch {
    Write-Host "   ⚠️  Event cache clearing not available (Laravel version may not support it)" -ForegroundColor Yellow
}

# Clear schedule cache (Laravel 8+)
Write-Host "   • Clearing schedule cache..." -ForegroundColor Gray
try {
    php artisan schedule:clear-cache
    Write-Host "   ✅ Schedule cache cleared" -ForegroundColor Green
} catch {
    Write-Host "   ⚠️  Schedule cache clearing not available (Laravel version may not support it)" -ForegroundColor Yellow
}

# Optimize for development (recreate caches for faster performance)
Write-Host "`n⚡ Recreating optimized caches for development..." -ForegroundColor Green

# Recreate configuration cache
Write-Host "   • Recreating configuration cache..." -ForegroundColor Gray
try {
    php artisan config:cache
    Write-Host "   ✅ Configuration cache recreated" -ForegroundColor Green
} catch {
    Write-Host "   ❌ Failed to recreate configuration cache" -ForegroundColor Red
}

# Recreate route cache
Write-Host "   • Recreating route cache..." -ForegroundColor Gray
try {
    php artisan route:cache
    Write-Host "   ✅ Route cache recreated" -ForegroundColor Green
} catch {
    Write-Host "   ❌ Failed to recreate route cache" -ForegroundColor Red
}

Write-Host "`n🎉 Cache clearing completed!" -ForegroundColor Green
Write-Host "🚀 Your Laravel application is ready for testing." -ForegroundColor Cyan

# Optional: Show some useful information
Write-Host "`n📊 Useful information:" -ForegroundColor Blue
Write-Host "   • To check routes: php artisan route:list" -ForegroundColor Gray
Write-Host "   • To check config: php artisan config:show" -ForegroundColor Gray
Write-Host "   • To serve locally: php artisan serve" -ForegroundColor Gray

Write-Host "`nPress any key to exit..." -ForegroundColor Yellow
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

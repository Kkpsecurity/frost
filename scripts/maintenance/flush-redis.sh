#!/bin/bash

# Flush Redis Cache
# Clears all Redis cache after database sync

echo "🔄 Flushing Redis cache..."

# Check if Redis is running
if command -v redis-cli >/dev/null 2>&1; then
    # Try to flush Redis
    if redis-cli ping >/dev/null 2>&1; then
        redis-cli FLUSHALL
        echo "✅ Redis cache flushed successfully"
    else
        echo "⚠️  Redis server not responding, skipping cache flush"
    fi
else
    echo "⚠️  Redis CLI not found, skipping cache flush"
    echo "   If using Laravel cache, run: php artisan cache:clear"
fi

# Also clear Laravel cache
if [[ -f "artisan" ]]; then
    echo "🔄 Clearing Laravel cache..."
    
    # Clear various Laravel caches
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    echo "✅ Laravel cache cleared"
fi

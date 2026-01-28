<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentinelHealthCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_type',
        'status',
        'response_time',
        'details',
        'error_message',
    ];

    protected $casts = [
        'details' => 'array',
        'response_time' => 'integer',
    ];

    /**
     * Scope for healthy checks
     */
    public function scopeHealthy($query)
    {
        return $query->where('status', 'healthy');
    }

    /**
     * Scope for failed checks
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['degraded', 'down']);
    }

    /**
     * Scope by check type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('check_type', $type);
    }

    /**
     * Scope for recent checks
     */
    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Get latest status for a check type
     */
    public static function latestStatus(string $checkType): ?string
    {
        return static::where('check_type', $checkType)
            ->latest()
            ->value('status');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentinelEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'event_data',
        'severity',
        'sent_to_n8n',
        'n8n_response',
        'processed_at',
        'user_id',
    ];

    protected $casts = [
        'event_data' => 'array',
        'n8n_response' => 'array',
        'sent_to_n8n' => 'boolean',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user that triggered this event
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for pending events
     */
    public function scopePending($query)
    {
        return $query->where('sent_to_n8n', false);
    }

    /**
     * Scope for sent events
     */
    public function scopeSent($query)
    {
        return $query->where('sent_to_n8n', true);
    }

    /**
     * Scope by severity
     */
    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope by event type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('event_type', 'like', $type . '%');
    }

    /**
     * Scope for recent events
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}

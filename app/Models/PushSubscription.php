<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Stores a Web Push subscription for a single user+device combination.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $endpoint
 * @property string|null $public_key
 * @property string|null $auth_token
 * @property string|null $user_agent
 */
class PushSubscription extends Model
{
    protected $table = 'push_subscriptions';

    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_token',
        'user_agent',
    ];

    protected $hidden = [
        'public_key',
        'auth_token',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StudentVideoQuota Model
 * 
 * Tracks video quota allocation, usage, and refunds for students
 * in self-study mode.
 */
class StudentVideoQuota extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'student_video_quota';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'total_hours',
        'used_hours',
        'refunded_hours',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total_hours' => 'decimal:2',
        'used_hours' => 'decimal:2',
        'refunded_hours' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user that owns this quota record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Quota Calculation Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate remaining quota hours.
     * Formula: total + refunded - used
     *
     * @return float Remaining hours available
     */
    public function getRemainingHours(): float
    {
        return max(0, (float) $this->total_hours + (float) $this->refunded_hours - (float) $this->used_hours);
    }

    /**
     * Calculate remaining quota in minutes.
     *
     * @return int Remaining minutes available
     */
    public function getRemainingMinutes(): int
    {
        return (int) ($this->getRemainingHours() * 60);
    }

    /**
     * Get remaining quota as a percentage.
     *
     * @return float Percentage of quota remaining (0-100)
     */
    public function getRemainingPercentage(): float
    {
        $total = (float) $this->total_hours;
        if ($total <= 0) {
            return 0;
        }
        
        return ($this->getRemainingHours() / $total) * 100;
    }

    /*
    |--------------------------------------------------------------------------
    | Quota Validation Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if there's enough quota for a given duration in minutes.
     *
     * @param int $requiredMinutes Minutes needed
     * @return bool True if quota available
     */
    public function hasEnoughQuota(int $requiredMinutes): bool
    {
        return $this->getRemainingMinutes() >= $requiredMinutes;
    }

    /**
     * Check if quota is low (less than 10% remaining).
     *
     * @return bool True if quota is low
     */
    public function isQuotaLow(): bool
    {
        return $this->getRemainingPercentage() < 10;
    }

    /**
     * Check if quota is depleted.
     *
     * @return bool True if no quota remaining
     */
    public function isQuotaDepleted(): bool
    {
        return $this->getRemainingHours() <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Quota Management Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Consume quota for a completed lesson.
     *
     * @param int $minutes Minutes to deduct
     * @return bool True if consumption successful
     */
    public function consumeQuota(int $minutes): bool
    {
        if (!$this->hasEnoughQuota($minutes)) {
            return false;
        }

        $hours = $minutes / 60;
        $this->used_hours = (float) $this->used_hours + $hours;
        $this->save();

        return true;
    }

    /**
     * Refund quota (e.g., when online lesson passes after self-study redo).
     *
     * @param int $minutes Minutes to refund
     * @return void
     */
    public function refundQuota(int $minutes): void
    {
        $hours = $minutes / 60;
        $this->refunded_hours = (float) $this->refunded_hours + $hours;
        $this->save();
    }

    /**
     * Reset quota to default (10 hours).
     * WARNING: This clears all usage and refund history.
     *
     * @return void
     */
    public function resetQuota(): void
    {
        $this->total_hours = 10.00;
        $this->used_hours = 0.00;
        $this->refunded_hours = 0.00;
        $this->save();
    }

    /**
     * Add bonus hours to total quota.
     *
     * @param float $hours Hours to add
     * @return void
     */
    public function addBonusHours(float $hours): void
    {
        $this->total_hours = (float) $this->total_hours + $hours;
        $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor Attributes
    |--------------------------------------------------------------------------
    */

    /**
     * Get remaining hours as an attribute.
     */
    public function getRemainingHoursAttribute(): float
    {
        return $this->getRemainingHours();
    }

    /**
     * Get remaining minutes as an attribute.
     */
    public function getRemainingMinutesAttribute(): int
    {
        return $this->getRemainingMinutes();
    }

    /**
     * Get remaining percentage as an attribute.
     */
    public function getRemainingPercentageAttribute(): float
    {
        return $this->getRemainingPercentage();
    }
}

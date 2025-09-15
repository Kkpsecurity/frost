<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\NoString;

/**
 * @file ClassroomParticipant.php
 * @brief Model for classroom_participants table.
 * @details This model represents a participant in a classroom session.
 */
class ClassroomParticipant extends Model
{
    use NoString;

    protected $table = 'classroom_participants';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $casts = [
        'id' => 'integer',
        'classroom_id' => 'integer',
        'user_id' => 'integer',
        'role' => 'string',
        'status' => 'string',
        'joined_at' => 'timestamp',
        'last_activity' => 'timestamp',
        'metadata' => 'array',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $guarded = ['id'];

    protected $attributes = [
        'role' => 'student',
        'status' => 'enrolled',
    ];

    //
    // Relationships
    //

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //
    // Helper Methods
    //

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    public function isPresent(): bool
    {
        return in_array($this->status, ['present', 'late']);
    }

    public function markPresent(): void
    {
        $this->update([
            'status' => 'present',
            'joined_at' => now(),
            'last_activity' => now(),
        ]);
    }

    public function markAbsent(): void
    {
        $this->update(['status' => 'absent']);
    }
}

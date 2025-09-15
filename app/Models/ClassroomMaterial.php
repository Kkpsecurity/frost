<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\NoString;

/**
 * @file ClassroomMaterial.php
 * @brief Model for classroom_materials table.
 * @details This model represents materials/resources attached to a classroom session.
 */
class ClassroomMaterial extends Model
{
    use NoString;

    protected $table = 'classroom_materials';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $casts = [
        'id' => 'integer',
        'classroom_id' => 'integer',
        'type' => 'string',
        'title' => 'string',
        'description' => 'string',
        'file_path' => 'string',
        'url' => 'string',
        'is_required' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $guarded = ['id'];

    protected $attributes = [
        'is_required' => false,
        'sort_order' => 0,
        'is_active' => true,
    ];

    //
    // Relationships
    //

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    //
    // Helper Methods
    //

    public function isFile(): bool
    {
        return !empty($this->file_path);
    }

    public function isUrl(): bool
    {
        return !empty($this->url);
    }

    public function getAccessPath(): ?string
    {
        if ($this->isFile()) {
            return $this->file_path;
        }
        if ($this->isUrl()) {
            return $this->url;
        }
        return null;
    }

    //
    // Scopes
    //

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }
}

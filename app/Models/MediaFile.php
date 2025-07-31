<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    use HasFactory;

    protected $table = 'media_manager_files';

    protected $fillable = [
        'name',
        'original_name', 
        'path',
        'disk',
        'mime_type',
        'size',
        'collection',
        'metadata',
        'user_id'
    ];

    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer'
    ];

    public function getFormattedSizeAttribute(): string
    {
        return $this->formatBytes($this->size);
    }

    public function getUrlAttribute(): string
    {
        if ($this->disk === 'public') {
            return asset('storage/' . $this->path);
        }
        
        // For private files, return a secure URL
        return route('media.stream', ['file' => $this->id]);
    }

    public function formatBytes(int $size, int $precision = 2): string
    {
        if ($size === 0) return '0 B';

        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}

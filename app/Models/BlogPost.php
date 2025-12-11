<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'meta_description',
        'meta_keywords',
        'featured_image',
        'author',
        'category',
        'tags',
        'read_time',
        'views',
        'is_published',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $dates = [
        'published_at',
    ];

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        // Handle URL-friendly category matching
        // Convert dashes to spaces and match case-insensitively
        $categoryFormatted = ucwords(str_replace('-', ' ', $category));
        return $query->where('category', 'ILIKE', $categoryFormatted);
    }

    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }

    // Accessors
    public function getFormattedPublishedAtAttribute()
    {
        return $this->published_at ? $this->published_at->format('F j, Y') : null;
    }

    public function getReadTimeTextAttribute()
    {
        return $this->read_time . ' min read';
    }

    public function getExcerptOrTruncatedContentAttribute()
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        return substr(strip_tags($this->content), 0, 150) . '...';
    }

    // Mutators
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = $value ?: str()->slug($this->title);
    }

    // Route key name for route model binding
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Increment views
    public function incrementViews()
    {
        $this->increment('views');
    }
}

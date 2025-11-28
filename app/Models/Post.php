<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image_path',
        'featured_image_alt',
        'is_draft',
        'published_at',
        'likes_count',
        'views',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'published_at' => 'datetime',
        'is_draft' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function scopePublished($query)
    {
        return $query->where('is_draft', false)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return ! $this->is_draft && ! is_null($this->published_at) && $this->published_at->isPast();
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (!$this->featured_image_path) {
            return null;
        }
        
        // If it's already a full URL, return as is
        if (str_starts_with($this->featured_image_path, 'http')) {
            return $this->featured_image_path;
        }
        
        // Check if path already has 'storage/' prefix to avoid double storage/storage
        if (str_starts_with($this->featured_image_path, 'storage/')) {
             return asset($this->featured_image_path);
        }
        
        // Otherwise, prepend storage path
        return asset('storage/' . $this->featured_image_path);
    }
}

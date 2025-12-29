<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

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

    /**
     * Generate excerpt automatically from content
     * Returns first paragraph (if exists) or first 30 words
     */
    public function getExcerptAttribute(): string
    {
        // If excerpt is already stored in DB and not empty, use it (backward compatibility)
        if (!empty($this->attributes['excerpt'])) {
            return $this->attributes['excerpt'];
        }
        
        // If no content, return empty string
        if (empty($this->attributes['content'])) {
            return '';
        }
        
        $content = $this->attributes['content'];
        
        // Try to extract first paragraph
        if (preg_match('/<p[^>]*>(.*?)<\/p>/is', $content, $matches)) {
            $firstParagraph = $matches[1];
            
            // Strip ALL HTML tags to get plain text
            $cleaned = strip_tags($firstParagraph);
            
            // Remove extra whitespace and decode HTML entities
            $cleaned = html_entity_decode($cleaned, ENT_QUOTES, 'UTF-8');
            $cleaned = preg_replace('/\s+/', ' ', trim($cleaned));
            
            // Limit to 250 characters
            return Str::limit($cleaned, 250, '...');
        }
        
        // Fallback: return first 30 words with all tags stripped
        $plainText = strip_tags($content);
        $plainText = html_entity_decode($plainText, ENT_QUOTES, 'UTF-8');
        $plainText = preg_replace('/\s+/', ' ', trim($plainText));
        
        return Str::words($plainText, 30, '...');
    }
}

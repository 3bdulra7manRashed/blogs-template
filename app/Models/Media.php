<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'user_id',
        'filename',
        'disk',
        'path',
        'size',
        'mime_type',
        'caption',
        'alt_text',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'size' => 'integer',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    protected $fillable = [
        'user_id',
        'blog_category_id',
        'title',
        'slug',
        'content',
        'published_at',
        'seo_title',
        'seo_description',
        'featured_img',
        'is_visible',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'date',
        'is_visible' => 'boolean',
    ];

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function blogCategory() : BelongsTo
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function author() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ScopeVisible(Builder $query): void
    {
        $query->where('is_visible', true);
    }
    public function scopePostSingle(Builder $query) : void
    {
        $query->with(['author', 'blogCategory']);
    }

    public function scopeByPostSlug(Builder $query, string $slug): void
    {
        $query->where('slug', $slug);
    }
    
}

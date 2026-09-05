<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogPost extends Model
{
    protected $fillable = [
        'blog_category_id', 'user_id', 'title', 'slug', 'excerpt', 'content',
        'featured_image', 'featured_image_alt', 'status', 'published_at',
        'meta_title', 'meta_description', 'canonical_url', 'og_title', 'og_description',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function readingTime(): int
    {
        $words = str_word_count(strip_tags($this->content));

        return max(1, (int) ceil($words / 200));
    }
}

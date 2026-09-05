<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = [
        'title', 'description', 'before_image', 'after_image',
        'service', 'category', 'is_featured', 'display_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function beforeImageUrl(): ?string
    {
        return app(\App\Services\ServiceImageService::class)->url($this->before_image);
    }

    public function afterImageUrl(): ?string
    {
        return app(\App\Services\ServiceImageService::class)->url($this->after_image);
    }
}

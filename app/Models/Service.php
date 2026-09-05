<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = [
        'service_category_id', 'name', 'slug', 'short_description', 'description',
        'price', 'duration_minutes', 'image', 'is_featured', 'featured_order',
        'is_active', 'display_order', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->orderBy('featured_order');
    }

    public function formattedPrice(): string
    {
        return '₹'.number_format($this->price, 0);
    }

    public function formattedDuration(): string
    {
        $totalMinutes = $this->duration_minutes;
        $days = intdiv($totalMinutes, 24 * 60);
        $hours = intdiv($totalMinutes % (24 * 60), 60);
        $minutes = $totalMinutes % 60;

        $parts = [];
        if ($days > 0) {
            $parts[] = $days.' day'.($days > 1 ? 's' : '');
        }
        if ($hours > 0) {
            $parts[] = $hours.' hour'.($hours > 1 ? 's' : '');
        }
        if ($minutes > 0) {
            $parts[] = $minutes.' minute'.($minutes > 1 ? 's' : '');
        }

        return $parts ? implode(', ', $parts) : '0 minutes';
    }

    public function imageUrl(): string
    {
        if ($this->image) {
            return str_starts_with($this->image, 'http') ? $this->image : asset($this->image);
        }

        $defaults = [
            'basic-wash' => 'images/car-wash.jpg',
            'premium-steam-wash' => 'images/steam-wash.jpg',
            'interior-deep-clean' => 'images/interior-detailing.jpg',
            'full-detailing' => 'images/full-detailing.jpg',
            'engine-bay-steam-clean' => 'images/steam-wash.jpg',
            'ceramic-coating' => 'images/ceramic-coating.jpg',
            'paint-protection-film' => 'images/ppf.jpg',
            'paint-correction' => 'images/detailing.jpg',
            'headlight-restoration' => 'images/exterior-detailing.jpg',
            'alloy-wheel-care' => 'images/car-wash.jpg',
            'leather-conditioning' => 'images/interior-detail.jpg',
            'monsoon-protection-package' => 'images/premium-wash.jpg',
        ];

        return asset($defaults[$this->slug] ?? 'images/car-wash.jpg');
    }
}

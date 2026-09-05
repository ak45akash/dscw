<?php

namespace App\Models;

use App\Support\Duration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(ServiceAddon::class, 'service_service_addon')
            ->orderBy('display_order')
            ->orderBy('name');
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
        return Duration::format((int) $this->duration_minutes);
    }

    public function hasCustomImage(): bool
    {
        return filled($this->image);
    }

    public function imageUrl(): string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }

            if (str_starts_with($this->image, 'services/') || str_starts_with($this->image, 'media/')) {
                return Storage::disk(\App\Services\MediaDisk::name())->url($this->image);
            }

            return asset($this->image);
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

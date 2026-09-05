<?php

namespace App\Models;

use App\Support\Duration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceAddon extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration_minutes',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_service_addon');
    }

    public function bookingLines(): HasMany
    {
        return $this->hasMany(BookingServiceAddon::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function formattedPrice(): string
    {
        return '₹'.number_format((float) $this->price, 0);
    }

    public function formattedDuration(): string
    {
        if ((int) $this->duration_minutes <= 0) {
            return 'No extra time';
        }

        return '+'.Duration::format((int) $this->duration_minutes);
    }
}

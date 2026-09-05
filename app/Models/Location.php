<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name', 'slug', 'phone', 'email', 'address', 'city', 'state', 'pincode',
        'latitude', 'longitude', 'is_active', 'is_default', 'display_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(LocationWorkingHour::class)->orderBy('day_of_week');
    }

    public function blockedDates(): HasMany
    {
        return $this->hasMany(BlockedDate::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }

    public function fullAddress(): string
    {
        return collect([$this->address, $this->city, $this->state, $this->pincode])
            ->filter()
            ->implode(', ');
    }
}

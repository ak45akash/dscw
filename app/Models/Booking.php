<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_NO_SHOW = 'no_show';

    public const PAYMENT_ONLINE = 'online';

    public const PAYMENT_AT_LOCATION = 'at_location';

    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_PENDING = 'pending';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_FAILED = 'failed';

    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'reference', 'location_id', 'service_id', 'customer_name', 'customer_email',
        'customer_phone', 'vehicle_make_model', 'vehicle_plate', 'notes',
        'booking_date', 'start_time', 'end_time', 'duration_minutes', 'price',
        'status', 'payment_method', 'payment_status', 'razorpay_order_id',
        'razorpay_payment_id', 'confirmed_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'price' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('booking_date', today());
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED], true);
    }

    public function formattedPrice(): string
    {
        return '₹'.number_format((float) $this->price, 0);
    }

    public function statusLabel(): string
    {
        return str_replace('_', ' ', ucfirst($this->status));
    }
}

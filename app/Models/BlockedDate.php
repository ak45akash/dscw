<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedDate extends Model
{
    protected $fillable = [
        'location_id', 'date', 'reason', 'is_full_day', 'start_time', 'end_time',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_full_day' => 'boolean',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}

<?php

namespace App\Services;

use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    public function __construct(private SettingsService $settings) {}

    /**
     * @return array<int, array{start: string, end: string, label: string}>
     */
    public function slotsFor(Location $location, Service $service, Carbon $date): array
    {
        if (! $this->settings->get('booking', 'online_bookings_enabled', true)) {
            return [];
        }

        $today = now()->startOfDay();
        $maxAdvance = (int) $this->settings->get('booking', 'max_advance_days', 30);
        $sameDay = (bool) $this->settings->get('booking', 'same_day_bookings', true);

        if ($date->lt($today)) {
            return [];
        }

        if (! $sameDay && $date->isToday()) {
            return [];
        }

        if ($date->gt($today->copy()->addDays($maxAdvance))) {
            return [];
        }

        $dayOfWeek = (int) $date->dayOfWeek;
        $hours = $location->workingHours()->where('day_of_week', $dayOfWeek)->first();

        if (! $hours || $hours->is_closed || ! $hours->opens_at || ! $hours->closes_at) {
            return [];
        }

        if ($this->isFullyBlocked($location->id, $date)) {
            return [];
        }

        $interval = max(15, (int) $this->settings->get('booking', 'slot_interval_minutes', 60));
        $buffer = (int) $this->settings->get('booking', 'buffer_minutes', 15);
        $maxPerSlot = (int) $this->settings->get('booking', 'max_bookings_per_slot', 2);
        $maxPerDay = (int) $this->settings->get('booking', 'max_bookings_per_day', 20);
        $duration = (int) $service->duration_minutes;

        $dayBookings = Booking::query()
            ->where('location_id', $location->id)
            ->whereDate('booking_date', $date->toDateString())
            ->active()
            ->get();

        if ($dayBookings->count() >= $maxPerDay) {
            return [];
        }

        $partialBlocks = $this->partialBlocks($location->id, $date);

        $open = Carbon::parse($date->toDateString().' '.$hours->opens_at);
        $close = Carbon::parse($date->toDateString().' '.$hours->closes_at);
        $now = now();

        $slots = [];
        $cursor = $open->copy();

        while ($cursor->copy()->addMinutes($duration)->lte($close)) {
            $slotStart = $cursor->copy();
            $slotEnd = $cursor->copy()->addMinutes($duration);

            $cursor->addMinutes($interval);

            if ($date->isToday() && $slotStart->lte($now)) {
                continue;
            }

            if ($this->overlapsPartialBlock($slotStart, $slotEnd, $partialBlocks)) {
                continue;
            }

            $overlapping = $dayBookings->filter(function (Booking $booking) use ($slotStart, $slotEnd, $buffer) {
                $existingStart = Carbon::parse($booking->booking_date->toDateString().' '.$booking->start_time);
                $existingEnd = Carbon::parse($booking->booking_date->toDateString().' '.$booking->end_time)
                    ->addMinutes($buffer);

                $proposedEnd = $slotEnd->copy()->addMinutes($buffer);

                return $slotStart->lt($existingEnd) && $proposedEnd->gt($existingStart);
            });

            if ($overlapping->count() >= $maxPerSlot) {
                continue;
            }

            $slots[] = [
                'start' => $slotStart->format('H:i'),
                'end' => $slotEnd->format('H:i'),
                'label' => $slotStart->format('g:i A').' – '.$slotEnd->format('g:i A'),
            ];
        }

        return $slots;
    }

    private function isFullyBlocked(int $locationId, Carbon $date): bool
    {
        return BlockedDate::query()
            ->whereDate('date', $date->toDateString())
            ->where('is_full_day', true)
            ->where(function ($q) use ($locationId) {
                $q->whereNull('location_id')->orWhere('location_id', $locationId);
            })
            ->exists();
    }

    private function partialBlocks(int $locationId, Carbon $date): Collection
    {
        return BlockedDate::query()
            ->whereDate('date', $date->toDateString())
            ->where('is_full_day', false)
            ->where(function ($q) use ($locationId) {
                $q->whereNull('location_id')->orWhere('location_id', $locationId);
            })
            ->get();
    }

    private function overlapsPartialBlock(Carbon $start, Carbon $end, Collection $blocks): bool
    {
        foreach ($blocks as $block) {
            if (! $block->start_time || ! $block->end_time) {
                continue;
            }

            $blockStart = Carbon::parse($start->toDateString().' '.$block->start_time);
            $blockEnd = Carbon::parse($start->toDateString().' '.$block->end_time);

            if ($start->lt($blockEnd) && $end->gt($blockStart)) {
                return true;
            }
        }

        return false;
    }
}

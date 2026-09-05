<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingCalendarController extends Controller
{
    public function __invoke(Request $request): View
    {
        $month = Carbon::parse($request->input('month', now()->format('Y-m')))->startOfMonth();
        $locationId = $request->integer('location_id') ?: null;

        $start = $month->copy()->startOfWeek(Carbon::MONDAY);
        $end = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $bookings = Booking::query()
            ->with(['service', 'location'])
            ->whereBetween('booking_date', [$start->toDateString(), $end->toDateString()])
            ->when($locationId, fn ($q) => $q->where('location_id', $locationId))
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn (Booking $b) => $b->booking_date->toDateString());

        $blocked = BlockedDate::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->when($locationId, fn ($q) => $q->where(function ($query) use ($locationId) {
                $query->whereNull('location_id')->orWhere('location_id', $locationId);
            }))
            ->get()
            ->groupBy(fn (BlockedDate $b) => $b->date->toDateString());

        $days = collect();
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $key = $day->toDateString();
            $days->push([
                'date' => $day->copy(),
                'inMonth' => $day->month === $month->month,
                'bookings' => $bookings->get($key, collect()),
                'blocked' => $blocked->get($key, collect()),
            ]);
        }

        return view('admin.bookings.calendar', [
            'month' => $month,
            'days' => $days,
            'locations' => Location::query()->orderBy('name')->get(),
            'locationId' => $locationId,
            'prevMonth' => $month->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $month->copy()->addMonth()->format('Y-m'),
        ]);
    }
}

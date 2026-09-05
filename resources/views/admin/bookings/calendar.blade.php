<x-layouts.admin title="Booking Calendar" breadcrumb="Dashboard / Calendar">
    @php
        $statusColors = [
            'pending' => 'booking-cal-event--pending',
            'confirmed' => 'booking-cal-event--confirmed',
            'completed' => 'booking-cal-event--completed',
            'no_show' => 'booking-cal-event--noshow',
        ];
    @endphp

    <div class="booking-cal">
        <div class="booking-cal__toolbar">
            <div class="booking-cal__nav">
                <a
                    href="{{ route('admin.bookings.calendar', ['month' => $prevMonth, 'location_id' => $locationId]) }}"
                    class="booking-cal__nav-btn"
                    aria-label="Previous month"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h2 class="booking-cal__title">{{ $month->format('F Y') }}</h2>
                <a
                    href="{{ route('admin.bookings.calendar', ['month' => $nextMonth, 'location_id' => $locationId]) }}"
                    class="booking-cal__nav-btn"
                    aria-label="Next month"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a
                    href="{{ route('admin.bookings.calendar', ['month' => $todayMonth, 'location_id' => $locationId]) }}"
                    class="booking-cal__today"
                >Today</a>
            </div>

            <div class="booking-cal__meta">
                <span class="booking-cal__stat">{{ $monthBookingCount }} {{ \Illuminate\Support\Str::plural('booking', $monthBookingCount) }}</span>
                @if($monthBlockedCount > 0)
                    <span class="booking-cal__stat booking-cal__stat--blocked">{{ $monthBlockedCount }} blocked {{ \Illuminate\Support\Str::plural('day', $monthBlockedCount) }}</span>
                @endif
                <form method="GET" class="booking-cal__filter">
                    <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
                    <label for="location_id" class="sr-only">Location</label>
                    <select name="location_id" id="location_id" class="form-input booking-cal__select" onchange="this.form.submit()">
                        <option value="">All locations</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" @selected($locationId == $location->id)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="booking-cal__legend">
            <span><i class="booking-cal-dot booking-cal-dot--confirmed"></i> Confirmed</span>
            <span><i class="booking-cal-dot booking-cal-dot--pending"></i> Pending</span>
            <span><i class="booking-cal-dot booking-cal-dot--completed"></i> Completed</span>
            <span><i class="booking-cal-dot booking-cal-dot--noshow"></i> No-show</span>
            <span><i class="booking-cal-dot booking-cal-dot--blocked"></i> Blocked</span>
        </div>

        <div class="booking-cal__frame">
            <div class="booking-cal__weekdays" aria-hidden="true">
                @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $label)
                    <div class="booking-cal__weekday">{{ $label }}</div>
                @endforeach
            </div>

            <div class="booking-cal__grid" role="grid" aria-label="{{ $month->format('F Y') }} bookings">
                @foreach($days as $day)
                    @php
                        $count = $day['bookings']->count();
                        $visible = $day['bookings']->take(3);
                        $extra = max(0, $count - 3);
                    @endphp
                    <div
                        role="gridcell"
                        @class([
                            'booking-cal__day',
                            'booking-cal__day--muted' => ! $day['inMonth'],
                            'booking-cal__day--today' => $day['isToday'],
                            'booking-cal__day--blocked' => $day['blocked']->isNotEmpty(),
                            'booking-cal__day--busy' => $count > 0,
                        ])
                    >
                        <div class="booking-cal__day-head">
                            <span @class(['booking-cal__date', 'booking-cal__date--today' => $day['isToday']])>
                                {{ $day['date']->day }}
                            </span>
                            @if($day['blocked']->isNotEmpty())
                                <span class="booking-cal__blocked-tag" title="{{ $day['blocked']->pluck('reason')->filter()->implode(', ') ?: 'Blocked' }}">Blocked</span>
                            @elseif($count > 0)
                                <span class="booking-cal__count">{{ $count }}</span>
                            @endif
                        </div>

                        <div class="booking-cal__events">
                            @foreach($visible as $booking)
                                <a
                                    href="{{ route('admin.bookings.show', $booking) }}"
                                    class="booking-cal-event {{ $statusColors[$booking->status] ?? 'booking-cal-event--confirmed' }}"
                                    title="{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} · {{ $booking->customer_name }} · {{ $booking->service?->name }} ({{ $booking->statusLabel() }})"
                                >
                                    <span class="booking-cal-event__time">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}</span>
                                    <span class="booking-cal-event__label">{{ $booking->service?->name ?? 'Booking' }}</span>
                                </a>
                            @endforeach

                            @if($extra > 0)
                                <a
                                    href="{{ route('admin.bookings.index', ['date' => $day['key'], 'location_id' => $locationId]) }}"
                                    class="booking-cal__more"
                                >+{{ $extra }} more</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.admin>

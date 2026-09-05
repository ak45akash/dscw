<x-layouts.admin title="Booking Calendar" breadcrumb="Dashboard / Calendar">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.bookings.calendar', ['month' => $prevMonth, 'location_id' => $locationId]) }}" class="btn-secondary">Previous</a>
            <h2 class="text-lg font-semibold">{{ $month->format('F Y') }}</h2>
            <a href="{{ route('admin.bookings.calendar', ['month' => $nextMonth, 'location_id' => $locationId]) }}" class="btn-secondary">Next</a>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
            <select name="location_id" class="form-input w-auto" onchange="this.form.submit()">
                <option value="">All locations</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" @selected($locationId == $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="grid grid-cols-7 gap-px overflow-hidden rounded-xl border border-graphite-200 bg-graphite-200 dark:border-graphite-800 dark:bg-graphite-800">
        @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $label)
            <div class="bg-graphite-50 px-2 py-2 text-center text-xs font-semibold uppercase tracking-wide text-graphite-500 dark:bg-graphite-900">{{ $label }}</div>
        @endforeach
        @foreach($days as $day)
            <div @class([
                'min-h-28 bg-white p-2 dark:bg-graphite-950',
                'opacity-50' => ! $day['inMonth'],
            ])>
                <div class="mb-1 flex items-center justify-between text-xs">
                    <span class="font-semibold">{{ $day['date']->day }}</span>
                    @if($day['blocked']->isNotEmpty())
                        <span class="rounded bg-red-100 px-1 text-[10px] font-medium text-red-700 dark:bg-red-900/40 dark:text-red-200">Blocked</span>
                    @endif
                </div>
                <div class="space-y-1">
                    @foreach($day['bookings']->take(4) as $booking)
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="block truncate rounded bg-blue-50 px-1.5 py-0.5 text-[11px] text-blue-800 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-100">
                            {{ substr((string) $booking->start_time, 0, 5) }} · {{ $booking->service?->name }}
                        </a>
                    @endforeach
                    @if($day['bookings']->count() > 4)
                        <p class="text-[10px] text-graphite-500">+{{ $day['bookings']->count() - 4 }} more</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.admin>

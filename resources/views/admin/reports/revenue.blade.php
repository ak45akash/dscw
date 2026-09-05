<x-layouts.admin title="Revenue" breadcrumb="Reports / Revenue">
    <form method="GET" class="mb-6 grid gap-3 rounded-xl border border-graphite-200 bg-white p-4 dark:border-graphite-800 dark:bg-graphite-900 sm:grid-cols-5">
        <div>
            <x-label for="from">From</x-label>
            <x-input type="date" name="from" id="from" :value="$filters['from']" />
        </div>
        <div>
            <x-label for="to">To</x-label>
            <x-input type="date" name="to" id="to" :value="$filters['to']" />
        </div>
        <div>
            <x-label for="location_id">Location</x-label>
            <select name="location_id" id="location_id" class="form-input">
                <option value="">All</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" @selected(($filters['location_id'] ?? '') == $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2 sm:col-span-2">
            <x-button type="submit" class="flex-1">Filter</x-button>
            <a href="{{ route('admin.reports.revenue', array_merge($filters, ['export' => 1])) }}" class="btn-secondary">CSV (paid)</a>
        </div>
    </form>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card title="Paid revenue" :value="'₹'.number_format($paidTotal, 0)" :hint="$paidCount.' paid bookings'" />
        <x-stat-card title="Unpaid at location" :value="'₹'.number_format($unpaidTotal, 0)" :hint="$unpaidCount.' confirmed/completed unpaid'" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card class="overflow-hidden p-0">
            <div class="border-b border-graphite-200 px-4 py-3 font-semibold dark:border-graphite-800">By day (paid)</div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-graphite-50 dark:bg-graphite-900/50"><tr><th class="px-4 py-2 text-left">Date</th><th class="px-4 py-2 text-right">Bookings</th><th class="px-4 py-2 text-right">Total</th></tr></thead>
                    <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                        @forelse($byDay as $row)
                            <tr>
                                <td class="px-4 py-2">{{ \Illuminate\Support\Carbon::parse($row->booking_date)->toDateString() }}</td>
                                <td class="px-4 py-2 text-right">{{ $row->count }}</td>
                                <td class="px-4 py-2 text-right">₹{{ number_format((float) $row->total, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-graphite-500">No paid bookings.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card class="overflow-hidden p-0">
            <div class="border-b border-graphite-200 px-4 py-3 font-semibold dark:border-graphite-800">By service (paid)</div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-graphite-50 dark:bg-graphite-900/50"><tr><th class="px-4 py-2 text-left">Service</th><th class="px-4 py-2 text-right">Bookings</th><th class="px-4 py-2 text-right">Total</th></tr></thead>
                    <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                        @forelse($byService as $row)
                            <tr>
                                <td class="px-4 py-2">{{ $row->service_name }}</td>
                                <td class="px-4 py-2 text-right">{{ $row->count }}</td>
                                <td class="px-4 py-2 text-right">₹{{ number_format((float) $row->total, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-graphite-500">No paid bookings.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card class="overflow-hidden p-0 lg:col-span-2">
            <div class="border-b border-graphite-200 px-4 py-3 font-semibold dark:border-graphite-800">By location (paid)</div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-graphite-50 dark:bg-graphite-900/50"><tr><th class="px-4 py-2 text-left">Location</th><th class="px-4 py-2 text-right">Bookings</th><th class="px-4 py-2 text-right">Total</th></tr></thead>
                    <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                        @forelse($byLocation as $row)
                            <tr>
                                <td class="px-4 py-2">{{ $row->location_name }}</td>
                                <td class="px-4 py-2 text-right">{{ $row->count }}</td>
                                <td class="px-4 py-2 text-right">₹{{ number_format((float) $row->total, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-graphite-500">No paid bookings.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.admin>

<x-layouts.admin title="Booking Reports" breadcrumb="Reports / Bookings">
    <form method="GET" class="mb-6 grid gap-3 rounded-xl border border-graphite-200 bg-white p-4 dark:border-graphite-800 dark:bg-graphite-900 sm:grid-cols-6">
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
        <div>
            <x-label for="status">Status</x-label>
            <select name="status" id="status" class="form-input">
                <option value="">All</option>
                @foreach(['pending','confirmed','completed','cancelled','no_show'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst(str_replace('_',' ', $status)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-label for="payment_status">Payment</x-label>
            <select name="payment_status" id="payment_status" class="form-input">
                <option value="">All</option>
                @foreach(['unpaid','pending','paid','failed','refunded'] as $payment)
                    <option value="{{ $payment }}" @selected(($filters['payment_status'] ?? '') === $payment)>{{ ucfirst($payment) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <x-button type="submit" class="flex-1">Filter</x-button>
            <a href="{{ route('admin.reports.bookings', array_merge($filters, ['export' => 1])) }}" class="btn-secondary">CSV</a>
        </div>
    </form>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card title="Total bookings" :value="$total" />
        @foreach(['pending','confirmed','completed','cancelled'] as $status)
            <x-stat-card :title="ucfirst($status)" :value="$byStatus[$status] ?? 0" />
        @endforeach
    </div>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Reference</th>
                        <th class="px-4 py-3 text-left font-semibold">When</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Service</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="px-4 py-3"><a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:underline">{{ $booking->reference }}</a></td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $booking->booking_date->format('Y-m-d') }} {{ substr((string) $booking->start_time, 0, 5) }}</td>
                            <td class="px-4 py-3">{{ $booking->customer_name }}</td>
                            <td class="px-4 py-3">{{ $booking->service?->name }}</td>
                            <td class="px-4 py-3">{{ $booking->statusLabel() }}</td>
                            <td class="px-4 py-3 text-right">{{ $booking->formattedPrice() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-graphite-500">No bookings in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
            <div class="border-t border-graphite-200 p-4 dark:border-graphite-800">{{ $bookings->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

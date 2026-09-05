<x-layouts.admin :title="$title" breadcrumb="Bookings">
    <form method="GET" class="mb-6 grid gap-3 rounded-xl border border-graphite-200 bg-white p-4 dark:border-graphite-800 dark:bg-graphite-900 sm:grid-cols-4">
        <div>
            <x-label for="date">Date</x-label>
            <x-input type="date" name="date" id="date" :value="$filters['date'] ?? ''" />
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
            <x-label for="location_id">Location</x-label>
            <select name="location_id" id="location_id" class="form-input">
                <option value="">All</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" @selected(($filters['location_id'] ?? '') == $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <x-button type="submit" class="w-full">Filter</x-button>
        </div>
    </form>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Reference</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Service</th>
                        <th class="px-4 py-3 text-left font-semibold">When</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs">{{ $booking->reference }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $booking->customer_name }}</div>
                                <div class="text-xs text-graphite-500">{{ $booking->customer_phone }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div>{{ $booking->service?->name }}</div>
                                <div class="text-xs text-graphite-500">{{ $booking->location?->name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                {{ $booking->booking_date->format('d M Y') }}
                                <div class="text-xs text-graphite-500">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}</div>
                            </td>
                            <td class="px-4 py-3"><x-badge color="blue">{{ $booking->statusLabel() }}</x-badge></td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:underline">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-graphite-500">No bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
            <div class="border-t border-graphite-200 p-4 dark:border-graphite-800">{{ $bookings->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

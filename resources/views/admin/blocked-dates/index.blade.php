<x-layouts.admin title="Blocked Dates" breadcrumb="Bookings / Blocked Dates">
    <div class="mb-6 flex justify-end">
        <x-button href="{{ route('admin.blocked-dates.create') }}">Add Blocked Date</x-button>
    </div>
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Location</th>
                        <th class="px-4 py-3 text-left font-semibold">Scope</th>
                        <th class="px-4 py-3 text-left font-semibold">Reason</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($blockedDates as $blocked)
                        <tr>
                            <td class="px-4 py-3">{{ $blocked->date->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $blocked->location?->name ?? 'All locations' }}</td>
                            <td class="px-4 py-3">{{ $blocked->is_full_day ? 'Full day' : substr($blocked->start_time,0,5).'–'.substr($blocked->end_time,0,5) }}</td>
                            <td class="px-4 py-3">{{ $blocked->reason ?? '—' }}</td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <a href="{{ route('admin.blocked-dates.edit', $blocked) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.blocked-dates.destroy', $blocked) }}" class="inline" onsubmit="return confirm('Remove this block?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-graphite-500">No blocked dates.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($blockedDates->hasPages())
            <div class="border-t border-graphite-200 p-4 dark:border-graphite-800">{{ $blockedDates->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

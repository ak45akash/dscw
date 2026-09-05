<x-layouts.admin title="Contact Enquiries" breadcrumb="Content / Enquiries">
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.enquiries.index') }}" @class(['btn-secondary', 'ring-2 ring-blue-500' => ! request('status')])>All</a>
        @foreach(['new', 'read', 'archived'] as $status)
            <a href="{{ route('admin.enquiries.index', ['status' => $status]) }}" @class(['btn-secondary', 'ring-2 ring-blue-500' => request('status') === $status])>{{ ucfirst($status) }}</a>
        @endforeach
    </div>
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">From</th>
                        <th class="px-4 py-3 text-left font-semibold">Subject</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Received</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($enquiries as $enquiry)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $enquiry->name }}</div>
                                <div class="text-xs text-graphite-500">{{ $enquiry->email }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $enquiry->subject ?: '—' }}</td>
                            <td class="px-4 py-3"><x-badge color="gray">{{ $enquiry->status }}</x-badge></td>
                            <td class="px-4 py-3">{{ $enquiry->created_at?->format('M j, Y H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-graphite-500">No enquiries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($enquiries->hasPages())
            <div class="border-t border-graphite-200 p-4 dark:border-graphite-800">{{ $enquiries->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

<x-layouts.admin title="Gallery" breadcrumb="Content / Gallery">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-graphite-500">Before-and-after gallery items for the public gallery page.</p>
        <x-button href="{{ route('admin.gallery-items.create') }}">Add Gallery Item</x-button>
    </div>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Title</th>
                        <th class="px-4 py-3 text-left font-semibold">Category</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ \Illuminate\Support\Str::limit($item->title, 80) }}</div>
                                @if($item->is_featured)
                                    <x-badge color="blue">Featured</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $item->category ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <x-badge :color="$item->is_active ? 'green' : 'gray'">{{ $item->is_active ? 'Active' : 'Inactive' }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <a href="{{ route('admin.gallery-items.edit', $item) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.gallery-items.destroy', $item) }}" class="inline" onsubmit="return confirm('Delete this gallery item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-graphite-500">No gallery items yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
            <div class="border-t border-graphite-200 p-4 dark:border-graphite-800">{{ $items->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

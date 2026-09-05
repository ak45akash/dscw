<x-layouts.admin title="Service Add-ons" breadcrumb="Services / Add-ons">
    <div class="mb-6 flex justify-end">
        <x-button href="{{ route('admin.service-addons.create') }}">Add add-on</x-button>
    </div>
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Price</th>
                        <th class="px-4 py-3 text-left font-semibold">Extra time</th>
                        <th class="px-4 py-3 text-left font-semibold">Services</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($addons as $addon)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $addon->name }}</td>
                            <td class="px-4 py-3">{{ $addon->formattedPrice() }}</td>
                            <td class="px-4 py-3">{{ $addon->formattedDuration() }}</td>
                            <td class="px-4 py-3">{{ $addon->services()->count() }}</td>
                            <td class="px-4 py-3">
                                <x-badge :color="$addon->is_active ? 'green' : 'gray'">{{ $addon->is_active ? 'Active' : 'Inactive' }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <a href="{{ route('admin.service-addons.edit', $addon) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.service-addons.destroy', $addon) }}" class="inline" onsubmit="return confirm('Delete this add-on?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-graphite-500">No add-ons yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($addons->hasPages())
            <div class="border-t border-graphite-200 px-4 py-3 dark:border-graphite-800">{{ $addons->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

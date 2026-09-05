<x-layouts.admin title="Services" breadcrumb="Services">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-graphite-500">Manage service catalog shown on the public website and booking form.</p>
        <x-button href="{{ route('admin.services.create') }}">Add Service</x-button>
    </div>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Service</th>
                        <th class="px-4 py-3 text-left font-semibold">Category</th>
                        <th class="px-4 py-3 text-left font-semibold">Price</th>
                        <th class="px-4 py-3 text-left font-semibold">Duration</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($services as $service)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img
                                        src="{{ $service->imageUrl() }}"
                                        alt=""
                                        class="h-12 w-16 shrink-0 rounded object-cover"
                                    >
                                    <div>
                                        <div class="font-medium">{{ $service->name }}</div>
                                        @if($service->is_featured)
                                            <x-badge color="blue">Featured</x-badge>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $service->category?->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $service->formattedPrice() }}</td>
                            <td class="px-4 py-3">{{ $service->formattedDuration() }}</td>
                            <td class="px-4 py-3">
                                <x-badge :color="$service->is_active ? 'green' : 'gray'">{{ $service->is_active ? 'Active' : 'Inactive' }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.services.edit', $service) }}" class="text-blue-600 hover:underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-graphite-500">No services yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($services->hasPages())
            <div class="border-t border-graphite-200 p-4 dark:border-graphite-800">{{ $services->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

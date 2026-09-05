<x-layouts.admin title="Service Categories" breadcrumb="Services / Categories">
    <div class="mb-6 flex justify-end">
        <x-button href="{{ route('admin.service-categories.create') }}">Add Category</x-button>
    </div>
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Services</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                            <td class="px-4 py-3">{{ $category->services_count }}</td>
                            <td class="px-4 py-3"><x-badge :color="$category->is_active ? 'green' : 'gray'">{{ $category->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('admin.service-categories.edit', $category) }}" class="text-blue-600 hover:underline">Edit</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-graphite-500">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.admin>

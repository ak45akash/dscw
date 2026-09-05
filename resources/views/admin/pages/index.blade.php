<x-layouts.admin title="Pages" breadcrumb="Content / Pages">
    <div class="mb-6 flex justify-end">
        <x-button href="{{ route('admin.pages.create') }}">Add Page</x-button>
    </div>
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Title</th>
                        <th class="px-4 py-3 text-left font-semibold">Slug</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($pages as $page)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $page->title }}</td>
                            <td class="px-4 py-3 text-graphite-500">{{ $page->slug }}</td>
                            <td class="px-4 py-3"><x-badge :color="$page->is_active ? 'green' : 'gray'">{{ $page->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.pages.edit', $page) }}" class="text-blue-600 hover:underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-graphite-500">No pages yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pages->hasPages())
            <div class="border-t border-graphite-200 p-4 dark:border-graphite-800">{{ $pages->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

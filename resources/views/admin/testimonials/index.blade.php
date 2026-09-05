<x-layouts.admin title="Testimonials" breadcrumb="Content / Testimonials">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-graphite-500">Customer reviews shown on the homepage and related pages.</p>
        <x-button href="{{ route('admin.testimonials.create') }}">Add Testimonial</x-button>
    </div>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Review</th>
                        <th class="px-4 py-3 text-left font-semibold">Rating</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($testimonials as $testimonial)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $testimonial->name }}</div>
                                @if($testimonial->is_featured)
                                    <x-badge color="blue">Featured</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($testimonial->review, 80) }}</td>
                            <td class="px-4 py-3">{{ $testimonial->rating }}/5</td>
                            <td class="px-4 py-3">
                                <x-badge :color="$testimonial->is_active ? 'green' : 'gray'">{{ $testimonial->is_active ? 'Active' : 'Inactive' }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" class="inline" onsubmit="return confirm('Delete this testimonial?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-graphite-500">No testimonials yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($testimonials->hasPages())
            <div class="border-t border-graphite-200 p-4 dark:border-graphite-800">{{ $testimonials->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

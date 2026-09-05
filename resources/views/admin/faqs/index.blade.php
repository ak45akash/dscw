<x-layouts.admin title="FAQs" breadcrumb="Content / FAQs">
    <div class="mb-6 flex justify-end">
        <x-button href="{{ route('admin.faqs.create') }}">Add FAQ</x-button>
    </div>
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Question</th>
                        <th class="px-4 py-3 text-left font-semibold">Category</th>
                        <th class="px-4 py-3 text-left font-semibold">Order</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($faqs as $faq)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ Str::limit($faq->question, 80) }}</td>
                            <td class="px-4 py-3">{{ $faq->category ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $faq->display_order }}</td>
                            <td class="px-4 py-3"><x-badge :color="$faq->is_active ? 'green' : 'gray'">{{ $faq->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" class="inline" onsubmit="return confirm('Delete this FAQ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-graphite-500">No FAQs yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($faqs->hasPages())
            <div class="border-t border-graphite-200 p-4 dark:border-graphite-800">{{ $faqs->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

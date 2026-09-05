<x-layouts.admin title="Coupons" breadcrumb="Marketing / Coupons">
    <div class="mb-6 flex justify-end">
        <x-button href="{{ route('admin.coupons.create') }}">Add Coupon</x-button>
    </div>
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Code</th>
                        <th class="px-4 py-3 text-left font-semibold">Type</th>
                        <th class="px-4 py-3 text-left font-semibold">Value</th>
                        <th class="px-4 py-3 text-left font-semibold">Uses</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($coupons as $coupon)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $coupon->code }}</td>
                            <td class="px-4 py-3">{{ $coupon->type === 'percent' ? 'Percent' : 'Fixed' }}</td>
                            <td class="px-4 py-3">
                                @if($coupon->type === 'percent')
                                    {{ rtrim(rtrim(number_format((float) $coupon->value, 2), '0'), '.') }}%
                                @else
                                    ₹{{ number_format((float) $coupon->value, 0) }}
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $coupon->used_count }}{{ $coupon->max_uses ? ' / '.$coupon->max_uses : '' }}</td>
                            <td class="px-4 py-3">
                                <x-badge :color="$coupon->is_active ? 'green' : 'gray'">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Delete this coupon?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-graphite-500">No coupons yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="border-t border-graphite-200 p-4 dark:border-graphite-800">{{ $coupons->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

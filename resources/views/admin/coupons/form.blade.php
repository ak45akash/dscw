<x-layouts.admin :title="$coupon->exists ? 'Edit Coupon' : 'Add Coupon'" breadcrumb="Coupons / Form">
    <form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" class="max-w-xl space-y-6">
        @csrf
        @if($coupon->exists) @method('PUT') @endif
        <x-card class="space-y-4">
            <div>
                <x-label for="code" required>Code</x-label>
                <x-input name="code" id="code" :value="old('code', $coupon->code)" required class="uppercase" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-label for="type" required>Type</x-label>
                    <select name="type" id="type" class="form-input" required>
                        <option value="percent" @selected(old('type', $coupon->type) === 'percent')>Percent (%)</option>
                        <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Fixed amount (₹)</option>
                    </select>
                </div>
                <div>
                    <x-label for="value" required>Value</x-label>
                    <x-input type="number" step="0.01" min="0.01" name="value" id="value" :value="old('value', $coupon->value)" required />
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-label for="min_order_amount">Min order amount</x-label>
                    <x-input type="number" step="0.01" min="0" name="min_order_amount" id="min_order_amount" :value="old('min_order_amount', $coupon->min_order_amount)" />
                </div>
                <div>
                    <x-label for="max_uses">Max uses</x-label>
                    <x-input type="number" min="1" name="max_uses" id="max_uses" :value="old('max_uses', $coupon->max_uses)" />
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-label for="starts_at">Starts at</x-label>
                    <x-input type="datetime-local" name="starts_at" id="starts_at" :value="old('starts_at', optional($coupon->starts_at)->format('Y-m-d\TH:i'))" />
                </div>
                <div>
                    <x-label for="ends_at">Ends at</x-label>
                    <x-input type="datetime-local" name="ends_at" id="ends_at" :value="old('ends_at', optional($coupon->ends_at)->format('Y-m-d\TH:i'))" />
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true))>
                <x-label for="is_active" class="mb-0">Active</x-label>
            </div>
            @if($coupon->exists)
                <p class="text-sm text-graphite-500">Used {{ $coupon->used_count }} time(s).</p>
            @endif
        </x-card>
        <div class="flex justify-between">
            <a href="{{ route('admin.coupons.index') }}" class="btn-secondary">Cancel</a>
            <x-button type="submit">Save</x-button>
        </div>
    </form>
</x-layouts.admin>

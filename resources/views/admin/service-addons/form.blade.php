<x-layouts.admin :title="$addon->exists ? 'Edit Add-on' : 'Add Add-on'" breadcrumb="Services / Add-ons / Form">
    <form method="POST" action="{{ $addon->exists ? route('admin.service-addons.update', $addon) : route('admin.service-addons.store') }}" class="max-w-2xl space-y-6">
        @csrf
        @if($addon->exists) @method('PUT') @endif

        <x-card class="space-y-4">
            <div>
                <x-label for="name" required>Name</x-label>
                <x-input name="name" id="name" :value="old('name', $addon->name)" required />
            </div>
            <div>
                <x-label for="description">Description</x-label>
                <textarea name="description" id="description" rows="3" class="form-input">{{ old('description', $addon->description) }}</textarea>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <x-label for="price" required>Price (₹)</x-label>
                    <x-input type="number" step="0.01" min="0" name="price" id="price" :value="old('price', $addon->price)" required />
                </div>
                <div>
                    <x-label for="duration_minutes" required>Extra minutes</x-label>
                    <x-input type="number" min="0" max="480" name="duration_minutes" id="duration_minutes" :value="old('duration_minutes', $addon->duration_minutes)" required />
                </div>
                <div>
                    <x-label for="display_order">Display order</x-label>
                    <x-input type="number" min="0" name="display_order" id="display_order" value="{{ old('display_order', $addon->display_order ?? 0) }}" />
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $addon->is_active ?? true))>
                <x-label for="is_active" class="mb-0">Active</x-label>
            </div>
            <div>
                <x-label>Available on services</x-label>
                <x-form-hint>Select which services can offer this add-on during booking.</x-form-hint>
                <div class="mt-2 max-h-64 space-y-2 overflow-y-auto rounded-lg border border-graphite-200 p-3 dark:border-graphite-700">
                    @forelse($services as $service)
                        <label class="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                name="service_ids[]"
                                value="{{ $service->id }}"
                                @checked(in_array($service->id, old('service_ids', $selectedServiceIds ?? []), false))
                            >
                            <span>{{ $service->name }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-graphite-500">Create services first.</p>
                    @endforelse
                </div>
            </div>
        </x-card>

        <div class="flex justify-between">
            <a href="{{ route('admin.service-addons.index') }}" class="btn-secondary">Cancel</a>
            <x-button type="submit">Save</x-button>
        </div>
    </form>
</x-layouts.admin>

<x-layouts.admin :title="$service->exists ? 'Edit Service' : 'Add Service'" breadcrumb="Services / Form">
    <form
        method="POST"
        action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}"
        class="max-w-4xl space-y-6"
        enctype="multipart/form-data"
    >
        @csrf
        @if($service->exists) @method('PUT') @endif

        <x-card>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-label for="name" required>Name</x-label>
                    <x-input name="name" id="name" :value="old('name', $service->name)" required />
                </div>
                <div>
                    <x-label for="service_category_id">Category</x-label>
                    <select name="service_category_id" id="service_category_id" class="form-input">
                        <option value="">— None —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('service_category_id', $service->service_category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-label for="price" required>Price (₹)</x-label>
                    <x-input type="number" step="1" min="0" name="price" id="price" :value="old('price', $service->price)" required />
                </div>
                <div>
                    <x-label for="display_order">Display order</x-label>
                    <x-input type="number" name="display_order" id="display_order" :value="old('display_order', $service->display_order ?? 0)" />
                </div>
                <x-duration-input :total-minutes="old('duration_minutes', $service->duration_minutes ?? 60)" />

                <div class="sm:col-span-2 space-y-3">
                    <x-label for="image">Service image</x-label>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <div class="h-36 w-full max-w-xs overflow-hidden rounded-lg border border-graphite-200 bg-graphite-50 dark:border-graphite-700 dark:bg-graphite-800">
                            <img
                                src="{{ $service->imageUrl() }}"
                                alt="{{ $service->name ?: 'Service preview' }}"
                                class="h-full w-full object-cover"
                                id="service-image-preview"
                            >
                        </div>
                        <div class="min-w-0 flex-1 space-y-3">
                            <input
                                type="file"
                                name="image"
                                id="image"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                class="form-input"
                                onchange="const img = document.getElementById('service-image-preview'); if (this.files?.[0] && img) { img.src = URL.createObjectURL(this.files[0]); }"
                            >
                            @if($service->exists && $service->hasCustomImage())
                                <label class="flex items-center gap-2 text-sm text-graphite-600 dark:text-graphite-300">
                                    <input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))>
                                    Remove current uploaded image (fallback to default)
                                </label>
                            @endif
                            <x-form-hint>
                                JPEG, PNG, GIF, or WebP up to 5MB. Files are stored on the host under storage/app/public/services and linked publicly via /storage. Run php artisan storage:link once after deploy.
                            </x-form-hint>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <x-label for="short_description" required>Short description</x-label>
                    <textarea name="short_description" id="short_description" rows="2" class="form-input" required>{{ old('short_description', $service->short_description) }}</textarea>
                    <x-form-hint>Plain-text summary for cards and booking (max 500 characters). Use the full description below for rich content and images.</x-form-hint>
                </div>
                <div class="sm:col-span-2">
                    <x-label for="description" required>Full description</x-label>
                    <x-wysiwyg
                        name="description"
                        id="description"
                        profile="full"
                        :value="old('description', $service->description)"
                        :required="true"
                        :rows="12"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $service->is_active))>
                    <x-label for="is_active" class="mb-0">Active</x-label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', $service->is_featured))>
                    <x-label for="is_featured" class="mb-0">Featured</x-label>
                </div>
            </div>
        </x-card>

        <div class="flex flex-wrap justify-between gap-3">
            <a href="{{ route('admin.services.index') }}" class="btn-secondary">Cancel</a>
            <x-button type="submit">Save Service</x-button>
        </div>
    </form>

    @if($service->exists)
        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="mt-4 max-w-4xl" onsubmit="return confirm('Delete this service?')">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="secondary" class="text-red-600">Delete Service</x-button>
        </form>
    @endif
</x-layouts.admin>

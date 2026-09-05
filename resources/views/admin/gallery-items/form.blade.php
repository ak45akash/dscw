<x-layouts.admin :title="$item->exists ? 'Edit Gallery Item' : 'Add Gallery Item'" breadcrumb="Content / Gallery / Form">
    <form
        method="POST"
        action="{{ $item->exists ? route('admin.gallery-items.update', $item) : route('admin.gallery-items.store') }}"
        class="max-w-4xl space-y-6"
        enctype="multipart/form-data"
    >
        @csrf
        @if($item->exists) @method('PUT') @endif

        <x-card>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-label for="title" required>Title</x-label>
                    <x-input name="title" id="title" :value="old('title', $item->title)" required />
                </div>

                <div class="sm:col-span-2">
                    <x-label for="description">Description</x-label>
                    <textarea name="description" id="description" rows="3" class="form-input">{{ old('description', $item->description) }}</textarea>
                </div>

                <div>
                    <x-label for="service">Service</x-label>
                    <x-input name="service" id="service" :value="old('service', $item->service)" placeholder="Paint correction, ceramic coating…" />
                </div>

                <div>
                    <x-label for="category">Category</x-label>
                    <x-input name="category" id="category" :value="old('category', $item->category)" placeholder="Exterior, Interior…" />
                </div>

                <div class="sm:col-span-2 space-y-3">
                    <x-label for="before_image">Before image</x-label>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <div class="h-36 w-full max-w-xs overflow-hidden rounded-lg border border-graphite-200 bg-graphite-50 dark:border-graphite-700 dark:bg-graphite-800">
                            @if($item->beforeImageUrl())
                                <img
                                    src="{{ $item->beforeImageUrl() }}"
                                    alt="Before preview"
                                    class="h-full w-full object-cover"
                                    id="before-image-preview"
                                >
                            @else
                                <img src="" alt="" class="hidden h-full w-full object-cover" id="before-image-preview">
                                <div id="before-image-placeholder" class="flex h-full items-center justify-center text-sm text-graphite-400">No image</div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1 space-y-3">
                            <input
                                type="file"
                                name="before_image"
                                id="before_image"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                class="form-input"
                                onchange="const img = document.getElementById('before-image-preview'); const ph = document.getElementById('before-image-placeholder'); if (this.files?.[0] && img) { img.src = URL.createObjectURL(this.files[0]); img.classList.remove('hidden'); if (ph) ph.classList.add('hidden'); }"
                            >
                            @if($item->exists && $item->before_image)
                                <label class="flex items-center gap-2 text-sm text-graphite-600 dark:text-graphite-300">
                                    <input type="checkbox" name="remove_before_image" value="1" @checked(old('remove_before_image'))>
                                    Remove current before image
                                </label>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2 space-y-3">
                    <x-label for="after_image">After image</x-label>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <div class="h-36 w-full max-w-xs overflow-hidden rounded-lg border border-graphite-200 bg-graphite-50 dark:border-graphite-700 dark:bg-graphite-800">
                            @if($item->afterImageUrl())
                                <img
                                    src="{{ $item->afterImageUrl() }}"
                                    alt="After preview"
                                    class="h-full w-full object-cover"
                                    id="after-image-preview"
                                >
                            @else
                                <img src="" alt="" class="hidden h-full w-full object-cover" id="after-image-preview">
                                <div id="after-image-placeholder" class="flex h-full items-center justify-center text-sm text-graphite-400">No image</div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1 space-y-3">
                            <input
                                type="file"
                                name="after_image"
                                id="after_image"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                class="form-input"
                                onchange="const img = document.getElementById('after-image-preview'); const ph = document.getElementById('after-image-placeholder'); if (this.files?.[0] && img) { img.src = URL.createObjectURL(this.files[0]); img.classList.remove('hidden'); if (ph) ph.classList.add('hidden'); }"
                            >
                            @if($item->exists && $item->after_image)
                                <label class="flex items-center gap-2 text-sm text-graphite-600 dark:text-graphite-300">
                                    <input type="checkbox" name="remove_after_image" value="1" @checked(old('remove_after_image'))>
                                    Remove current after image
                                </label>
                            @endif
                            <x-form-hint>
                                JPEG, PNG, GIF, or WebP up to 5MB each.
                            </x-form-hint>
                        </div>
                    </div>
                </div>

                <div>
                    <x-label for="display_order">Display order</x-label>
                    <x-input type="number" name="display_order" id="display_order" :value="old('display_order', $item->display_order ?? 0)" />
                </div>

                <div class="flex flex-col justify-end gap-3 sm:flex-row sm:items-center">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', $item->is_featured))>
                        Featured
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))>
                        Active
                    </label>
                </div>
            </div>
        </x-card>

        <div class="flex flex-wrap justify-between gap-3">
            <a href="{{ route('admin.gallery-items.index') }}" class="btn-secondary">Cancel</a>
            <x-button type="submit">Save Gallery Item</x-button>
        </div>
    </form>

    @if($item->exists)
        <form method="POST" action="{{ route('admin.gallery-items.destroy', $item) }}" class="mt-4 max-w-4xl" onsubmit="return confirm('Delete this gallery item?')">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="secondary" class="text-red-600">Delete Gallery Item</x-button>
        </form>
    @endif
</x-layouts.admin>

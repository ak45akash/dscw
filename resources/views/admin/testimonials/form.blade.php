<x-layouts.admin :title="$testimonial->exists ? 'Edit Testimonial' : 'Add Testimonial'" breadcrumb="Content / Testimonials / Form">
    <form
        method="POST"
        action="{{ $testimonial->exists ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}"
        class="max-w-3xl space-y-6"
    >
        @csrf
        @if($testimonial->exists) @method('PUT') @endif

        <x-card class="space-y-4">
            <div>
                <x-label for="name" required>Name</x-label>
                <x-input name="name" id="name" :value="old('name', $testimonial->name)" required />
            </div>

            <div>
                <x-label for="review" required>Review</x-label>
                <textarea name="review" id="review" rows="5" class="form-input" required>{{ old('review', $testimonial->review) }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-label for="rating" required>Rating</x-label>
                    <select name="rating" id="rating" class="form-input" required>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" @selected((int) old('rating', $testimonial->rating ?? 5) === $i)>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <x-label for="service">Service</x-label>
                    <x-input name="service" id="service" :value="old('service', $testimonial->service)" placeholder="Steam wash, detailing…" />
                </div>
                <div>
                    <x-label for="display_order">Display order</x-label>
                    <x-input type="number" name="display_order" id="display_order" :value="old('display_order', $testimonial->display_order ?? 0)" />
                </div>
                <div class="flex flex-col justify-end gap-3 sm:flex-row sm:items-center">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', $testimonial->is_featured))>
                        Featured
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $testimonial->is_active ?? true))>
                        Active
                    </label>
                </div>
            </div>
        </x-card>

        <div class="flex justify-between">
            <a href="{{ route('admin.testimonials.index') }}" class="btn-secondary">Cancel</a>
            <x-button type="submit">Save Testimonial</x-button>
        </div>
    </form>

    @if($testimonial->exists)
        <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" class="mt-4 max-w-3xl" onsubmit="return confirm('Delete this testimonial?')">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="secondary" class="text-red-600">Delete Testimonial</x-button>
        </form>
    @endif
</x-layouts.admin>

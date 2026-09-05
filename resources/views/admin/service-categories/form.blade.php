<x-layouts.admin :title="$category->exists ? 'Edit Category' : 'Add Category'" breadcrumb="Categories / Form">
    <form method="POST" action="{{ $category->exists ? route('admin.service-categories.update', $category) : route('admin.service-categories.store') }}" class="max-w-xl space-y-6">
        @csrf
        @if($category->exists) @method('PUT') @endif
        <x-card class="space-y-4">
            <div>
                <x-label for="name" required>Name</x-label>
                <x-input name="name" id="name" :value="old('name', $category->name)" required />
            </div>
            <div>
                <x-label for="description">Description</x-label>
                <x-wysiwyg
                    name="description"
                    id="description"
                    profile="full"
                    :value="old('description', $category->description)"
                    :rows="8"
                />
            </div>
            <div>
                <x-label for="display_order">Display order</x-label>
                <x-input type="number" name="display_order" id="display_order" :value="old('display_order', $category->display_order ?? 0)" />
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))>
                <x-label for="is_active" class="mb-0">Active</x-label>
            </div>
        </x-card>
        <div class="flex justify-between">
            <a href="{{ route('admin.service-categories.index') }}" class="btn-secondary">Cancel</a>
            <x-button type="submit">Save</x-button>
        </div>
    </form>
</x-layouts.admin>

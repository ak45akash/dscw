<x-layouts.admin :title="$page->exists ? 'Edit Page' : 'Add Page'" breadcrumb="Content / Pages / Form">
    <form method="POST" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="max-w-4xl space-y-6">
        @csrf
        @if($page->exists) @method('PUT') @endif
        <x-card class="space-y-4">
            <div>
                <x-label for="title" required>Title</x-label>
                <x-input name="title" id="title" :value="old('title', $page->title)" required />
            </div>
            @if($page->exists)
                <div>
                    <x-label for="slug">Slug</x-label>
                    <x-input name="slug" id="slug" :value="old('slug', $page->slug)" />
                </div>
            @endif
            <div>
                <x-label for="content" required>Content</x-label>
                <x-wysiwyg name="content" id="content" profile="full" :value="old('content', $page->content)" :required="true" :rows="14" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-label for="meta_title">Meta title</x-label>
                    <x-input name="meta_title" id="meta_title" :value="old('meta_title', $page->meta_title)" />
                </div>
                <div>
                    <x-label for="template">Template</x-label>
                    <x-input name="template" id="template" :value="old('template', $page->template ?? 'default')" />
                </div>
            </div>
            <div>
                <x-label for="meta_description">Meta description</x-label>
                <textarea name="meta_description" id="meta_description" rows="2" class="form-input">{{ old('meta_description', $page->meta_description) }}</textarea>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $page->is_active ?? true))>
                Active
            </label>
        </x-card>
        <div class="flex justify-between">
            <a href="{{ route('admin.pages.index') }}" class="btn-secondary">Cancel</a>
            <x-button type="submit">Save Page</x-button>
        </div>
    </form>
</x-layouts.admin>

<x-layouts.admin title="Categories & Tags" breadcrumb="Content / Posts / Categories">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-graphite-500">Organize posts the WordPress way — categories for hierarchy, tags for topics.</p>
        <a href="{{ route('admin.blog-posts.index') }}" class="btn-secondary">← All Posts</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card>
            <h2 class="mb-4 text-lg font-semibold">Categories</h2>

            <form method="POST" action="{{ route('admin.blog-categories.store') }}" class="mb-6 space-y-3 border-b border-graphite-200 pb-6 dark:border-graphite-800">
                @csrf
                <div>
                    <x-label for="category_name" required>Name</x-label>
                    <x-input name="name" id="category_name" :value="old('name')" required />
                </div>
                <div>
                    <x-label for="category_description">Description</x-label>
                    <textarea name="description" id="category_description" rows="2" class="form-input">{{ old('description') }}</textarea>
                </div>
                <x-button type="submit">Add Category</x-button>
            </form>

            <ul class="divide-y divide-graphite-100 dark:divide-graphite-800">
                @forelse($categories as $category)
                    <li class="flex items-start justify-between gap-3 py-3">
                        <div>
                            <div class="font-medium">{{ $category->name }}</div>
                            @if($category->description)
                                <p class="mt-1 text-sm text-graphite-500">{{ $category->description }}</p>
                            @endif
                            <div class="mt-2">
                                <x-badge>{{ $category->posts_count }} {{ \Illuminate\Support\Str::plural('post', $category->posts_count) }}</x-badge>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.blog-categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                        </form>
                    </li>
                @empty
                    <li class="py-4 text-sm text-graphite-500">No categories yet.</li>
                @endforelse
            </ul>
        </x-card>

        <x-card>
            <h2 class="mb-4 text-lg font-semibold">Tags</h2>

            <form method="POST" action="{{ route('admin.blog-tags.store') }}" class="mb-6 space-y-3 border-b border-graphite-200 pb-6 dark:border-graphite-800">
                @csrf
                <div>
                    <x-label for="tag_name" required>Name</x-label>
                    <x-input name="name" id="tag_name" required />
                </div>
                <x-button type="submit">Add Tag</x-button>
            </form>

            <ul class="divide-y divide-graphite-100 dark:divide-graphite-800">
                @forelse($tags as $tag)
                    <li class="flex items-center justify-between gap-3 py-3">
                        <div>
                            <div class="font-medium">{{ $tag->name }}</div>
                            <div class="mt-2">
                                <x-badge>{{ $tag->posts_count }} {{ \Illuminate\Support\Str::plural('post', $tag->posts_count) }}</x-badge>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.blog-tags.destroy', $tag) }}" onsubmit="return confirm('Delete this tag?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                        </form>
                    </li>
                @empty
                    <li class="py-4 text-sm text-graphite-500">No tags yet.</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.admin>

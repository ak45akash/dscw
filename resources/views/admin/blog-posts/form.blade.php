<x-layouts.admin :title="$post->exists ? 'Edit Post' : 'Add New Post'" breadcrumb="Content / Posts / Editor">
    @php
        $isEdit = $post->exists;
        $formAction = $isEdit ? route('admin.blog-posts.update', $post) : route('admin.blog-posts.store');
    @endphp

    <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-4" x-data="{ status: @js(old('status', $post->status ?? 'draft')) }">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-graphite-900 dark:text-white">
                    {{ $isEdit ? 'Edit Post' : 'Add New Post' }}
                </h2>
                @if($isEdit && $post->isPublished())
                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener" class="mt-1 inline-block text-sm text-blue-600 hover:underline">
                        View post ↗
                    </a>
                @endif
            </div>
            <a href="{{ route('admin.blog-posts.index') }}" class="btn-secondary">All Posts</a>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            {{-- Main column --}}
            <div class="space-y-4">
                <x-card class="space-y-3">
                    <div>
                        <x-label for="title" class="sr-only">Title</x-label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title', $post->title) }}"
                            required
                            placeholder="Add title"
                            class="w-full border-0 bg-transparent p-0 text-3xl font-semibold text-graphite-900 placeholder:text-graphite-400 focus:outline-none focus:ring-0 dark:text-white"
                        >
                    </div>

                    <div class="rounded-lg border border-dashed border-graphite-200 bg-graphite-50 px-3 py-2 text-sm text-graphite-600 dark:border-graphite-700 dark:bg-graphite-900/50 dark:text-graphite-300">
                        <span class="font-medium text-graphite-500">Permalink:</span>
                        <span class="mx-1">{{ url('/blog') }}/</span>
                        <input
                            type="text"
                            name="slug"
                            id="slug"
                            value="{{ old('slug', $post->slug) }}"
                            placeholder="auto-from-title"
                            class="inline-block min-w-[12rem] rounded border border-graphite-200 bg-white px-2 py-1 text-sm dark:border-graphite-700 dark:bg-graphite-950"
                        >
                    </div>
                </x-card>

                <x-card class="space-y-3">
                    <x-label for="content" required>Content</x-label>
                    <x-wysiwyg
                        name="content"
                        id="content"
                        profile="full"
                        :value="old('content', $post->content)"
                        :required="true"
                        :rows="18"
                    />
                </x-card>

                <x-card class="space-y-3">
                    <x-label for="excerpt">Excerpt</x-label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="form-input" placeholder="Write an excerpt (optional). If empty, one is generated from the content.">{{ old('excerpt', $post->excerpt) }}</textarea>
                    <x-form-hint>Shown on blog cards, home page, and search snippets.</x-form-hint>
                </x-card>

                <details class="rounded-xl border border-graphite-200 bg-white open:shadow-sm dark:border-graphite-800 dark:bg-graphite-950" open>
                    <summary class="cursor-pointer list-none px-5 py-4 font-semibold text-graphite-900 dark:text-white">
                        SEO
                        <span class="ml-2 text-xs font-normal text-graphite-500">Meta title &amp; description</span>
                    </summary>
                    <div class="space-y-4 border-t border-graphite-200 px-5 py-4 dark:border-graphite-800">
                        <div>
                            <x-label for="meta_title">Meta title</x-label>
                            <x-input name="meta_title" id="meta_title" :value="old('meta_title', $post->meta_title)" />
                        </div>
                        <div>
                            <x-label for="meta_description">Meta description</x-label>
                            <textarea name="meta_description" id="meta_description" rows="2" class="form-input">{{ old('meta_description', $post->meta_description) }}</textarea>
                        </div>
                    </div>
                </details>
            </div>

            {{-- Sidebar meta boxes --}}
            <aside class="space-y-4 xl:sticky xl:top-24 xl:self-start">
                <x-card class="space-y-4">
                    <h3 class="font-semibold text-graphite-900 dark:text-white">Publish</h3>

                    <div>
                        <x-label for="status">Status</x-label>
                        <select name="status" id="status" class="form-input" x-model="status" required>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>

                    <div>
                        <x-label for="published_at">Publish date</x-label>
                        <x-input
                            type="datetime-local"
                            name="published_at"
                            id="published_at"
                            :value="old('published_at', $post->published_at?->format('Y-m-d\TH:i'))"
                        />
                        <x-form-hint>Leave blank to publish immediately when status is Published.</x-form-hint>
                    </div>

                    <div class="flex flex-wrap gap-2 border-t border-graphite-200 pt-4 dark:border-graphite-800">
                        <x-button type="submit">
                            <span x-show="status === 'published'" x-cloak>{{ $isEdit ? 'Update' : 'Publish' }}</span>
                            <span x-show="status !== 'published'" x-cloak>Save Draft</span>
                        </x-button>
                        @if($isEdit && $post->isPublished())
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener" class="btn-secondary">Preview</a>
                        @endif
                    </div>
                </x-card>

                <x-card class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-graphite-900 dark:text-white">Categories</h3>
                        <a href="{{ route('admin.blog-taxonomies.index') }}" class="text-xs text-blue-600 hover:underline">+ Add New</a>
                    </div>
                    <div class="max-h-48 space-y-2 overflow-y-auto rounded-lg border border-graphite-200 p-3 dark:border-graphite-700">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="blog_category_id" value="" @checked(! old('blog_category_id', $post->blog_category_id))>
                            <span class="text-graphite-500">Uncategorized</span>
                        </label>
                        @foreach($categories as $category)
                            <label class="flex items-center gap-2 text-sm text-graphite-700 dark:text-graphite-300">
                                <input
                                    type="radio"
                                    name="blog_category_id"
                                    value="{{ $category->id }}"
                                    @checked((string) old('blog_category_id', $post->blog_category_id) === (string) $category->id)
                                >
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                </x-card>

                <x-card class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-graphite-900 dark:text-white">Tags</h3>
                        <a href="{{ route('admin.blog-taxonomies.index') }}" class="text-xs text-blue-600 hover:underline">Manage</a>
                    </div>
                    <div class="max-h-48 space-y-2 overflow-y-auto rounded-lg border border-graphite-200 p-3 dark:border-graphite-700">
                        @forelse($tags as $tag)
                            <label class="flex items-center gap-2 text-sm text-graphite-700 dark:text-graphite-300">
                                <input
                                    type="checkbox"
                                    name="tag_ids[]"
                                    value="{{ $tag->id }}"
                                    @checked(in_array($tag->id, old('tag_ids', $selectedTagIds ?? []), false))
                                >
                                {{ $tag->name }}
                            </label>
                        @empty
                            <p class="text-sm text-graphite-500">No tags yet.</p>
                        @endforelse
                    </div>
                </x-card>

                <x-card class="space-y-3">
                    <h3 class="font-semibold text-graphite-900 dark:text-white">Featured image</h3>
                    <div class="overflow-hidden rounded-lg border border-graphite-200 bg-graphite-50 dark:border-graphite-700 dark:bg-graphite-800">
                        @if($post->featuredImageUrl())
                            <img
                                src="{{ $post->featuredImageUrl() }}"
                                alt="{{ $post->featured_image_alt ?: ($post->title ?: 'Featured image preview') }}"
                                class="aspect-video w-full object-cover"
                                id="featured-image-preview"
                            >
                        @else
                            <img src="" alt="" class="hidden aspect-video w-full object-cover" id="featured-image-preview">
                            <div id="featured-image-placeholder" class="flex aspect-video items-center justify-center text-sm text-graphite-400">Set featured image</div>
                        @endif
                    </div>
                    <input
                        type="file"
                        name="featured_image"
                        id="featured_image"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        class="form-input"
                        onchange="const img = document.getElementById('featured-image-preview'); const ph = document.getElementById('featured-image-placeholder'); if (this.files?.[0] && img) { img.src = URL.createObjectURL(this.files[0]); img.classList.remove('hidden'); if (ph) ph.classList.add('hidden'); }"
                    >
                    @if($isEdit && $post->featured_image)
                        <label class="flex items-center gap-2 text-sm text-graphite-600 dark:text-graphite-300">
                            <input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))>
                            Remove featured image
                        </label>
                    @endif
                    <div>
                        <x-label for="featured_image_alt">Alt text</x-label>
                        <x-input name="featured_image_alt" id="featured_image_alt" :value="old('featured_image_alt', $post->featured_image_alt)" />
                    </div>
                </x-card>
            </aside>
        </div>
    </form>

    @if($isEdit)
        <form method="POST" action="{{ route('admin.blog-posts.destroy', $post) }}" class="mt-4 xl:ml-auto xl:max-w-[320px]" onsubmit="return confirm('Move this post to trash?')">
            @csrf
            @method('DELETE')
            <x-card>
                <button type="submit" class="text-sm text-red-600 hover:underline">Move to Trash</button>
            </x-card>
        </form>
    @endif
</x-layouts.admin>

<x-layouts.admin title="Posts" breadcrumb="Content / Posts">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-graphite-900 dark:text-white">Posts</h2>
            <p class="mt-1 text-sm text-graphite-500">Write, schedule, and publish articles for the public blog.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.blog-taxonomies.index') }}" class="btn-secondary">Categories &amp; Tags</a>
            <x-button href="{{ route('admin.blog-posts.create') }}">Add New</x-button>
        </div>
    </div>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <nav class="flex flex-wrap gap-3 text-sm">
            <a href="{{ route('admin.blog-posts.index') }}" @class(['font-semibold text-blue-600' => $status === '', 'text-graphite-600 hover:text-blue-600' => $status !== ''])>
                All <span class="text-graphite-400">({{ $counts['all'] }})</span>
            </a>
            <span class="text-graphite-300">|</span>
            <a href="{{ route('admin.blog-posts.index', ['status' => 'published']) }}" @class(['font-semibold text-blue-600' => $status === 'published', 'text-graphite-600 hover:text-blue-600' => $status !== 'published'])>
                Published <span class="text-graphite-400">({{ $counts['published'] }})</span>
            </a>
            <span class="text-graphite-300">|</span>
            <a href="{{ route('admin.blog-posts.index', ['status' => 'draft']) }}" @class(['font-semibold text-blue-600' => $status === 'draft', 'text-graphite-600 hover:text-blue-600' => $status !== 'draft'])>
                Drafts <span class="text-graphite-400">({{ $counts['draft'] }})</span>
            </a>
        </nav>

        <form method="GET" class="flex items-center gap-2">
            @if($status !== '')
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <x-input name="s" type="search" :value="$search" placeholder="Search posts…" class="w-56" />
            <x-button type="submit" variant="secondary">Search Posts</x-button>
        </form>
    </div>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Title</th>
                        <th class="px-4 py-3 text-left font-semibold">Author</th>
                        <th class="px-4 py-3 text-left font-semibold">Categories</th>
                        <th class="px-4 py-3 text-left font-semibold">Tags</th>
                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($posts as $post)
                        <tr class="group align-top hover:bg-graphite-50/80 dark:hover:bg-graphite-900/40">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.blog-posts.edit', $post) }}" class="font-semibold text-blue-700 hover:underline dark:text-blue-300">
                                    {{ $post->title }}
                                </a>
                                @if($post->status === 'draft')
                                    <span class="ml-1 text-xs font-medium text-graphite-500">— Draft</span>
                                @endif
                                <div class="mt-1 flex flex-wrap gap-x-2 text-xs opacity-0 transition group-hover:opacity-100">
                                    <a href="{{ route('admin.blog-posts.edit', $post) }}" class="text-blue-600 hover:underline">Edit</a>
                                    <span class="text-graphite-300">|</span>
                                    @if($post->isPublished())
                                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline">View</a>
                                        <span class="text-graphite-300">|</span>
                                    @endif
                                    <form method="POST" action="{{ route('admin.blog-posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Move this post to trash?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Trash</button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-graphite-600">{{ $post->author?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-graphite-600">{{ $post->category?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-graphite-600">
                                {{ $post->tags->isNotEmpty() ? $post->tags->pluck('name')->join(', ') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-graphite-600">
                                @if($post->status === 'published' && $post->published_at)
                                    <div>Published</div>
                                    <div class="text-xs">{{ $post->published_at->format('Y/m/d \a\t g:i a') }}</div>
                                @else
                                    <div>Last Modified</div>
                                    <div class="text-xs">{{ $post->updated_at->format('Y/m/d \a\t g:i a') }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-graphite-500">
                                No posts found.
                                <a href="{{ route('admin.blog-posts.create') }}" class="text-blue-600 hover:underline">Add New</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($posts->hasPages())
            <div class="border-t border-graphite-200 p-4 dark:border-graphite-800">{{ $posts->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

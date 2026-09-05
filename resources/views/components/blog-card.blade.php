@props(['post'])

<article class="card flex h-full flex-col overflow-hidden transition-shadow hover:shadow-md">
    <a href="{{ route('blog.show', $post->slug) }}" class="block">
        <div class="aspect-[16/10] bg-gradient-to-br from-graphite-100 to-brand-100 dark:from-graphite-800 dark:to-brand-900"></div>
    </a>
    <div class="flex flex-1 flex-col p-5">
        @if($post->category)
            <span class="text-xs font-semibold uppercase tracking-wide text-brand-600 dark:text-accent-400">{{ $post->category->name }}</span>
        @endif
        <h3 class="mt-2 text-lg font-semibold leading-snug text-graphite-900 dark:text-white">
            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-brand-600 dark:hover:text-accent-400">{{ $post->title }}</a>
        </h3>
        <p class="mt-2 flex-1 text-sm text-graphite-600 dark:text-graphite-300">{{ $post->excerpt }}</p>
        <div class="mt-4 flex items-center justify-between text-xs text-graphite-500">
            <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('M j, Y') }}</time>
            <span>{{ $post->readingTime() }} min read</span>
        </div>
    </div>
</article>

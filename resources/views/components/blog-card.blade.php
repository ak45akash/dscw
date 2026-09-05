@props(['post'])

@php
    $imageUrl = $post->featuredImageUrl();
@endphp

<article class="card flex h-full flex-col overflow-hidden transition-shadow hover:shadow-md">
    <a href="{{ route('blog.show', $post->slug) }}" class="block">
        @if($imageUrl)
            <div class="aspect-[16/10] overflow-hidden bg-graphite-100 dark:bg-graphite-800">
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $post->featured_image_alt ?: $post->title }}"
                    class="h-full w-full object-cover transition-transform duration-300 hover:scale-105"
                    loading="lazy"
                >
            </div>
        @else
            <div class="aspect-[16/10] bg-gradient-to-br from-graphite-100 to-brand-100 dark:from-graphite-800 dark:to-brand-900"></div>
        @endif
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
            <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('M j, Y') }}</time>
            <span>{{ $post->readingTime() }} min read</span>
        </div>
    </div>
</article>

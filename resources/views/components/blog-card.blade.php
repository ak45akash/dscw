@props([
    'post',
    'variant' => 'light',
])

@php
    $imageUrl = $post->featuredImageUrl();
    $isDark = $variant === 'dark';
@endphp

<article @class([
    'card hover-lift flex h-full flex-col overflow-hidden transition-shadow',
    'border-white/10 bg-white/10 text-white shadow-none backdrop-blur-sm' => $isDark,
    'bg-white dark:bg-graphite-900' => ! $isDark,
])>
    <a href="{{ route('blog.show', $post->slug) }}" class="block">
        @if($imageUrl)
            <div class="aspect-[16/10] overflow-hidden bg-graphite-100 dark:bg-graphite-800">
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $post->featured_image_alt ?: $post->title }}"
                    class="h-full w-full object-cover transition-transform duration-500 hover:scale-105"
                    loading="lazy"
                >
            </div>
        @else
            <div class="aspect-[16/10] bg-gradient-to-br from-brand-700 to-brand-900"></div>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-5">
        @if($post->category)
            <span @class([
                'text-xs font-semibold uppercase tracking-wide',
                'text-accent-300' => $isDark,
                'text-brand-600 dark:text-accent-400' => ! $isDark,
            ])>{{ $post->category->name }}</span>
        @endif
        <h3 @class([
            'mt-2 text-lg font-semibold leading-snug',
            'text-white' => $isDark,
            'text-graphite-900 dark:text-white' => ! $isDark,
        ])>
            <a href="{{ route('blog.show', $post->slug) }}" @class([
                'hover:text-accent-300' => $isDark,
                'hover:text-brand-600 dark:hover:text-accent-400' => ! $isDark,
            ])>{{ $post->title }}</a>
        </h3>
        <p @class([
            'mt-2 flex-1 text-sm',
            'text-brand-100/90' => $isDark,
            'text-graphite-600 dark:text-graphite-300' => ! $isDark,
        ])>{{ $post->excerpt }}</p>
        <div @class([
            'mt-4 flex items-center justify-between text-xs',
            'text-brand-200' => $isDark,
            'text-graphite-500' => ! $isDark,
        ])>
            <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('M j, Y') }}</time>
            <span>{{ $post->readingTime() }} min read</span>
        </div>
    </div>
</article>

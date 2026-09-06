<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription" :seo-image="$seoImage ?? null" :og-type="$ogType ?? 'website'">
    <x-page-hero
        :badge="$post->category?->name"
        :title="$post->title"
        :subtitle="$post->excerpt"
    />

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Blog', 'url' => route('blog.index')],
        ['label' => $post->title],
    ]" />

    <article class="py-12 sm:py-16">
        <div class="container-site max-w-4xl">
            <div class="mb-8 flex flex-wrap items-center gap-4 text-sm text-graphite-500">
                <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('F j, Y') }}</time>
                <span>·</span>
                <span>{{ $post->readingTime() }} min read</span>
                @if($post->author)
                    <span>·</span>
                    <span>By {{ $post->author->name }}</span>
                @endif
            </div>

            @if($post->featuredImageUrl())
                <figure class="mb-10 overflow-hidden rounded-xl">
                    <img
                        src="{{ $post->featuredImageUrl() }}"
                        alt="{{ $post->featured_image_alt ?: $post->title }}"
                        class="w-full object-cover"
                    >
                </figure>
            @endif

            @if($post->tags->isNotEmpty())
                <div class="mb-8 flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                        <x-badge color="blue">{{ $tag->name }}</x-badge>
                    @endforeach
                </div>
            @endif

            <div class="prose-content">
                {!! $post->content !!}
            </div>

            <div class="mt-12 rounded-xl border border-brand-200 bg-brand-50 p-8 dark:border-brand-800 dark:bg-brand-950">
                <h3 class="text-xl font-bold text-graphite-900 dark:text-white">Ready for professional car care?</h3>
                <p class="mt-2 text-graphite-600 dark:text-graphite-300">Put these tips into practice with expert service from Diamond Steam Car Wash. Book a professional steam wash or explore our ceramic coating services today.</p>
                <div class="mt-6 flex flex-wrap gap-4">
                    <x-button href="{{ route('booking.index') }}">Book Now</x-button>
                    <x-button href="{{ route('services.index') }}" variant="secondary">View Services</x-button>
                </div>
            </div>
        </div>
    </article>

    @if($related->isNotEmpty())
    <section class="bg-graphite-50 py-16 dark:bg-graphite-900 sm:py-20">
        <div class="container-site">
            <x-section-heading title="Related Articles" class="mb-10" />
            <div class="grid gap-6 md:grid-cols-3">
                @foreach($related as $relatedPost)
                    <x-blog-card :post="$relatedPost" />
                @endforeach
            </div>
        </div>
    </section>
    @endif
</x-layouts.public>

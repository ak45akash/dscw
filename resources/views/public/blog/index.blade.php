<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription" :seo-image="$seoImage ?? null">
    <x-page-hero
        badge="Car Care Blog"
        title="Expert Car Care Guides & Tips"
        subtitle="In-depth articles on washing, detailing, paint protection, ceramic coating, and keeping your vehicle in showroom condition."
    />

    <x-breadcrumbs :items="[['label' => 'Home', 'url' => route('home')], ['label' => 'Blog']]" />

  @if($featured)
    <section class="border-b border-graphite-200 py-12 dark:border-graphite-800">
        <div class="container-site">
            <div class="card overflow-hidden lg:flex">
                <a href="{{ route('blog.show', $featured->slug) }}" class="block aspect-[16/9] overflow-hidden bg-gradient-to-br from-brand-100 to-brand-200 lg:aspect-auto lg:min-h-[280px] lg:w-1/2 dark:from-brand-900 dark:to-brand-800">
                    @if($featured->featuredImageUrl())
                        <img
                            src="{{ $featured->featuredImageUrl() }}"
                            alt="{{ $featured->featured_image_alt ?: $featured->title }}"
                            class="h-full w-full object-cover"
                        >
                    @endif
                </a>
                <div class="flex flex-col justify-center p-8 lg:w-1/2 lg:p-12">
                    <span class="text-xs font-semibold uppercase tracking-wide text-brand-600">Featured Article</span>
                    <h2 class="mt-3 text-2xl font-bold text-graphite-900 dark:text-white">
                        <a href="{{ route('blog.show', $featured->slug) }}" class="hover:text-brand-600">{{ $featured->title }}</a>
                    </h2>
                    <p class="mt-4 text-graphite-600 dark:text-graphite-300">{{ $featured->excerpt }}</p>
                    <div class="mt-6 flex items-center gap-4 text-sm text-graphite-500">
                        <time datetime="{{ $featured->published_at->toDateString() }}">{{ $featured->published_at->format('F j, Y') }}</time>
                        <span>{{ $featured->readingTime() }} min read</span>
                    </div>
                    <x-button href="{{ route('blog.show', $featured->slug) }}" class="mt-6 w-fit">Read Article</x-button>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="py-16 sm:py-20">
        <div class="container-site">
            <div class="grid gap-12 lg:grid-cols-4">
                <aside class="lg:col-span-1">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-graphite-500">Categories</h3>
                    <ul class="mt-4 space-y-2">
                        <li>
                            <a href="{{ route('blog.index') }}" @class(['block rounded-lg px-3 py-2 text-sm', 'bg-brand-50 font-medium text-brand-700 dark:bg-brand-950 dark:text-brand-200' => !$activeCategory, 'text-graphite-600 hover:bg-graphite-100 dark:text-graphite-300 dark:hover:bg-graphite-800' => $activeCategory])>All Articles</a>
                        </li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('blog.index', ['category' => $cat->slug]) }}" @class(['flex items-center justify-between rounded-lg px-3 py-2 text-sm', 'bg-brand-50 font-medium text-brand-700 dark:bg-brand-950 dark:text-brand-200' => $activeCategory === $cat->slug, 'text-graphite-600 hover:bg-graphite-100 dark:text-graphite-300 dark:hover:bg-graphite-800' => $activeCategory !== $cat->slug])>
                                    <span>{{ $cat->name }}</span>
                                    <span class="text-xs text-graphite-400">{{ $cat->posts_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </aside>
                <div class="lg:col-span-3">
                    @if($posts->isEmpty())
                        <p class="text-graphite-500">No articles found in this category.</p>
                    @else
                        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach($posts as $post)
                                <x-blog-card :post="$post" />
                            @endforeach
                        </div>
                        <div class="mt-10">{{ $posts->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>

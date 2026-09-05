<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription">
    <x-page-hero
        badge="Our Work"
        title="Before & After Gallery"
        subtitle="Real results from real customers. See the transformations we deliver through professional washing, detailing, and paint protection."
    />

    <x-breadcrumbs :items="[['label' => 'Home', 'url' => route('home')], ['label' => 'Gallery']]" />

    <section class="py-16 sm:py-20">
        <div class="container-site max-w-4xl">
            <p class="text-lg leading-relaxed text-graphite-600 dark:text-graphite-300">
                Every vehicle that leaves our facility represents our commitment to excellence. This gallery showcases a selection of before-and-after results from our most popular services — paint correction, interior deep cleaning, ceramic coating, headlight restoration, and more. These are not stock photos; they reflect the quality you can expect when you trust Diamond Steam Car Wash with your vehicle.
            </p>
        </div>
    </section>

    @if($featured->isNotEmpty())
    <section class="bg-graphite-50 py-16 dark:bg-graphite-900/50 sm:py-20">
        <div class="container-site">
            <x-section-heading title="Featured Transformations" class="mb-10" />
            <div class="grid gap-8 md:grid-cols-2">
                @foreach($featured as $item)
                    <article class="card overflow-hidden">
                        <div class="grid grid-cols-2 gap-1">
                            <div class="aspect-[4/3] bg-gradient-to-br from-graphite-300 to-graphite-400 dark:from-graphite-700 dark:to-graphite-600">
                                <span class="flex h-full items-center justify-center text-xs font-medium text-white/80">Before</span>
                            </div>
                            <div class="aspect-[4/3] bg-gradient-to-br from-brand-200 to-brand-400 dark:from-brand-800 dark:to-brand-600">
                                <span class="flex h-full items-center justify-center text-xs font-medium text-white/80">After</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-2">
                                <x-badge color="blue">{{ $item->category }}</x-badge>
                                <span class="text-xs text-graphite-500">{{ $item->service }}</span>
                            </div>
                            <h3 class="mt-3 text-lg font-semibold">{{ $item->title }}</h3>
                            <p class="mt-2 text-sm text-graphite-600 dark:text-graphite-300">{{ $item->description }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="py-16 sm:py-20">
        <div class="container-site">
            <x-section-heading title="All Gallery Items" subtitle="Browse our complete collection of service results." class="mb-10" />
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($items as $item)
                    <article class="card overflow-hidden">
                        <div class="aspect-[16/10] bg-gradient-to-br from-graphite-200 to-brand-100 dark:from-graphite-800 dark:to-brand-900"></div>
                        <div class="p-5">
                            <h3 class="font-semibold">{{ $item->title }}</h3>
                            <p class="mt-1 text-xs text-graphite-500">{{ $item->service }}</p>
                            @if($item->description)
                                <p class="mt-2 text-sm text-graphite-600 dark:text-graphite-300">{{ Str::limit($item->description, 120) }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-brand-700 py-16 text-white sm:py-20">
        <div class="container-site text-center">
            <h2 class="text-3xl font-bold">Want results like these?</h2>
            <p class="mx-auto mt-4 max-w-2xl text-brand-100">Book a service today and experience the Diamond Steam difference for yourself.</p>
            <x-button href="{{ route('booking.index') }}" class="mt-8 bg-white text-brand-700 hover:bg-brand-50">Book Now</x-button>
        </div>
    </section>
</x-layouts.public>

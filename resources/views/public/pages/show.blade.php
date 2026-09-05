<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription">
    <x-page-hero :title="$page->title" :subtitle="$page->meta_description" />

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $page->title],
    ]" />

    <section class="py-12 sm:py-16">
        <div class="container-site max-w-4xl">
            <div class="prose-content">
                {!! $page->content !!}
            </div>
        </div>
    </section>

    @if($page->slug === 'about-us')
    <section class="bg-brand-700 py-16 text-white sm:py-20">
        <div class="container-site text-center">
            <h2 class="text-3xl font-bold">Experience the Diamond Steam difference</h2>
            <p class="mx-auto mt-4 max-w-2xl text-brand-100">Join thousands of satisfied customers who trust us with their vehicles. Book your first service today.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <x-button href="{{ route('booking.index') }}" class="bg-white text-brand-700 hover:bg-brand-50">Book Now</x-button>
                <x-button href="{{ route('services.index') }}" variant="secondary" class="border-white/30 bg-white/10 text-white hover:bg-white/20">Our Services</x-button>
            </div>
        </div>
    </section>
    @endif
</x-layouts.public>

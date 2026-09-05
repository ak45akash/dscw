<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription">
    <x-page-hero
        :badge="$service->category?->name"
        :title="$service->name"
        :subtitle="$service->short_description"
    >
        <x-slot:actions>
            <x-button href="{{ route('booking.index') }}" class="bg-white text-brand-700 hover:bg-brand-50">Book This Service</x-button>
            <x-button href="{{ route('services.index') }}" variant="secondary" class="border-white/30 bg-white/10 text-white hover:bg-white/20">All Services</x-button>
        </x-slot:actions>
    </x-page-hero>

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Services', 'url' => route('services.index')],
        ['label' => $service->name],
    ]" />

    <section class="py-12 sm:py-16">
        <div class="container-site">
            <div class="grid gap-12 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="prose-content">
                        {!! $service->description !!}
                    </div>
                </div>
                <aside class="space-y-6">
                    <x-card>
                        <h3 class="text-lg font-semibold">Service Summary</h3>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex justify-between border-b border-graphite-100 pb-3 dark:border-graphite-800">
                                <dt class="text-graphite-500">Price from</dt>
                                <dd class="font-bold text-brand-700 dark:text-brand-300">{{ $service->formattedPrice() }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-graphite-100 pb-3 dark:border-graphite-800">
                                <dt class="text-graphite-500">Duration</dt>
                                <dd class="font-medium">{{ $service->formattedDuration() }}</dd>
                            </div>
                            @if($service->category)
                            <div class="flex justify-between border-b border-graphite-100 pb-3 dark:border-graphite-800">
                                <dt class="text-graphite-500">Category</dt>
                                <dd class="font-medium">{{ $service->category->name }}</dd>
                            </div>
                            @endif
                        </dl>
                        <x-button href="{{ route('booking.index') }}" class="mt-6 w-full">Book Now</x-button>
                    </x-card>
                    <x-card>
                        <h3 class="text-lg font-semibold">What to Expect</h3>
                        <ul class="mt-4 space-y-2 text-sm text-graphite-600 dark:text-graphite-300">
                            <li>✓ Trained professional technicians</li>
                            <li>✓ Premium automotive-grade products</li>
                            <li>✓ Paint-safe steam technology</li>
                            <li>✓ Quality check before handover</li>
                            <li>✓ Transparent pricing, no surprises</li>
                        </ul>
                    </x-card>
                </aside>
            </div>
        </div>
    </section>

    @if($related->isNotEmpty())
    <section class="bg-graphite-50 py-16 dark:bg-graphite-900/50 sm:py-20">
        <div class="container-site">
            <x-section-heading title="Related Services" subtitle="You might also be interested in these services." class="mb-10" />
            <div class="grid gap-6 md:grid-cols-3">
                @foreach($related as $relatedService)
                    <x-service-card :service="$relatedService" />
                @endforeach
            </div>
        </div>
    </section>
    @endif
</x-layouts.public>

<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription" seo-image="images/hero.jpg">
    <div class="relative flex min-h-[42vh] items-center overflow-hidden sm:min-h-[50vh] lg:min-h-[56vh]">
        <x-parallax-bg src="images/hero.jpg" alt="Premium Car Care Services" :speed="0.8" brightness="0.5" position="center 30%" height="180%">
            <div class="absolute inset-0 bg-gradient-to-r from-graphite-950/70 via-brand-950/40 to-transparent"></div>
        </x-parallax-bg>
        <div class="container-custom relative z-10 py-14 text-white sm:py-16 lg:py-20">
            <div class="max-w-2xl">
                <h1 class="mb-4 text-3xl font-bold sm:text-4xl md:text-5xl">Premium Car Care Services</h1>
                <p class="mb-8 text-base leading-relaxed text-white/90 sm:text-lg md:text-xl">
                    Professional washing and detailing with paint-safe products and expert care
                </p>
                <div class="flex flex-col gap-3 sm:flex-row sm:gap-4">
                    <a href="{{ route('booking.index') }}" class="btn-cta">Book Now</a>
                    <a href="{{ route('contact.index') }}" class="btn-cta-outline">Contact Us</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-custom py-12 sm:py-16">
        <div class="mb-10 text-center sm:mb-12">
            <h2 class="mb-3 text-2xl font-bold text-graphite-900 dark:text-white sm:text-3xl">Choose Your Service</h2>
            <p class="mx-auto max-w-2xl text-sm text-graphite-600 dark:text-graphite-300 sm:text-base">
                From basic washes to premium detailing packages
            </p>
        </div>

        @php
            $allServices = $categories->flatMap(fn ($c) => $c->services)->sortBy('price');
        @endphp

        @if($allServices->isEmpty())
            <div class="py-16 text-center">
                <p class="text-lg text-graphite-600">No services available at the moment. Please check back later.</p>
            </div>
        @else
            <div class="grid grid-cols-1 items-stretch gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3 lg:gap-8">
                @foreach($allServices as $service)
                    <div id="{{ $service->slug }}" class="h-full">
                        <x-service-card :service="$service" />
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-12 text-center sm:mt-16">
            <a href="{{ route('booking.index') }}" class="btn-cta">Book Your Service Now</a>
        </div>
    </div>
</x-layouts.public>

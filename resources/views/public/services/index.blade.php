<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription">
    <div class="relative flex h-[70vh] items-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img
                src="{{ asset('images/hero.jpg') }}"
                alt="Premium Car Care Services"
                class="h-full w-full object-cover brightness-[0.6]"
                style="object-position: center 30%;"
            >
        </div>
        <div class="container-custom relative z-10 text-white">
            <div class="max-w-2xl">
                <h1 class="mb-4 text-5xl font-bold">Premium <span class="text-blue-400">Car Care</span> Services</h1>
                <p class="mb-8 text-2xl leading-relaxed font-light">
                    Professional car washing and detailing services with premium quality products and expert care
                </p>
                <div class="flex flex-col gap-4 sm:flex-row">
                    <a href="{{ route('booking.index') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 font-medium text-white transition-all duration-300 hover:scale-105 hover:bg-blue-700">
                        Book Now
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                    <a href="{{ route('contact.index') }}" class="inline-flex items-center justify-center rounded-lg border-2 border-white px-8 py-3 font-medium text-white transition-all duration-300 hover:bg-white hover:text-gray-800">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-16">
        <div class="mb-12 text-center">
            <h2 class="mb-4 text-3xl font-bold text-gray-800">Choose Your Service</h2>
            <p class="mx-auto max-w-2xl text-lg text-gray-600">
                From basic washes to premium detailing packages, we have the perfect service for your car
            </p>
        </div>

        @php
            $allServices = $categories->flatMap(fn ($c) => $c->services)->sortBy('price');
        @endphp

        @if($allServices->isEmpty())
            <div class="py-16 text-center">
                <p class="text-lg text-gray-600">No services available at the moment. Please check back later.</p>
            </div>
        @else
            <div class="grid grid-cols-1 items-stretch gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($allServices as $service)
                    <div id="{{ $service->slug }}">
                        <x-service-card :service="$service" />
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-16 text-center">
            <a href="{{ route('booking.index') }}" class="inline-block rounded-lg bg-blue-600 px-8 py-3 font-semibold text-white transition-colors duration-200 hover:bg-blue-700">
                Book Your Service Now
            </a>
        </div>
    </div>
</x-layouts.public>

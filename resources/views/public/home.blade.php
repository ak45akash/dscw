<x-layouts.public :seo-title="$businessName . ' | Premium Car Care in ' . $city">
    {{-- Hero --}}
    <div class="relative flex h-[90vh] items-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            @foreach([5, 15, 25, 35, 45, 55, 65, 75, 85, 10, 30, 50, 70, 90, 20] as $i => $left)
                <div
                    class="absolute h-4 w-4 animate-float rounded-full bg-white/10"
                    style="left: {{ $left }}%; top: {{ ($i * 17) % 90 + 5 }}%; animation-delay: {{ ($i * 0.4) }}s; animation-duration: {{ 3 + ($i % 4) }}s;"
                ></div>
            @endforeach
        </div>

        <div class="absolute inset-0 z-0">
            <img
                src="{{ asset('images/exterior-detailing.jpg') }}"
                alt="Car washing"
                class="h-full w-full scale-110 object-cover brightness-[0.6] transition-transform duration-[10000ms] hover:scale-105"
                style="object-position: center 25%;"
            >
        </div>

        <div class="animate-fadeInUp absolute inset-0 z-0 bg-gradient-to-r from-blue-900/40 to-black/30"></div>

        <div class="container-custom relative z-10 text-white">
            <div class="max-w-2xl animate-fadeInUp" style="animation-delay: 0.3s;">
                <h1 class="mb-6 text-5xl leading-tight font-bold">
                    <span class="animate-pulse text-blue-400">Diamond</span> Steam Car Wash in <span class="gradient-text">{{ $city }}</span>
                </h1>
                <p class="mb-8 text-xl leading-relaxed font-light">
                    {{ $tagline }}. Experience the ultimate car care with our professional washing and detailing services using eco-friendly methods.
                </p>
                <div class="flex animate-slideInFromBottom flex-wrap gap-4" style="animation-delay: 0.6s;">
                    <a href="{{ route('booking.index') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-8 py-4 font-medium text-white shadow-lg transition-all duration-300 hover:scale-105 hover:bg-blue-700">
                        Book Now
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </a>
                    <a href="{{ route('services.index') }}" class="inline-flex items-center rounded-lg border-2 border-white bg-transparent px-8 py-4 font-medium text-white transition-all duration-300 hover:scale-105 hover:bg-white hover:text-blue-600">
                        Our Services
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="absolute right-0 bottom-12 left-0 z-10">
            <div class="container-custom">
                <div class="flex animate-slideInFromBottom flex-wrap justify-center gap-4 md:gap-8" style="animation-delay: 1s;">
                    @foreach([
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Premium Quality'],
                        ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => 'Fast Service'],
                        ['icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h.5A2.5 2.5 0 0020 5.5v-1.65', 'label' => 'Eco-Friendly'],
                    ] as $badge)
                        <div class="flex items-center rounded-full border border-white/20 bg-white/10 px-6 py-3 text-white shadow-lg backdrop-blur-md">
                            <svg class="mr-2 h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $badge['icon'] }}"></path></svg>
                            <span class="font-medium">{{ $badge['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Services --}}
    <section class="bg-gradient-to-b from-gray-50 to-white py-24">
        <div class="container-custom">
            <div class="mb-16 text-center">
                <h2 class="mb-4 text-4xl font-bold text-gray-800">Our Services</h2>
                <div class="mx-auto mb-6 h-1 w-24 bg-blue-600"></div>
                <p class="mx-auto max-w-2xl text-lg text-gray-600">
                    Choose from our range of professional car washing and detailing services. We use eco-friendly products and the latest techniques to give your car the care it deserves.
                </p>
            </div>

            @if($featuredServices->isNotEmpty())
                <div class="grid grid-cols-1 items-stretch gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($featuredServices as $service)
                        <x-service-card :service="$service" />
                    @endforeach
                </div>
            @else
                <div class="py-16 text-center">
                    <p class="text-gray-600">No services available at the moment.</p>
                </div>
            @endif

            <div class="mt-16 text-center">
                <a href="{{ route('services.index') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-8 py-3 font-medium text-white shadow-md transition-all duration-300 hover:scale-105 hover:bg-blue-700">
                    View All Services
                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Process --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-blue-50 py-20" x-data="scrollSection">
        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 h-32 w-32 animate-float rounded-full bg-blue-200/20"></div>
            <div class="absolute right-10 bottom-20 h-24 w-24 animate-float rounded-full bg-purple-200/20" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/4 h-16 w-16 animate-float rounded-full bg-green-200/20" style="animation-delay: 2s;"></div>
        </div>

        <div class="relative z-10 container mx-auto px-4">
            <div class="mb-16 text-center transition-all duration-1000" :class="visible ? 'animate-fadeInUp opacity-100' : 'translate-y-10 opacity-0'">
                <h2 class="mb-4 text-4xl font-bold text-gray-800 md:text-5xl">
                    Our <span class="gradient-text">Premium Process</span>
                </h2>
                <p class="mx-auto max-w-3xl text-xl text-gray-600">
                    Experience the Diamond Steam difference with our meticulous 4-step process
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['id' => 1, 'title' => 'Book Online', 'description' => 'Choose your service and book instantly through our easy-to-use platform', 'icon' => '📱', 'color' => 'from-blue-500 to-blue-600'],
                    ['id' => 2, 'title' => 'Pre-Wash Inspection', 'description' => 'Our team inspects your vehicle and prepares the perfect treatment plan', 'icon' => '🔍', 'color' => 'from-green-500 to-green-600'],
                    ['id' => 3, 'title' => 'Premium Wash', 'description' => 'Professional cleaning using eco-friendly products and advanced techniques', 'icon' => '🚿', 'color' => 'from-purple-500 to-purple-600'],
                    ['id' => 4, 'title' => 'Quality Check', 'description' => 'Final inspection to ensure your vehicle meets our premium standards', 'icon' => '✨', 'color' => 'from-orange-500 to-orange-600'],
                ] as $index => $step)
                    <div class="group relative transition-all duration-700" :class="visible ? 'animate-fadeInUp opacity-100' : 'translate-y-10 opacity-0'" style="animation-delay: {{ $index * 200 }}ms;">
                        @if($index < 3)
                            <div class="absolute top-1/2 -right-4 z-10 hidden h-0.5 w-8 -translate-y-1/2 bg-gradient-to-r from-blue-300 to-transparent lg:block">
                                <div class="absolute top-1/2 right-0 h-2 w-2 -translate-y-1/2 animate-pulse rounded-full bg-blue-400"></div>
                            </div>
                        @endif

                        <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-8 shadow-lg transition-all duration-300 hover-lift group-hover:shadow-2xl">
                            <div class="absolute inset-0 bg-gradient-to-br {{ $step['color'] }} opacity-0 transition-opacity duration-300 group-hover:opacity-5"></div>
                            <div class="absolute -top-4 -right-4 flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br {{ $step['color'] }} text-lg font-bold text-white shadow-lg">
                                {{ $step['id'] }}
                            </div>
                            <div class="mb-6 animate-wave text-6xl transition-transform duration-300 group-hover:scale-110">{{ $step['icon'] }}</div>
                            <h3 class="mb-4 text-2xl font-bold text-gray-800 transition-colors duration-300 group-hover:text-blue-600">{{ $step['title'] }}</h3>
                            <p class="leading-relaxed text-gray-600">{{ $step['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-16 text-center transition-all duration-1000" :class="visible ? 'animate-slideInFromBottom opacity-100' : 'translate-y-10 opacity-0'" style="animation-delay: 800ms;">
                <div class="mx-auto max-w-2xl rounded-2xl border border-gray-100 bg-white p-8 shadow-lg">
                    <h3 class="mb-4 text-2xl font-bold text-gray-800">Ready to Experience Premium Care?</h3>
                    <p class="mb-6 text-gray-600">Join thousands of satisfied customers who trust Diamond Steam with their vehicles</p>
                    <a href="{{ route('booking.index') }}" class="inline-block rounded-full bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-4 font-semibold text-white shadow-lg transition-all duration-300 hover:from-blue-700 hover:to-purple-700 hover:shadow-xl transform hover:scale-105">
                        Book Your Service Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800 py-20" x-data="statsSection">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-black/20"></div>
            @foreach(range(0, 19) as $i)
                <div
                    class="absolute h-2 w-2 animate-float rounded-full bg-white/20"
                    style="left: {{ ($i * 13 + 7) % 100 }}%; top: {{ ($i * 19 + 11) % 100 }}%; animation-delay: {{ ($i * 0.3) }}s; animation-duration: {{ 3 + ($i % 4) }}s;"
                ></div>
            @endforeach
        </div>

        <div class="relative z-10 container mx-auto px-4">
            <div class="mb-16 text-center transition-all duration-1000" :class="visible ? 'animate-fadeInUp opacity-100' : 'translate-y-10 opacity-0'">
                <h2 class="mb-4 text-4xl font-bold text-white md:text-5xl">
                    Our <span class="text-yellow-300">Achievements</span>
                </h2>
                <p class="mx-auto max-w-3xl text-xl text-blue-100">
                    Numbers that speak for our commitment to excellence
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['number' => 5000, 'suffix' => '+', 'label' => 'Happy Customers', 'icon' => '😊', 'color' => 'from-blue-500 to-blue-600'],
                    ['number' => 15000, 'suffix' => '+', 'label' => 'Cars Washed', 'icon' => '🚗', 'color' => 'from-green-500 to-green-600'],
                    ['number' => 98, 'suffix' => '%', 'label' => 'Satisfaction Rate', 'icon' => '⭐', 'color' => 'from-yellow-500 to-orange-500'],
                    ['number' => 3, 'suffix' => '', 'label' => 'Years Experience', 'icon' => '🏆', 'color' => 'from-purple-500 to-purple-600'],
                ] as $index => $stat)
                    <div class="text-center transition-all duration-700" :class="visible ? 'animate-fadeInUp opacity-100' : 'translate-y-10 opacity-0'" style="animation-delay: {{ $index * 200 }}ms;">
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-8 backdrop-blur-lg transition-all duration-300 hover-lift group hover:bg-white/20">
                            <div class="mb-4 animate-wave text-6xl transition-transform duration-300 group-hover:scale-110">{{ $stat['icon'] }}</div>
                            <div class="mb-4">
                                <span class="text-5xl font-bold text-white md:text-6xl" x-text="counts[{{ $index }}].toLocaleString()"></span>
                                <span class="text-3xl font-bold text-yellow-300 md:text-4xl">{{ $stat['suffix'] }}</span>
                            </div>
                            <p class="text-xl font-semibold text-blue-100">{{ $stat['label'] }}</p>
                            <div class="mx-auto mt-4 h-1 w-16 rounded-full bg-gradient-to-r {{ $stat['color'] }} transition-all duration-300 group-hover:w-24"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Choose Us --}}
    <section class="relative overflow-hidden bg-white py-20" x-data="scrollSection">
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 h-1 w-full bg-gradient-to-r from-blue-500 via-purple-500 to-blue-500"></div>
            <div class="absolute top-20 right-10 h-64 w-64 animate-float rounded-full bg-gradient-to-br from-blue-100 to-purple-100 opacity-30"></div>
            <div class="absolute bottom-20 left-10 h-48 w-48 animate-float rounded-full bg-gradient-to-br from-green-100 to-blue-100 opacity-30" style="animation-delay: 2s;"></div>
        </div>

        <div class="relative z-10 container mx-auto px-4">
            <div class="mb-16 text-center transition-all duration-1000" :class="visible ? 'animate-fadeInUp opacity-100' : 'translate-y-10 opacity-0'">
                <h2 class="mb-4 text-4xl font-bold text-gray-800 md:text-5xl">
                    Why Choose <span class="gradient-text">Diamond Steam</span>?
                </h2>
                <p class="mx-auto max-w-3xl text-xl text-gray-600">
                    Discover what makes us the preferred choice for premium car care services
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach([
                    ['title' => 'Eco-Friendly Products', 'description' => 'We use only biodegradable, environmentally safe cleaning products that protect your car and the planet.', 'icon' => '🌱', 'color' => 'from-green-500 to-emerald-600', 'bg' => 'bg-green-50'],
                    ['title' => 'Professional Team', 'description' => 'Our trained professionals have years of experience and treat every vehicle with meticulous care.', 'icon' => '👨‍🔧', 'color' => 'from-blue-500 to-blue-600', 'bg' => 'bg-blue-50'],
                    ['title' => 'Advanced Equipment', 'description' => 'State-of-the-art washing equipment and techniques ensure the best results for your vehicle.', 'icon' => '⚡', 'color' => 'from-purple-500 to-purple-600', 'bg' => 'bg-purple-50'],
                    ['title' => 'Time Efficient', 'description' => 'Quick service without compromising quality. Most services completed within 30-60 minutes.', 'icon' => '⏱️', 'color' => 'from-orange-500 to-red-500', 'bg' => 'bg-orange-50'],
                    ['title' => 'Satisfaction Guarantee', 'description' => "100% satisfaction guaranteed or we'll redo the service at no extra cost. Your happiness is our priority.", 'icon' => '✅', 'color' => 'from-teal-500 to-cyan-600', 'bg' => 'bg-teal-50'],
                    ['title' => 'Affordable Pricing', 'description' => 'Premium quality services at competitive prices. Great value for money with transparent pricing.', 'icon' => '💰', 'color' => 'from-yellow-500 to-yellow-600', 'bg' => 'bg-yellow-50'],
                ] as $index => $feature)
                    <div class="group transition-all duration-700" :class="visible ? 'animate-fadeInUp opacity-100' : 'translate-y-10 opacity-0'" style="animation-delay: {{ $index * 150 }}ms;">
                        <div class="{{ $feature['bg'] }} relative h-full overflow-hidden rounded-2xl border-2 border-transparent p-8 transition-all duration-300 hover-lift hover:border-gray-200">
                            <div class="absolute inset-0 bg-gradient-to-br {{ $feature['color'] }} opacity-0 transition-opacity duration-500 group-hover:opacity-5"></div>
                            <div class="relative mb-6">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br {{ $feature['color'] }} shadow-lg transition-all duration-300 group-hover:scale-110 group-hover:shadow-xl">
                                    <span class="animate-wave text-3xl group-hover:animate-bounce">{{ $feature['icon'] }}</span>
                                </div>
                            </div>
                            <h3 class="mb-4 text-2xl font-bold text-gray-800 transition-colors duration-300 group-hover:text-blue-600">{{ $feature['title'] }}</h3>
                            <p class="leading-relaxed text-gray-600">{{ $feature['description'] }}</p>
                            <div class="absolute bottom-0 left-0 h-1 w-0 rounded-b-2xl bg-gradient-to-r {{ $feature['color'] }} transition-all duration-500 group-hover:w-full"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-16 text-center transition-all duration-1000" :class="visible ? 'animate-slideInFromBottom opacity-100' : 'translate-y-10 opacity-0'" style="animation-delay: 900ms;">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 to-purple-600 p-12 text-white">
                    <div class="absolute inset-0 animate-shimmer bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600"></div>
                    <div class="relative z-10">
                        <h3 class="mb-4 text-3xl font-bold md:text-4xl">Experience the Diamond Steam Difference</h3>
                        <p class="mx-auto mb-8 max-w-2xl text-xl text-blue-100">
                            Join thousands of satisfied customers who trust us with their vehicles. Book your premium car wash service today!
                        </p>
                        <div class="flex flex-col justify-center gap-4 sm:flex-row">
                            <a href="{{ route('booking.index') }}" class="rounded-full bg-white px-8 py-4 font-semibold text-blue-600 shadow-lg transition-all duration-300 hover:scale-105 hover:bg-gray-100 hover:shadow-xl">Book Now</a>
                            <a href="{{ route('services.index') }}" class="rounded-full border-2 border-white px-8 py-4 font-semibold text-white transition-all duration-300 hover:scale-105 hover:bg-white hover:text-blue-600">View Services</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Benefits --}}
    <section class="relative overflow-hidden py-24">
        <div class="absolute inset-0 z-0 opacity-5">
            <div class="h-full w-full bg-cover bg-center" style="background-image: url('{{ asset('images/detailing.jpg') }}')"></div>
        </div>

        <div class="container-custom relative z-10">
            <div class="animate-on-scroll mb-16 text-center" x-data="scrollReveal">
                <h2 class="mb-4 text-4xl font-bold text-gray-800">Why Choose Diamond Steam Car Wash</h2>
                <div class="mx-auto mb-6 h-1 w-24 bg-blue-600"></div>
                <p class="mx-auto max-w-2xl text-lg text-gray-600">
                    We are committed to providing the best car wash experience with quality service, eco-friendly products, and professional staff.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach([
                    ['title' => 'Time-Saving', 'description' => 'Quick turnaround times with our efficient processes. Get your car back faster.', 'path' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['title' => 'Eco-Friendly', 'description' => 'We use eco-friendly products and water-saving techniques to protect the environment.', 'path' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h.5A2.5 2.5 0 0020 5.5v-1.65'],
                    ['title' => 'Professional Staff', 'description' => 'Our trained professionals handle your vehicle with care and expertise.', 'path' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ['title' => 'Satisfaction Guaranteed', 'description' => "We ensure 100% satisfaction with our services or we'll rewash your vehicle.", 'path' => 'M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5'],
                    ['title' => 'Vehicle Protection', 'description' => "Our methods and products are safe for your car's paint and finish.", 'path' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['title' => 'All Vehicle Types', 'description' => 'We service all types of vehicles from compact cars to SUVs and luxury vehicles.', 'path' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                ] as $index => $benefit)
                    <div class="group animate-on-scroll overflow-hidden rounded-xl shadow-lg" x-data="scrollReveal" style="transition-delay: {{ $index * 100 }}ms;">
                        <div class="flex h-24 items-center justify-center bg-gradient-to-r from-blue-600 to-blue-700 p-6">
                            <div class="transition-transform duration-300 group-hover:scale-110">
                                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $benefit['path'] }}"></path></svg>
                            </div>
                        </div>
                        <div class="bg-white p-6">
                            <h3 class="mb-3 text-xl font-bold text-gray-800">{{ $benefit['title'] }}</h3>
                            <p class="text-gray-600">{{ $benefit['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    @if($testimonials->isNotEmpty())
        @php
            $testimonialImages = ['images/dry-clean.jpg', 'images/interior-detail.jpg', 'images/ppf.jpg', 'images/steam-wash.jpg'];
            $carouselItems = $testimonials->map(function ($t, $i) use ($testimonialImages, $city) {
                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'location' => $city,
                    'rating' => $t->rating,
                    'comment' => $t->review,
                    'image' => asset($testimonialImages[$i % count($testimonialImages)]),
                ];
            })->values();
        @endphp

        <section class="relative overflow-hidden bg-gradient-to-b from-blue-50 to-white py-24">
            <div class="pointer-events-none absolute inset-0 opacity-20">
                <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-blue-500 blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-blue-400 blur-3xl"></div>
            </div>

            <div class="container-custom relative z-10">
                <div class="animate-on-scroll mb-16 text-center" x-data="scrollReveal">
                    <h2 class="mb-4 text-4xl font-bold text-gray-800">What Our Customers Say</h2>
                    <div class="mx-auto mb-6 h-1 w-24 bg-blue-600"></div>
                    <p class="mx-auto max-w-2xl text-lg text-gray-600">
                        We take pride in providing excellent service to our customers. Here's what some of them have to say about their experience with Diamond Steam.
                    </p>
                </div>

                <div class="mx-auto max-w-5xl px-4" x-data="testimonialCarousel(@js($carouselItems))">
                    <div
                        class="relative overflow-hidden rounded-xl bg-white p-6 shadow-xl md:p-10"
                        @mouseenter="autoplay = false"
                        @mouseleave="autoplay = true"
                    >
                        <div class="absolute top-0 left-0 h-40 w-40 -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-600 opacity-10"></div>
                        <div class="absolute top-10 right-10">
                            <svg class="h-16 w-16 text-blue-100" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"></path></svg>
                        </div>

                        <div class="flex flex-col items-center gap-10 lg:flex-row">
                            <div class="w-full lg:w-1/3">
                                <div class="relative mb-6 h-80 overflow-hidden rounded-xl shadow-lg lg:mb-0 lg:h-96">
                                    <template x-for="(item, index) in items" :key="item.id">
                                        <img
                                            x-show="current === index"
                                            x-transition:enter="transition ease-out duration-500"
                                            x-transition:enter-start="opacity-0"
                                            x-transition:enter-end="opacity-100"
                                            :src="item.image"
                                            :alt="item.name"
                                            class="absolute inset-0 h-full w-full object-cover"
                                            style="object-position: center 25%;"
                                        >
                                    </template>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                </div>
                            </div>

                            <div class="w-full lg:w-2/3">
                                <template x-for="(item, index) in items" :key="'content-' + item.id">
                                    <div x-show="current === index" x-transition>
                                        <div class="mb-6 flex">
                                            <template x-for="i in 5" :key="i">
                                                <svg class="h-6 w-6" :class="i <= item.rating ? 'text-yellow-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8-2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            </template>
                                        </div>
                                        <p class="mb-8 text-xl leading-relaxed text-gray-700 italic" x-text="'&quot;' + item.comment + '&quot;'"></p>
                                        <div>
                                            <h4 class="text-xl font-bold text-gray-800" x-text="item.name"></h4>
                                            <p class="text-blue-600" x-text="item.location"></p>
                                        </div>
                                    </div>
                                </template>

                                <div class="mt-8 flex gap-3">
                                    <button type="button" @click="prev()" class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 transition-colors hover:bg-gray-300" aria-label="Previous testimonial">
                                        <svg class="h-5 w-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                    <button type="button" @click="next()" class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-white transition-colors hover:bg-blue-700" aria-label="Next testimonial">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="absolute right-10 bottom-10 rounded-full bg-gray-100 px-3 py-1 text-sm font-medium" x-text="(current + 1) + ' / ' + items.length"></div>
                    </div>
                </div>
            </div>
        </section>
    @endif
</x-layouts.public>

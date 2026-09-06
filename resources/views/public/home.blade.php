<x-layouts.public
    :seo-title="$businessName . ' | Premium Steam Car Wash in ' . $city"
    :seo-description="'Book paint-safe steam wash, detailing, ceramic coating, and PPF at Diamond Steam Car Wash in ' . $city . ' and Matour, Punjab. Fast online booking.'"
    seo-image="images/exterior-detailing.jpg"
>
    {{-- Hero --}}
    <div class="relative flex min-h-[62vh] items-center overflow-hidden sm:min-h-[72vh] lg:min-h-[80vh]">
        <x-parallax-bg src="images/exterior-detailing.jpg" alt="Professional steam car wash in Punjab" :speed="0.8" brightness="0.52" position="center 25%" height="180%" />
        <div class="absolute inset-0 z-0 bg-gradient-to-r from-graphite-950/75 via-brand-950/45 to-transparent"></div>

        <div class="container-custom relative z-10 py-16 text-white sm:py-20 lg:py-24">
            <div class="max-w-2xl animate-on-scroll" x-data="scrollReveal">
                <p class="mb-3 text-sm font-semibold tracking-wide text-accent-400 uppercase">{{ $city }} &amp; Matour</p>
                <h1 class="mb-5 text-3xl leading-tight font-bold sm:text-4xl md:text-5xl lg:text-6xl">
                    Diamond Steam Car Wash
                </h1>
                <p class="mb-8 max-w-xl text-base leading-relaxed text-white/90 sm:text-lg md:text-xl">
                    {{ $tagline }}. Professional steam washing and detailing with paint-safe methods.
                </p>
                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:gap-4">
                    <a href="{{ route('booking.index') }}" class="btn-cta">Book Now</a>
                    <a href="{{ route('services.index') }}" class="btn-cta-outline">Our Services</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Services --}}
    <section class="bg-graphite-50 py-14 dark:bg-graphite-900 sm:py-20 lg:py-24">
        <div class="container-custom">
            <div class="mb-10 text-center sm:mb-14">
                <h2 class="mb-3 text-2xl font-bold text-graphite-900 dark:text-white sm:text-3xl md:text-4xl">Our Services</h2>
                <div class="mx-auto mb-4 h-1 w-16 bg-brand-600 sm:mb-6 sm:w-20"></div>
                <p class="mx-auto max-w-2xl text-sm text-graphite-600 dark:text-graphite-300 sm:text-base md:text-lg">
                    Professional washes and detailing packages tailored for everyday drivers and premium vehicles.
                </p>
            </div>

            @if($featuredServices->isNotEmpty())
                <div class="grid grid-cols-1 items-stretch gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3 lg:gap-8">
                    @foreach($featuredServices as $service)
                        <x-service-card :service="$service" />
                    @endforeach
                </div>
            @else
                <p class="py-12 text-center text-graphite-600">No services available at the moment.</p>
            @endif

            <div class="mt-10 text-center sm:mt-14">
                <a href="{{ route('services.index') }}" class="btn-cta">View All Services</a>
            </div>
        </div>
    </section>

    {{-- Process --}}
    <section class="relative flex min-h-[32rem] items-center overflow-hidden py-24 sm:min-h-[36rem] sm:py-28 lg:min-h-[40rem] lg:py-32">
        <x-parallax-bg
            src="images/steam-wash.jpg"
            alt=""
            :speed="0.75"
            brightness="0.55"
            position="center 40%"
            height="180%"
        >
            <div class="absolute inset-0 bg-white/88 dark:bg-graphite-950/90"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-brand-600/5 via-transparent to-brand-700/10 dark:from-brand-500/10 dark:to-brand-900/20"></div>
        </x-parallax-bg>

        <div class="container-custom relative z-10 w-full">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="mb-3 text-2xl font-bold text-graphite-900 dark:text-white sm:text-3xl md:text-4xl">How It Works</h2>
                <div class="mx-auto mb-4 h-1 w-16 bg-brand-600 sm:w-20"></div>
                <p class="mx-auto max-w-2xl text-sm text-graphite-600 dark:text-graphite-300 sm:text-base md:text-lg">
                    A clear four-step process from booking to handover.
                </p>
            </div>

            <div class="grid grid-cols-1 items-stretch gap-6 sm:grid-cols-2 sm:gap-7 lg:grid-cols-4 lg:gap-8">
                @foreach([
                    ['id' => '01', 'title' => 'Book Online', 'description' => 'Pick a service, location, and time slot that fits your schedule.', 'path' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['id' => '02', 'title' => 'Vehicle Check', 'description' => 'We review your car and confirm the right treatment for its condition.', 'path' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                    ['id' => '03', 'title' => 'Professional Care', 'description' => 'From washes to detailing and protection, we follow a paint-safe process.', 'path' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                    ['id' => '04', 'title' => 'Handover', 'description' => 'Final quality check, then we return your vehicle ready to drive.', 'path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ] as $step)
                    <article class="hover-lift flex h-full min-h-[14rem] flex-col rounded-xl border border-graphite-200/90 bg-white/95 p-7 shadow-sm backdrop-blur-sm dark:border-graphite-700 dark:bg-graphite-900/95 sm:min-h-[15.5rem] sm:p-8">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="text-sm font-semibold tracking-wide text-brand-600 dark:text-accent-400">{{ $step['id'] }}</span>
                            <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand-600 text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $step['path'] }}"></path></svg>
                            </span>
                        </div>
                        <h3 class="mb-3 text-lg font-semibold text-graphite-900 dark:text-white">{{ $step['title'] }}</h3>
                        <p class="flex-1 text-sm leading-relaxed text-graphite-600 dark:text-graphite-300">{{ $step['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Trusted Local Care — image + stats + CTA --}}
    <section class="relative flex min-h-[34rem] items-center overflow-hidden py-24 text-white sm:min-h-[40rem] sm:py-28 lg:min-h-[44rem] lg:py-36" x-data="statsSection">
        <x-parallax-bg src="images/facility-1.jpg" alt="Diamond Steam Car Wash facility" :speed="0.95" brightness="1" position="center center" height="190%">
            <div class="absolute inset-0 bg-gradient-to-r from-graphite-950/90 via-brand-950/80 to-brand-900/70"></div>
        </x-parallax-bg>

        <div class="container-custom relative z-10 w-full">
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                <div>
                    <p class="mb-3 text-sm font-semibold uppercase tracking-wide text-accent-400">Trusted Local Care</p>
                    <h2 class="mb-4 text-2xl font-bold sm:text-3xl md:text-4xl">Clean finishes. Consistent standards.</h2>
                    <p class="mb-8 max-w-xl text-sm leading-relaxed text-white/80 sm:text-base">
                        From Sector 66 to Matour, we keep every bay process paint-safe, transparent, and easy to book — so your car looks after every visit.
                    </p>
                    <div class="flex flex-col gap-3 sm:flex-row sm:gap-4">
                        <a href="{{ route('booking.index') }}" class="btn-cta">Book a Slot</a>
                        <a href="{{ route('gallery.index') }}" class="btn-cta-outline">See Our Work</a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 sm:gap-5">
                    @foreach([
                        ['value' => '5,000+', 'label' => 'Happy customers', 'index' => 0],
                        ['value' => '15,000+', 'label' => 'Cars washed', 'index' => 1],
                        ['value' => '98%', 'label' => 'Satisfaction', 'index' => 2],
                        ['value' => '2', 'label' => 'Punjab locations', 'index' => 3],
                    ] as $stat)
                        <div class="flex min-h-[8.5rem] h-full flex-col justify-center rounded-xl border border-white/15 bg-white/10 px-4 py-8 text-center backdrop-blur-sm sm:min-h-[10rem] sm:px-6 sm:py-10">
                            <div class="text-2xl font-bold text-white sm:text-3xl md:text-4xl">{{ $stat['value'] }}</div>
                            <div class="mt-2 text-xs text-white/75 sm:text-sm">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Why choose us --}}
    <section class="bg-white py-14 dark:bg-graphite-950 sm:py-20 lg:py-24">
        <div class="container-custom">
            <div class="mb-10 text-center sm:mb-14">
                <h2 class="mb-3 text-2xl font-bold text-graphite-900 dark:text-white sm:text-3xl md:text-4xl">Why Choose Diamond Steam</h2>
                <div class="mx-auto mb-4 h-1 w-16 bg-brand-600 sm:w-20"></div>
                <p class="mx-auto max-w-2xl text-sm text-graphite-600 dark:text-graphite-300 sm:text-base md:text-lg">
                    Practical advantages that matter every time you book.
                </p>
            </div>

            <div class="grid grid-cols-1 items-stretch gap-5 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6">
                @foreach([
                    ['title' => 'Eco-conscious methods', 'description' => 'Lower water use and paint-safe products for everyday maintenance.', 'path' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h.5A2.5 2.5 0 0020 5.5v-1.65'],
                    ['title' => 'Trained technicians', 'description' => 'Consistent process control for interiors, exteriors, and protection work.', 'path' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ['title' => 'Reliable turnaround', 'description' => 'Clear slot booking with predictable service windows.', 'path' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['title' => 'Paint-first approach', 'description' => 'Techniques designed to reduce swirl risk and preserve finish.', 'path' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['title' => 'Transparent pricing', 'description' => 'Published packages with optional add-ons you can choose at booking.', 'path' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['title' => 'Two Punjab locations', 'description' => 'Sector 66, SAS Nagar and Matour — book the centre that suits you.', 'path' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                ] as $feature)
                    <article class="hover-lift flex h-full flex-col rounded-xl border border-graphite-200 bg-graphite-50 p-6 dark:border-graphite-700 dark:bg-graphite-900">
                        <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-brand-600 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $feature['path'] }}"></path></svg>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-graphite-900 dark:text-white">{{ $feature['title'] }}</h3>
                        <p class="flex-1 text-sm leading-relaxed text-graphite-600 dark:text-graphite-300">{{ $feature['description'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="mt-10 rounded-xl bg-brand-700 px-6 py-8 text-center text-white sm:mt-14 sm:px-10 sm:py-10">
                <h3 class="mb-3 text-xl font-bold sm:text-2xl md:text-3xl">Ready to book?</h3>
                <p class="mx-auto mb-6 max-w-xl text-sm text-brand-100 sm:text-base">Pick a service and location online — same-day slots when available.</p>
                <div class="flex flex-col justify-center gap-3 sm:flex-row sm:gap-4">
                    <a href="{{ route('booking.index') }}" class="inline-flex justify-center rounded-lg bg-white px-6 py-3.5 font-semibold text-brand-800 transition hover:bg-brand-50">Book Now</a>
                    <a href="{{ route('contact.index') }}" class="btn-cta-outline">Contact Us</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Locations --}}
    @if($locations->isNotEmpty())
        <section class="bg-graphite-50 py-14 dark:bg-graphite-900 sm:py-20">
            <div class="container-custom">
                <div class="mb-10 text-center sm:mb-12">
                    <h2 class="mb-3 text-2xl font-bold text-graphite-900 dark:text-white sm:text-3xl md:text-4xl">Our Locations</h2>
                    <div class="mx-auto mb-4 h-1 w-16 bg-brand-600 sm:w-20"></div>
                </div>
                <div class="mx-auto grid max-w-4xl grid-cols-1 items-stretch gap-5 sm:grid-cols-2 sm:gap-6">
                    @foreach($locations as $location)
                        <article class="hover-lift flex h-full flex-col rounded-xl border border-graphite-200 bg-white p-6 shadow-sm dark:border-graphite-700 dark:bg-graphite-900">
                            <h3 class="text-lg font-semibold text-graphite-900 dark:text-white">{{ $location->name }}</h3>
                            <p class="mt-3 flex-1 text-sm leading-relaxed text-graphite-600 dark:text-graphite-300">{{ $location->fullAddress() }}</p>
                            @if($location->phone)
                                <a href="tel:{{ $location->phone }}" class="mt-4 text-sm font-medium text-brand-700 hover:underline dark:text-accent-400">{{ $location->phone }}</a>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Blog --}}
    @if($latestPosts->isNotEmpty())
        <section class="relative overflow-hidden bg-brand-950 py-14 text-white sm:py-20 lg:py-24">
            <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 15% 20%, #14b8a6 0, transparent 35%), radial-gradient(circle at 85% 10%, #06b6d4 0, transparent 30%);"></div>
            <div class="container-custom relative">
                <div class="mb-8 flex flex-col gap-4 sm:mb-12 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="mb-3 text-2xl font-bold sm:text-3xl md:text-4xl">From the Blog</h2>
                        <div class="mb-3 h-1 w-16 bg-accent-400 sm:w-20"></div>
                        <p class="max-w-2xl text-sm text-brand-100 sm:text-base">Car care tips and protection guidance from our team.</p>
                    </div>
                    <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-accent-300 hover:text-accent-200">View all articles →</a>
                </div>
                <div class="grid grid-cols-1 items-stretch gap-5 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6">
                    @foreach($latestPosts as $post)
                        <x-blog-card :post="$post" variant="dark" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Testimonials --}}
    @if($testimonials->isNotEmpty())
        @php
            $testimonialImages = ['images/dry-clean.jpg', 'images/interior-detail.jpg', 'images/ppf.jpg', 'images/steam-wash.jpg'];
            $carouselItems = $testimonials->map(function ($t, $i) use ($testimonialImages, $city) {
                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'location' => $city,
                    'service' => $t->service,
                    'rating' => (int) $t->rating,
                    'comment' => \Illuminate\Support\Str::limit(trim($t->review), 160),
                    'image' => asset($testimonialImages[$i % count($testimonialImages)]),
                ];
            })->values();
        @endphp

        <section class="bg-graphite-50 py-14 dark:bg-graphite-900 sm:py-20 lg:py-24">
            <div class="container-custom">
                <div class="mb-10 text-center sm:mb-12">
                    <h2 class="mb-3 text-2xl font-bold text-graphite-900 dark:text-white sm:text-3xl md:text-4xl">Customer Feedback</h2>
                    <div class="mx-auto mb-4 h-1 w-16 bg-brand-600 sm:w-20"></div>
                    <p class="mx-auto max-w-2xl text-sm text-graphite-600 dark:text-graphite-300 sm:text-base">Recent reviews from drivers who book with us regularly.</p>
                </div>

                <div
                    class="relative"
                    x-data="testimonialCarousel(@js($carouselItems))"
                    @mouseenter="autoplay = false"
                    @mouseleave="autoplay = true"
                    @focusin="autoplay = false"
                    @focusout="autoplay = true"
                >
                    <div class="overflow-hidden" x-ref="viewport">
                        <div
                            class="testimonial-track flex items-stretch will-change-transform"
                            x-ref="track"
                            :style="trackStyle"
                        >
                            <template x-for="item in items" :key="item.id">
                                <article
                                    class="testimonial-card flex h-full min-h-[22rem] flex-col rounded-xl border border-graphite-200 bg-white p-5 shadow-sm dark:border-graphite-700 dark:bg-graphite-950 sm:min-h-[24rem] sm:p-6"
                                    :style="slideStyle"
                                >
                                    <div class="mb-4 h-36 w-full shrink-0 overflow-hidden rounded-lg sm:h-40">
                                        <img
                                            :src="item.image"
                                            :alt="item.name"
                                            class="h-full w-full object-cover"
                                            style="object-position: center 30%;"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    </div>

                                    <div class="mb-3 flex gap-1">
                                        <template x-for="star in 5" :key="star">
                                            <svg
                                                class="h-5 w-5"
                                                :class="star <= item.rating ? 'text-amber-400' : 'text-graphite-300 dark:text-graphite-600'"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                                aria-hidden="true"
                                            >
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8-2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </template>
                                    </div>

                                    <p class="mb-4 line-clamp-5 flex-1 text-sm leading-relaxed text-graphite-700 dark:text-graphite-200" x-text="'“' + item.comment + '”'"></p>

                                    <div class="mt-auto border-t border-graphite-100 pt-4 dark:border-graphite-800">
                                        <h3 class="truncate text-base font-semibold text-graphite-900 dark:text-white" x-text="item.name"></h3>
                                        <p class="mt-0.5 truncate text-sm text-brand-700 dark:text-accent-400" x-text="item.service || item.location"></p>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-center gap-4">
                        <button
                            type="button"
                            @click="prev()"
                            class="flex h-11 w-11 items-center justify-center rounded-full border border-graphite-300 text-graphite-700 transition hover:bg-white dark:border-graphite-600 dark:text-graphite-200 dark:hover:bg-graphite-800"
                            aria-label="Previous testimonials"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>

                        <div class="flex items-center gap-2" role="tablist" aria-label="Testimonial slides">
                            <template x-for="page in pageCount" :key="'dot-' + page">
                                <button
                                    type="button"
                                    class="h-2.5 rounded-full transition-all duration-300"
                                    :class="(page - 1) === index ? 'w-7 bg-brand-600' : 'w-2.5 bg-graphite-300 hover:bg-graphite-400 dark:bg-graphite-600 dark:hover:bg-graphite-500'"
                                    :aria-label="'Go to slide ' + page"
                                    :aria-current="(page - 1) === index ? 'true' : null"
                                    @click="goTo(page - 1)"
                                ></button>
                            </template>
                        </div>

                        <button
                            type="button"
                            @click="next()"
                            class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-600 text-white transition hover:bg-brand-700"
                            aria-label="Next testimonials"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    @endif
</x-layouts.public>

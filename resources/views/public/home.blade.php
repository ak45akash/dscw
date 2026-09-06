<x-layouts.public
    :seo-title="$businessName . ' | Premium Steam Car Wash in ' . $city"
    :seo-description="'Book paint-safe steam wash, detailing, ceramic coating, and PPF at Diamond Steam Car Wash. Fast online booking across our service centres.'"
    seo-image="images/exterior-detailing.jpg"
>
    @php
        $formatLocationList = function ($items): string {
            $items = collect($items)->filter(fn ($v) => filled($v))->values();
            $count = $items->count();

            if ($count === 0) {
                return '';
            }

            if ($count === 1) {
                return (string) $items[0];
            }

            if ($count === 2) {
                return $items[0].' and '.$items[1];
            }

            return $items->take($count - 1)->implode(', ').', and '.$items->last();
        };
        $activeLocationCount = $locations->count();
        $activeLocationNames = $formatLocationList($locations->pluck('name'));
        $activeLocationCities = $formatLocationList($locations->pluck('city')->unique());
        $heroLocationLabel = $activeLocationCities !== '' ? $activeLocationCities : $activeLocationNames;
    @endphp

    {{-- Hero --}}
    <div class="relative flex min-h-[62vh] items-center overflow-hidden sm:min-h-[72vh] lg:min-h-[80vh]">
        <x-parallax-bg src="images/exterior-detailing.jpg" alt="Professional steam car wash" :speed="0.8" brightness="0.52" position="center 25%" height="180%" />
        <div class="absolute inset-0 z-0 bg-gradient-to-r from-graphite-950/75 via-brand-950/45 to-transparent"></div>

        <div class="container-custom relative z-10 py-16 text-white sm:py-20 lg:py-24">
            <div class="max-w-2xl animate-on-scroll" x-data="scrollReveal">
                @if($heroLocationLabel !== '')
                    <p class="mb-3 text-sm font-semibold tracking-wide text-accent-400 uppercase">{{ $heroLocationLabel }}</p>
                @endif
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
    <section class="relative flex min-h-[44rem] items-center overflow-hidden py-28 sm:min-h-[52rem] sm:py-32 lg:min-h-[56rem] lg:py-36">
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
                    An eight-step path from booking to handover — the same clear process for every service.
                </p>
            </div>

            {{--
                Snake layout (no arrows):
                Mobile 1-col: 1→2→3→4→5→6→7→8
                sm 2-col:     1 2 / 4 3 / 5 6 / 8 7
                lg 4-col:     1 2 3 4 / 8 7 6 5
            --}}
            <div class="grid grid-cols-1 items-stretch gap-6 sm:grid-cols-2 sm:gap-7 lg:grid-cols-4 lg:gap-8">
                @foreach([
                    [
                        'id' => '01',
                        'order' => 'order-1 sm:order-1 lg:order-1',
                        'title' => 'Book Online',
                        'description' => 'Pick any service, location, and time slot that fits your day.',
                        'path' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    ],
                    [
                        'id' => '02',
                        'order' => 'order-2 sm:order-2 lg:order-2',
                        'title' => 'Get Confirmation',
                        'description' => 'We send booking details and any quick prep notes for your visit.',
                        'path' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                    ],
                    [
                        'id' => '03',
                        'order' => 'order-3 sm:order-4 lg:order-3',
                        'title' => 'Check In',
                        'description' => 'Arrive at your chosen centre and we log your vehicle into the bay.',
                        'path' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z',
                    ],
                    [
                        'id' => '04',
                        'order' => 'order-4 sm:order-3 lg:order-4',
                        'title' => 'Vehicle Review',
                        'description' => 'A short condition check so we apply the right process for your car.',
                        'path' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                    ],
                    [
                        'id' => '05',
                        'order' => 'order-5 sm:order-5 lg:order-8',
                        'title' => 'Bay Setup',
                        'description' => 'Tools, products, and paint protection are prepared for your service.',
                        'path' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                    ],
                    [
                        'id' => '06',
                        'order' => 'order-6 sm:order-6 lg:order-7',
                        'title' => 'Professional Care',
                        'description' => 'Wash, detailing, or protection work — done with paint-safe methods.',
                        'path' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
                    ],
                    [
                        'id' => '07',
                        'order' => 'order-7 sm:order-8 lg:order-6',
                        'title' => 'Quality Check',
                        'description' => 'We inspect the finish so every detail meets our standard before return.',
                        'path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    [
                        'id' => '08',
                        'order' => 'order-8 sm:order-7 lg:order-5',
                        'title' => 'Handover',
                        'description' => 'Keys back, brief walkthrough, and you drive out when you are ready.',
                        'path' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
                    ],
                ] as $step)
                    <article class="{{ $step['order'] }} hover-lift flex h-full min-h-[13.5rem] flex-col rounded-xl border border-graphite-200/90 bg-white/95 p-6 shadow-sm backdrop-blur-sm dark:border-graphite-700 dark:bg-graphite-900/95 sm:min-h-[14.5rem] sm:p-7 lg:p-8">
                        <div class="mb-4 flex items-center gap-3 sm:mb-5">
                            <span class="text-sm font-semibold tracking-wide text-brand-600 dark:text-accent-400">{{ $step['id'] }}</span>
                            <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand-600 text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $step['path'] }}"></path></svg>
                            </span>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-graphite-900 dark:text-white sm:mb-3">{{ $step['title'] }}</h3>
                        <p class="flex-1 text-sm leading-relaxed text-graphite-600 dark:text-graphite-300">{{ $step['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Trusted Local Care — image + stats + CTA --}}
    <section class="relative flex min-h-[34rem] items-center overflow-hidden py-24 text-white sm:min-h-[40rem] sm:py-28 lg:min-h-[44rem] lg:py-36" x-data="statsSection">
        <x-parallax-bg src="images/facility-1.jpg" alt="Diamond Steam Car Wash facility" :speed="-0.95" brightness="1" position="center center" height="190%">
            <div class="absolute inset-0 bg-gradient-to-r from-graphite-950/90 via-brand-950/80 to-brand-900/70"></div>
        </x-parallax-bg>

        <div class="container-custom relative z-10 w-full">
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                <div>
                    <p class="mb-3 text-sm font-semibold uppercase tracking-wide text-accent-400">Trusted Local Care</p>
                    <h2 class="mb-4 text-2xl font-bold sm:text-3xl md:text-4xl">Clean finishes. Consistent standards.</h2>
                    <p class="mb-8 max-w-xl text-sm leading-relaxed text-white/80 sm:text-base">
                        @if($activeLocationCount <= 1)
                            We keep every bay process paint-safe, transparent, and easy to book — so your car looks after every visit.
                        @else
                            From {{ $activeLocationNames }} — we keep every bay process paint-safe, transparent, and easy to book — so your car looks after every visit.
                        @endif
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
                        [
                            'value' => (string) max($activeLocationCount, 1),
                            'label' => $activeLocationCount === 1 ? 'Service location' : 'Service locations',
                            'index' => 3,
                        ],
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
                    ['title' => $activeLocationCount === 1 ? 'Convenient location' : $activeLocationCount.' service locations', 'description' => $activeLocationCount === 0 ? 'Book the centre that suits you.' : ($activeLocationCount === 1 ? $activeLocationNames.' — easy to reach and simple to book.' : $activeLocationNames.' — book the centre that suits you.'), 'path' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
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
        <section class="relative flex min-h-[36rem] items-center overflow-hidden py-24 sm:min-h-[42rem] sm:py-28 lg:min-h-[46rem] lg:py-32">
            <x-parallax-bg
                src="images/facility-2.jpg"
                alt=""
                :speed="-0.85"
                brightness="0.5"
                position="center 45%"
                height="185%"
            >
                <div class="absolute inset-0 bg-graphite-50/90 dark:bg-graphite-900/92"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-brand-600/5 via-transparent to-brand-700/10 dark:from-brand-500/10 dark:to-brand-900/25"></div>
            </x-parallax-bg>

            <div class="container-custom relative z-10 w-full">
                <div class="mx-auto mb-12 max-w-3xl text-center sm:mb-16">
                    <h2 class="mb-3 text-2xl font-bold text-graphite-900 dark:text-white sm:text-3xl md:text-4xl">Our Locations</h2>
                    <div class="mx-auto mb-4 h-1 w-16 bg-brand-600 sm:w-20"></div>
                    <p class="text-sm leading-relaxed text-graphite-600 dark:text-graphite-300 sm:text-base md:text-lg">
                        @if($activeLocationCount === 1)
                            One service centre, one standard of care. Book at {{ $activeLocationNames }} for the same paint-safe process, clear pricing, and reliable turnaround.
                        @else
                            {{ $activeLocationCount }} centres, one standard of care. Book {{ $activeLocationNames }} for the same paint-safe process, clear pricing, and reliable turnaround — whichever is closer to your day.
                        @endif
                        @if($activeLocationCities !== '')
                            <span class="mt-2 block text-graphite-500 dark:text-graphite-400">Serving {{ $activeLocationCities }}.</span>
                        @endif
                    </p>
                </div>

                <div
                    @class([
                        'mx-auto grid items-stretch gap-6 sm:gap-8',
                        'max-w-xl grid-cols-1' => $activeLocationCount === 1,
                        'max-w-5xl grid-cols-1 sm:grid-cols-2' => $activeLocationCount === 2,
                        'max-w-6xl grid-cols-1 sm:grid-cols-2 lg:grid-cols-3' => $activeLocationCount >= 3,
                    ])
                >
                    @foreach($locations as $location)
                        <article class="hover-lift flex h-full min-h-[14rem] flex-col rounded-xl border border-graphite-200/90 bg-white/95 p-7 shadow-sm backdrop-blur-sm dark:border-graphite-700 dark:bg-graphite-950/95 sm:min-h-[15rem] sm:p-8">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-brand-600 dark:text-accent-400">Service centre</p>
                            <h3 class="text-xl font-semibold text-graphite-900 dark:text-white">{{ $location->name }}</h3>
                            @if(filled($location->city) || filled($location->state))
                                <p class="mt-1 text-sm font-medium text-graphite-500 dark:text-graphite-400">
                                    {{ collect([$location->city, $location->state])->filter()->implode(', ') }}
                                </p>
                            @endif
                            <p class="mt-4 flex-1 text-sm leading-relaxed text-graphite-600 dark:text-graphite-300">{{ $location->fullAddress() }}</p>
                            <div class="mt-6 flex flex-col gap-3 border-t border-graphite-100 pt-5 dark:border-graphite-800 sm:flex-row sm:items-center sm:justify-between">
                                @if($location->phone)
                                    <a href="tel:{{ $location->phone }}" class="text-sm font-medium text-brand-700 hover:underline dark:text-accent-400">{{ $location->phone }}</a>
                                @else
                                    <span class="text-sm text-graphite-500 dark:text-graphite-400">Call via Contact</span>
                                @endif
                                <a
                                    href="{{ route('booking.index', ['location' => $location->slug]) }}"
                                    class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700"
                                >
                                    Book here
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mx-auto mt-12 max-w-3xl rounded-xl border border-graphite-200/80 bg-white/80 px-6 py-6 text-center backdrop-blur-sm dark:border-graphite-700 dark:bg-graphite-950/80 sm:mt-14 sm:px-8 sm:py-7">
                    <p class="text-sm leading-relaxed text-graphite-600 dark:text-graphite-300 sm:text-base">
                        @if($activeLocationCount === 1)
                            Ready to book? Pick a slot online — services and add-ons are available at this centre.
                        @else
                            Not sure which centre fits? Start a booking and choose the location at checkout — slots, services, and add-ons stay consistent across all {{ $activeLocationCount }} sites.
                        @endif
                    </p>
                    <div class="mt-5 flex flex-col justify-center gap-3 sm:flex-row sm:gap-4">
                        <a href="{{ route('booking.index') }}" class="btn-cta">Book a Slot</a>
                        <a href="{{ route('contact.index') }}" class="btn-secondary">Ask a Question</a>
                    </div>
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

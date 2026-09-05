<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription">
    <x-page-hero
        badge="Online Booking"
        title="Book Your Car Care Service"
        subtitle="Select your service, choose a convenient time, and let our team handle the rest. Fast, clear, and hassle-free booking."
    />

    <x-breadcrumbs :items="[['label' => 'Home', 'url' => route('home')], ['label' => 'Book Now']]" />

    {{-- How it works --}}
    <section class="border-b border-graphite-200 py-16 dark:border-graphite-800 sm:py-20">
        <div class="container-site">
            <x-section-heading title="How Booking Works" subtitle="Four simple steps to a cleaner, better-protected vehicle." class="mb-12 text-center" align="center" />
            <ol class="grid gap-8 md:grid-cols-4">
                @foreach([
                    ['step' => '1', 'title' => 'Choose Service', 'text' => 'Browse our service menu and select the treatment your vehicle needs — from a quick wash to full ceramic coating.'],
                    ['step' => '2', 'title' => 'Pick Date & Time', 'text' => 'Select an available slot that fits your schedule. Same-day bookings may be available for select services.'],
                    ['step' => '3', 'title' => 'Enter Details', 'text' => 'Provide your contact information, vehicle make/model, and any special instructions for our team.'],
                    ['step' => '4', 'title' => 'Confirm & Pay', 'text' => 'Review your booking summary, apply any coupon codes, and confirm with secure online payment or pay at location.'],
                ] as $item)
                    <li class="text-center">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-brand-600 text-lg font-bold text-white">{{ $item['step'] }}</span>
                        <h3 class="mt-4 font-semibold text-graphite-900 dark:text-white">{{ $item['title'] }}</h3>
                        <p class="mt-2 text-sm text-graphite-600 dark:text-graphite-300">{{ $item['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Service selection --}}
    <section class="py-16 sm:py-20">
        <div class="container-site">
            <div class="grid gap-12 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <h2 class="text-2xl font-bold text-graphite-900 dark:text-white">Select a Service</h2>
                    <p class="mt-3 text-graphite-600 dark:text-graphite-300">Choose from our complete range of car care services. The full booking engine with live availability and payment will be connected in the next phase — for now, select a service and contact us to confirm your appointment.</p>

                    <div class="mt-8 space-y-4" x-data="{ selected: null }">
                        @foreach($services as $service)
                            <label class="card flex cursor-pointer items-start gap-4 p-5 transition-colors" :class="selected === {{ $service->id }} ? 'ring-2 ring-brand-500' : ''">
                                <input type="radio" name="service_id" value="{{ $service->id }}" class="mt-1" x-model="selected" @change="selected = {{ $service->id }}">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="font-semibold text-graphite-900 dark:text-white">{{ $service->name }}</p>
                                            <p class="mt-1 text-sm text-graphite-600 dark:text-graphite-300">{{ $service->short_description }}</p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="font-bold text-brand-700 dark:text-brand-300">{{ $service->formattedPrice() }}</p>
                                            <p class="text-xs text-graphite-500">{{ $service->formattedDuration() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <aside>
                    <x-card class="sticky top-24">
                        <h3 class="text-lg font-semibold">Booking Form</h3>
                        <p class="mt-2 text-sm text-graphite-500">Complete booking with live calendar and payment is coming soon. Contact us to schedule your appointment today.</p>
                        <form class="mt-6 space-y-4">
                            <div>
                                <x-label for="book_name">Full Name</x-label>
                                <x-input name="book_name" id="book_name" placeholder="Your name" />
                            </div>
                            <div>
                                <x-label for="book_phone">Phone</x-label>
                                <x-input name="book_phone" id="book_phone" placeholder="+91" />
                            </div>
                            <div>
                                <x-label for="book_email">Email</x-label>
                                <x-input type="email" name="book_email" id="book_email" />
                            </div>
                            <div>
                                <x-label for="book_vehicle">Vehicle Make & Model</x-label>
                                <x-input name="book_vehicle" id="book_vehicle" placeholder="e.g. Honda City 2022" />
                            </div>
                            <div>
                                <x-label for="book_notes">Notes</x-label>
                                <textarea name="book_notes" id="book_notes" rows="3" class="form-input" placeholder="Any special requirements..."></textarea>
                            </div>
                        </form>
                        <x-button href="{{ route('contact.index') }}" class="mt-6 w-full">Contact to Confirm Booking</x-button>
                        <p class="mt-3 text-center text-xs text-graphite-400">Full online booking engine launching soon</p>
                    </x-card>
                </aside>
            </div>
        </div>
    </section>

    <section class="bg-graphite-50 py-16 dark:bg-graphite-900/50 sm:py-20">
        <div class="container-site">
            <x-section-heading title="Why Book With Us?" class="mb-10" />
            <div class="grid gap-6 md:grid-cols-3">
                @foreach([
                    ['title' => 'Flexible Scheduling', 'text' => 'Morning, afternoon, or evening slots available six days a week. Drop off and collect at your convenience.'],
                    ['title' => 'Secure Payments', 'text' => 'Pay online via Razorpay or choose pay-at-location. All transactions are encrypted and verified server-side.'],
                    ['title' => 'Satisfaction Guaranteed', 'text' => 'We inspect every vehicle before handover. If something isn\'t right, we\'ll make it right.'],
                ] as $item)
                    <div class="card p-6">
                        <h3 class="font-semibold">{{ $item['title'] }}</h3>
                        <p class="mt-2 text-sm text-graphite-600 dark:text-graphite-300">{{ $item['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.public>

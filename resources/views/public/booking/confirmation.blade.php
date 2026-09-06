<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription">
    <section class="bg-graphite-50 py-16 dark:bg-graphite-950 sm:py-20">
        <div class="container-custom max-w-2xl text-center">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h1 class="text-3xl font-bold text-graphite-900 dark:text-white md:text-4xl">Booking received</h1>
            <p class="mt-3 text-graphite-600 dark:text-graphite-300">Thank you, {{ $booking->customer_name }}. Save your reference for check-in.</p>
            <p class="mt-6 inline-block rounded-full bg-brand-600 px-5 py-2.5 font-mono text-sm font-semibold text-white">{{ $booking->reference }}</p>
        </div>

        <div class="container-custom mt-12 max-w-2xl">
            <div class="rounded-2xl border border-graphite-200 bg-white p-6 shadow-lg dark:border-graphite-700 dark:bg-graphite-900 md:p-8">
                <dl class="grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-graphite-500 dark:text-graphite-400">Status</dt>
                        <dd class="font-semibold capitalize text-graphite-900 dark:text-white">{{ $booking->statusLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-graphite-500 dark:text-graphite-400">Payment</dt>
                        <dd class="font-semibold text-graphite-900 dark:text-white">{{ $booking->payment_method === 'online' ? 'Online' : 'At location' }} ({{ $booking->payment_status }})</dd>
                    </div>
                    <div>
                        <dt class="text-graphite-500 dark:text-graphite-400">Service</dt>
                        <dd class="font-semibold text-graphite-900 dark:text-white">{{ $booking->service?->name }}</dd>
                    </div>
                    @if($booking->addons->isNotEmpty())
                        <div class="sm:col-span-2">
                            <dt class="text-graphite-500 dark:text-graphite-400">Add-ons</dt>
                            <dd class="font-semibold text-graphite-900 dark:text-white">{{ $booking->addons->map(fn ($a) => $a->name.' (₹'.number_format((float) $a->unit_price, 0).')')->join(', ') }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-graphite-500 dark:text-graphite-400">Amount</dt>
                        <dd class="font-semibold text-brand-700 dark:text-accent-400">{{ $booking->formattedPrice() }}</dd>
                    </div>
                    <div>
                        <dt class="text-graphite-500 dark:text-graphite-400">Location</dt>
                        <dd class="font-semibold text-graphite-900 dark:text-white">{{ $booking->location?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-graphite-500 dark:text-graphite-400">Address</dt>
                        <dd class="text-graphite-700 dark:text-graphite-300">{{ $booking->location?->fullAddress() }}</dd>
                    </div>
                    <div>
                        <dt class="text-graphite-500 dark:text-graphite-400">Date</dt>
                        <dd class="font-semibold text-graphite-900 dark:text-white">{{ $booking->booking_date->format('l, d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-graphite-500 dark:text-graphite-400">Time</dt>
                        <dd class="font-semibold text-graphite-900 dark:text-white">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-graphite-500 dark:text-graphite-400">Vehicle</dt>
                        <dd class="font-semibold text-graphite-900 dark:text-white">{{ $booking->vehicle_make_model }}</dd>
                    </div>
                </dl>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('home') }}" class="btn-cta">Back to home</a>
                    <a href="{{ route('booking.index') }}" class="rounded-lg border border-graphite-300 px-6 py-3.5 font-medium text-graphite-700 transition hover:bg-graphite-50 dark:border-graphite-600 dark:text-graphite-200 dark:hover:bg-graphite-800">Book another</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>

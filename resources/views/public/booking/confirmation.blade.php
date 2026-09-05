<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription">
    <section class="bg-gradient-to-b from-blue-50 to-white py-20">
        <div class="container-custom max-w-2xl text-center">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 md:text-4xl">Booking received</h1>
            <p class="mt-3 text-gray-600">Thank you, {{ $booking->customer_name }}. Save your reference for check-in.</p>
            <p class="mt-6 inline-block rounded-full bg-blue-600 px-5 py-2 font-mono text-sm font-semibold text-white">{{ $booking->reference }}</p>
        </div>

        <div class="container-custom mt-12 max-w-2xl">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-lg md:p-8">
                <dl class="grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-gray-500">Status</dt><dd class="font-semibold capitalize">{{ $booking->statusLabel() }}</dd></div>
                    <div><dt class="text-gray-500">Payment</dt><dd class="font-semibold">{{ $booking->payment_method === 'online' ? 'Online' : 'At location' }} ({{ $booking->payment_status }})</dd></div>
                    <div><dt class="text-gray-500">Service</dt><dd class="font-semibold">{{ $booking->service?->name }}</dd></div>
                    <div><dt class="text-gray-500">Amount</dt><dd class="font-semibold text-blue-600">{{ $booking->formattedPrice() }}</dd></div>
                    <div><dt class="text-gray-500">Location</dt><dd class="font-semibold">{{ $booking->location?->name }}</dd></div>
                    <div><dt class="text-gray-500">Address</dt><dd>{{ $booking->location?->fullAddress() }}</dd></div>
                    <div><dt class="text-gray-500">Date</dt><dd class="font-semibold">{{ $booking->booking_date->format('l, d M Y') }}</dd></div>
                    <div><dt class="text-gray-500">Time</dt><dd class="font-semibold">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-gray-500">Vehicle</dt><dd class="font-semibold">{{ $booking->vehicle_make_model }}</dd></div>
                </dl>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('home') }}" class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">Back to home</a>
                    <a href="{{ route('booking.index') }}" class="rounded-lg border border-gray-300 px-6 py-3 font-medium text-gray-700">Book another</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>

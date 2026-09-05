<x-layouts.admin title="Booking {{ $booking->reference }}" breadcrumb="Bookings / Detail">
    <div class="mb-6">
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-blue-600 hover:underline">← All bookings</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2 space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-xl font-bold">{{ $booking->reference }}</h2>
                <x-badge color="blue">{{ $booking->statusLabel() }}</x-badge>
                <x-badge>{{ str_replace('_', ' ', $booking->payment_status) }}</x-badge>
            </div>
            <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                <div><dt class="text-graphite-500">Customer</dt><dd class="font-medium">{{ $booking->customer_name }}</dd></div>
                <div><dt class="text-graphite-500">Phone</dt><dd>{{ $booking->customer_phone }}</dd></div>
                <div><dt class="text-graphite-500">Email</dt><dd>{{ $booking->customer_email }}</dd></div>
                <div><dt class="text-graphite-500">Vehicle</dt><dd>{{ $booking->vehicle_make_model }} @if($booking->vehicle_plate)({{ $booking->vehicle_plate }})@endif</dd></div>
                <div><dt class="text-graphite-500">Service</dt><dd>{{ $booking->service?->name }} — {{ $booking->formattedPrice() }}</dd></div>
                @if((float) $booking->discount_amount > 0)
                    <div><dt class="text-graphite-500">Discount</dt><dd>−₹{{ number_format((float) $booking->discount_amount, 0) }} @if($booking->coupon_code)({{ $booking->coupon_code }})@endif</dd></div>
                @endif
                <div><dt class="text-graphite-500">Location</dt><dd>{{ $booking->location?->name }}</dd></div>
                <div><dt class="text-graphite-500">Date</dt><dd>{{ $booking->booking_date->format('l, d M Y') }}</dd></div>
                <div><dt class="text-graphite-500">Time</dt><dd>{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</dd></div>
                <div><dt class="text-graphite-500">Payment</dt><dd>{{ $booking->payment_method === 'online' ? 'Online (Razorpay)' : 'Pay at location' }}</dd></div>
                @if($booking->notes)
                    <div class="sm:col-span-2"><dt class="text-graphite-500">Notes</dt><dd>{{ $booking->notes }}</dd></div>
                @endif
            </dl>
        </x-card>

        <x-card>
            <h3 class="mb-4 font-semibold">Update status</h3>
            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <select name="status" class="form-input">
                    @foreach(['pending','confirmed','completed','cancelled','no_show'] as $status)
                        <option value="{{ $status }}" @selected($booking->status === $status)>{{ ucfirst(str_replace('_',' ', $status)) }}</option>
                    @endforeach
                </select>
                <x-button type="submit" class="w-full">Save status</x-button>
            </form>
        </x-card>
    </div>
</x-layouts.admin>

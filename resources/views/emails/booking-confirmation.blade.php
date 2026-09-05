<x-mail::message>
# Booking confirmed

Hi {{ $booking->customer_name }},

Your booking **{{ $booking->reference }}** has been received.

- **Service:** {{ $booking->service?->name }}
- **Location:** {{ $booking->location?->name }}
- **Date:** {{ $booking->booking_date->format('D, M j, Y') }}
- **Time:** {{ substr((string) $booking->start_time, 0, 5) }}
- **Total:** {{ $booking->formattedPrice() }}
- **Status:** {{ $booking->statusLabel() }}

<x-mail::button :url="route('booking.confirmation', $booking->reference)">
View confirmation
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

<x-mail::message>
# New booking

A new booking was placed on the website.

- **Reference:** {{ $booking->reference }}
- **Customer:** {{ $booking->customer_name }} ({{ $booking->customer_email }}, {{ $booking->customer_phone }})
- **Service:** {{ $booking->service?->name }}
- **Location:** {{ $booking->location?->name }}
- **When:** {{ $booking->booking_date->format('Y-m-d') }} {{ substr((string) $booking->start_time, 0, 5) }}
- **Payment:** {{ $booking->payment_method }} / {{ $booking->payment_status }}

<x-mail::button :url="route('admin.bookings.show', $booking)">
Open in admin
</x-mail::button>
</x-mail::message>

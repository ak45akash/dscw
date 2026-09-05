<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Location;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class BookingService
{
    public function __construct(
        private SettingsService $settings,
        private AvailabilityService $availability,
        private RazorpayService $razorpay,
        private AuditLogService $auditLog,
    ) {}

    /**
     * @param  array{
     *     location_id: int,
     *     service_id: int,
     *     customer_name: string,
     *     customer_email: string,
     *     customer_phone: string,
     *     vehicle_make_model: string,
     *     vehicle_plate?: ?string,
     *     notes?: ?string,
     *     booking_date: string,
     *     start_time: string,
     *     payment_method: string,
     * }  $data
     * @return array{booking: Booking, razorpay_order?: array{id: string, amount: int, currency: string, key: string}}
     */
    public function create(array $data): array
    {
        if (! $this->settings->get('booking', 'online_bookings_enabled', true)) {
            throw new RuntimeException('Online bookings are currently disabled.');
        }

        $location = Location::query()->active()->findOrFail($data['location_id']);
        $service = Service::query()->active()->findOrFail($data['service_id']);
        $date = Carbon::parse($data['booking_date'])->startOfDay();
        $startTime = Carbon::parse($data['booking_date'].' '.$data['start_time']);

        $slots = $this->availability->slotsFor($location, $service, $date);
        $match = collect($slots)->firstWhere('start', $startTime->format('H:i'));

        if (! $match) {
            throw new InvalidArgumentException('The selected time slot is no longer available.');
        }

        $paymentMethod = $data['payment_method'];

        if ($paymentMethod === Booking::PAYMENT_ONLINE && ! $this->razorpay->isEnabled()) {
            throw new InvalidArgumentException('Online payment is not available. Please choose pay at location.');
        }

        $requireApproval = (bool) $this->settings->get('booking', 'require_admin_approval', false);
        $autoConfirm = (bool) $this->settings->get('booking', 'auto_confirm_bookings', true);

        $status = Booking::STATUS_PENDING;
        $paymentStatus = Booking::PAYMENT_UNPAID;

        if ($paymentMethod === Booking::PAYMENT_AT_LOCATION) {
            if (! $requireApproval && $autoConfirm) {
                $status = Booking::STATUS_CONFIRMED;
            }
        } else {
            $paymentStatus = Booking::PAYMENT_PENDING;
        }

        $endTime = Carbon::parse($data['booking_date'].' '.$match['end']);

        $booking = DB::transaction(function () use ($data, $location, $service, $date, $startTime, $endTime, $status, $paymentMethod, $paymentStatus) {
            return Booking::query()->create([
                'reference' => $this->generateReference(),
                'location_id' => $location->id,
                'service_id' => $service->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'vehicle_make_model' => $data['vehicle_make_model'],
                'vehicle_plate' => $data['vehicle_plate'] ?? null,
                'notes' => $data['notes'] ?? null,
                'booking_date' => $date->toDateString(),
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'duration_minutes' => $service->duration_minutes,
                'price' => $service->price,
                'status' => $status,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'confirmed_at' => $status === Booking::STATUS_CONFIRMED ? now() : null,
            ]);
        });

        $result = ['booking' => $booking->load(['location', 'service'])];

        if ($paymentMethod === Booking::PAYMENT_ONLINE) {
            $order = $this->razorpay->createOrder($booking);
            $booking->update(['razorpay_order_id' => $order['id']]);
            $result['razorpay_order'] = [
                'id' => $order['id'],
                'amount' => $order['amount'],
                'currency' => $order['currency'],
                'key' => $this->razorpay->keyId(),
            ];
        }

        return $result;
    }

    public function verifyOnlinePayment(Booking $booking, string $paymentId, string $signature): Booking
    {
        if (! $booking->razorpay_order_id) {
            throw new InvalidArgumentException('This booking has no payment order.');
        }

        if (! $this->razorpay->verifyPaymentSignature($booking->razorpay_order_id, $paymentId, $signature)) {
            $booking->update(['payment_status' => Booking::PAYMENT_FAILED]);
            throw new InvalidArgumentException('Payment verification failed.');
        }

        $requireApproval = (bool) $this->settings->get('booking', 'require_admin_approval', false);
        $status = $requireApproval ? Booking::STATUS_PENDING : Booking::STATUS_CONFIRMED;

        $booking->update([
            'razorpay_payment_id' => $paymentId,
            'payment_status' => Booking::PAYMENT_PAID,
            'status' => $status,
            'confirmed_at' => $status === Booking::STATUS_CONFIRMED ? now() : null,
        ]);

        return $booking->fresh(['location', 'service']);
    }

    public function updateStatus(Booking $booking, string $status): Booking
    {
        $allowed = [
            Booking::STATUS_PENDING,
            Booking::STATUS_CONFIRMED,
            Booking::STATUS_COMPLETED,
            Booking::STATUS_CANCELLED,
            Booking::STATUS_NO_SHOW,
        ];

        if (! in_array($status, $allowed, true)) {
            throw new InvalidArgumentException('Invalid booking status.');
        }

        $old = $booking->status;

        $booking->update([
            'status' => $status,
            'confirmed_at' => $status === Booking::STATUS_CONFIRMED ? ($booking->confirmed_at ?? now()) : $booking->confirmed_at,
            'cancelled_at' => $status === Booking::STATUS_CANCELLED ? now() : null,
        ]);

        $this->auditLog->log(
            module: 'bookings',
            action: 'status_updated',
            oldValues: ['status' => $old],
            newValues: ['status' => $status, 'reference' => $booking->reference],
        );

        return $booking->fresh(['location', 'service']);
    }

    private function generateReference(): string
    {
        do {
            $reference = 'DSCW-'.strtoupper(Str::random(8));
        } while (Booking::query()->where('reference', $reference)->exists());

        return $reference;
    }
}

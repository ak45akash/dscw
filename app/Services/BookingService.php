<?php

namespace App\Services;

use App\Mail\BookingConfirmationMail;
use App\Mail\NewBookingAdminMail;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Service;
use App\Models\ServiceAddon;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class BookingService
{
    public function __construct(
        private SettingsService $settings,
        private AvailabilityService $availability,
        private RazorpayService $razorpay,
        private AuditLogService $auditLog,
        private CouponService $coupons,
        private SmsService $sms,
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
     *     coupon_code?: ?string,
     *     addon_ids?: list<int>|null,
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
        $addons = $this->resolveAddons($service, $data['addon_ids'] ?? []);
        $date = Carbon::parse($data['booking_date'])->startOfDay();
        $startTime = Carbon::parse($data['booking_date'].' '.$data['start_time']);
        $extraDuration = (int) $addons->sum('duration_minutes');

        $slots = $this->availability->slotsFor($location, $service, $date, $extraDuration);
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
        $basePrice = (float) $service->price + (float) $addons->sum('price');
        $discount = 0.0;
        $couponId = null;
        $couponCode = null;

        if (! empty($data['coupon_code'])) {
            $applied = $this->coupons->apply((string) $data['coupon_code'], $basePrice);
            $discount = $applied['discount'];
            $couponId = $applied['coupon']->id;
            $couponCode = $applied['coupon']->code;
        }

        $finalPrice = max(0, round($basePrice - $discount, 2));
        $totalDuration = (int) $service->duration_minutes + $extraDuration;

        $booking = DB::transaction(function () use ($data, $location, $service, $addons, $date, $startTime, $endTime, $status, $paymentMethod, $paymentStatus, $finalPrice, $discount, $couponId, $couponCode, $totalDuration) {
            $booking = Booking::query()->create([
                'reference' => $this->generateReference(),
                'location_id' => $location->id,
                'service_id' => $service->id,
                'coupon_id' => $couponId,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'vehicle_make_model' => $data['vehicle_make_model'],
                'vehicle_plate' => $data['vehicle_plate'] ?? null,
                'notes' => $data['notes'] ?? null,
                'booking_date' => $date->toDateString(),
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'duration_minutes' => $totalDuration,
                'price' => $finalPrice,
                'discount_amount' => $discount,
                'coupon_code' => $couponCode,
                'status' => $status,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'confirmed_at' => $status === Booking::STATUS_CONFIRMED ? now() : null,
            ]);

            foreach ($addons as $addon) {
                $booking->addons()->create([
                    'service_addon_id' => $addon->id,
                    'name' => $addon->name,
                    'unit_price' => $addon->price,
                    'duration_minutes' => $addon->duration_minutes,
                    'quantity' => 1,
                ]);
            }

            if ($couponId) {
                \App\Models\Coupon::query()->whereKey($couponId)->increment('used_count');
            }

            return $booking;
        });

        $result = ['booking' => $booking->load(['location', 'service', 'coupon', 'addons'])];

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

        $this->sendBookingEmails($result['booking']);

        if ($status === Booking::STATUS_CONFIRMED) {
            $this->sms->sendConfirmation($result['booking']);
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

        $booking = $booking->fresh(['location', 'service', 'coupon', 'addons']);

        if ($status === Booking::STATUS_CONFIRMED) {
            $this->sms->sendConfirmation($booking);
        }

        return $booking;
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

        $booking = $booking->fresh(['location', 'service', 'coupon', 'addons']);

        if ($status === Booking::STATUS_CONFIRMED && $old !== Booking::STATUS_CONFIRMED && ! $booking->sms_confirmation_sent_at) {
            $this->sms->sendConfirmation($booking);
        }

        return $booking;
    }

    /**
     * @param  list<int|string>|null  $addonIds
     * @return Collection<int, ServiceAddon>
     */
    public function resolveAddons(Service $service, ?array $addonIds): Collection
    {
        $ids = collect($addonIds ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $addons = $service->addons()
            ->where('service_addons.is_active', true)
            ->whereIn('service_addons.id', $ids)
            ->get();

        if ($addons->count() !== $ids->count()) {
            throw new InvalidArgumentException('One or more selected add-ons are not available for this service.');
        }

        return $addons;
    }

    private function sendBookingEmails(Booking $booking): void
    {
        try {
            Mail::to($booking->customer_email)->send(new BookingConfirmationMail($booking));
        } catch (Throwable) {
            // Mail failures must not block booking creation (shared hosting / misconfigured SMTP).
        }

        $adminEmail = $this->settings->get('business', 'email');
        if (filled($adminEmail)) {
            try {
                Mail::to($adminEmail)->send(new NewBookingAdminMail($booking));
            } catch (Throwable) {
                // Same as above.
            }
        }
    }

    private function generateReference(): string
    {
        do {
            $reference = 'DSCW-'.strtoupper(Str::random(8));
        } while (Booking::query()->where('reference', $reference)->exists());

        return $reference;
    }
}

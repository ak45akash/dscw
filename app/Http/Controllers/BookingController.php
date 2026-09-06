<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Location;
use App\Models\Service;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use App\Services\CouponService;
use App\Services\RazorpayService;
use App\Services\SettingsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;
use RuntimeException;

class BookingController extends Controller
{
    public function __construct(
        private SettingsService $settings,
        private AvailabilityService $availability,
        private BookingService $bookingService,
        private RazorpayService $razorpay,
        private CouponService $coupons,
    ) {}

    public function index(Request $request): View
    {
        $services = Service::query()->active()->with(['category', 'addons' => fn ($q) => $q->active()])->orderBy('display_order')->get();
        $locations = Location::query()->active()->with('workingHours')->get();
        $preselectedService = $services->firstWhere('slug', $request->string('service')->toString());
        $preselectedLocation = $locations->firstWhere('slug', $request->string('location')->toString());

        return view('public.booking.index', [
            'services' => $services,
            'locations' => $locations,
            'preselectedServiceId' => $preselectedService?->id,
            'preselectedLocationId' => $preselectedLocation?->id,
            'bookingsEnabled' => (bool) $this->settings->get('booking', 'online_bookings_enabled', true),
            'razorpayEnabled' => $this->razorpay->isEnabled(),
            'maxAdvanceDays' => (int) $this->settings->get('booking', 'max_advance_days', 30),
            'sameDayBookings' => (bool) $this->settings->get('booking', 'same_day_bookings', true),
            'seoTitle' => 'Book Steam Car Wash Online | Sector 66 & Matour',
            'seoDescription' => 'Book steam wash, detailing, ceramic coating, or PPF online at Diamond Steam Car Wash. Choose Sector 66 or Matour, pick a slot, and confirm in minutes.',
        ]);
    }

    public function slots(Request $request): JsonResponse
    {
        $data = $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'service_id' => ['required', 'exists:services,id'],
            'date' => ['required', 'date'],
            'addon_ids' => ['nullable', 'array'],
            'addon_ids.*' => ['integer', 'exists:service_addons,id'],
        ]);

        $location = Location::query()->active()->findOrFail($data['location_id']);
        $service = Service::query()->active()->findOrFail($data['service_id']);
        $date = Carbon::parse($data['date'])->startOfDay();
        $addons = $this->bookingService->resolveAddons($service, $data['addon_ids'] ?? []);
        $extra = (int) $addons->sum('duration_minutes');

        return response()->json([
            'slots' => $this->availability->slotsFor($location, $service, $date, $extra),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'service_id' => ['required', 'exists:services,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'vehicle_make_model' => ['required', 'string', 'max:255'],
            'vehicle_plate' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'booking_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'payment_method' => ['required', 'in:online,at_location'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'addon_ids' => ['nullable', 'array'],
            'addon_ids.*' => ['integer', 'exists:service_addons,id'],
        ]);

        try {
            $result = $this->bookingService->create($data);
        } catch (InvalidArgumentException|RuntimeException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withInput()->withErrors(['booking' => $e->getMessage()]);
        }

        $booking = $result['booking'];

        if ($request->expectsJson() || isset($result['razorpay_order'])) {
            return response()->json([
                'reference' => $booking->reference,
                'confirmation_url' => route('booking.confirmation', $booking->reference),
                'razorpay' => $result['razorpay_order'] ?? null,
                'booking' => [
                    'customer_name' => $booking->customer_name,
                    'customer_email' => $booking->customer_email,
                    'customer_phone' => $booking->customer_phone,
                    'amount' => (float) $booking->price,
                    'service' => $booking->service?->name,
                ],
            ]);
        }

        return redirect()
            ->route('booking.confirmation', $booking->reference)
            ->with('success', 'Your booking has been received.');
    }

    public function verifyPayment(Request $request): JsonResponse
    {
        $data = $request->validate([
            'reference' => ['required', 'string'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $booking = Booking::query()->where('reference', $data['reference'])->firstOrFail();

        if ($booking->razorpay_order_id !== $data['razorpay_order_id']) {
            return response()->json(['message' => 'Order mismatch.'], 422);
        }

        try {
            $this->bookingService->verifyOnlinePayment(
                $booking,
                $data['razorpay_payment_id'],
                $data['razorpay_signature'],
            );
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'confirmation_url' => route('booking.confirmation', $booking->reference),
        ]);
    }

    public function validateCoupon(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'service_id' => ['required', 'exists:services,id'],
            'addon_ids' => ['nullable', 'array'],
            'addon_ids.*' => ['integer', 'exists:service_addons,id'],
        ]);

        $service = Service::query()->active()->findOrFail($data['service_id']);
        $addons = $this->bookingService->resolveAddons($service, $data['addon_ids'] ?? []);
        $subtotal = (float) $service->price + (float) $addons->sum('price');

        try {
            $applied = $this->coupons->apply($data['code'], $subtotal);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'code' => $applied['coupon']->code,
            'discount' => $applied['discount'],
            'total' => $applied['total'],
            'subtotal' => $subtotal,
        ]);
    }

    public function confirmation(string $reference): View
    {
        $booking = Booking::query()
            ->with(['location', 'service', 'addons'])
            ->where('reference', $reference)
            ->firstOrFail();

        return view('public.booking.confirmation', [
            'booking' => $booking,
            'seoTitle' => 'Booking Confirmed | Diamond Steam Car Wash',
            'seoDescription' => 'Your booking reference and appointment details.',
        ]);
    }
}

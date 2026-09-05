<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingSettingsController extends Controller
{
    public function __construct(
        private SettingsService $settings,
        private AuditLogService $auditLog,
    ) {}

    public function edit(): View
    {
        return view('admin.settings.booking', [
            'booking' => $this->settings->getGroup('booking'),
            'payment' => $this->settings->getGroup('payment'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'online_bookings_enabled' => ['sometimes', 'boolean'],
            'same_day_bookings' => ['sometimes', 'boolean'],
            'max_advance_days' => ['required', 'integer', 'min:1', 'max:365'],
            'slot_interval_minutes' => ['required', 'integer', 'min:15', 'max:240'],
            'default_duration_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'buffer_minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'max_bookings_per_day' => ['required', 'integer', 'min:1', 'max:500'],
            'max_bookings_per_slot' => ['required', 'integer', 'min:1', 'max:50'],
            'require_admin_approval' => ['sometimes', 'boolean'],
            'auto_confirm_bookings' => ['sometimes', 'boolean'],
            'razorpay_enabled' => ['sometimes', 'boolean'],
            'razorpay_key_id' => ['nullable', 'string', 'max:255'],
        ]);

        $oldBooking = $this->settings->getGroup('booking');
        $oldPayment = $this->settings->getGroup('payment');

        $this->settings->setMany('booking', [
            'online_bookings_enabled' => ['value' => $request->boolean('online_bookings_enabled'), 'type' => 'boolean'],
            'same_day_bookings' => ['value' => $request->boolean('same_day_bookings'), 'type' => 'boolean'],
            'max_advance_days' => ['value' => $validated['max_advance_days'], 'type' => 'integer'],
            'slot_interval_minutes' => ['value' => $validated['slot_interval_minutes'], 'type' => 'integer'],
            'default_duration_minutes' => ['value' => $validated['default_duration_minutes'], 'type' => 'integer'],
            'buffer_minutes' => ['value' => $validated['buffer_minutes'], 'type' => 'integer'],
            'max_bookings_per_day' => ['value' => $validated['max_bookings_per_day'], 'type' => 'integer'],
            'max_bookings_per_slot' => ['value' => $validated['max_bookings_per_slot'], 'type' => 'integer'],
            'require_admin_approval' => ['value' => $request->boolean('require_admin_approval'), 'type' => 'boolean'],
            'auto_confirm_bookings' => ['value' => $request->boolean('auto_confirm_bookings'), 'type' => 'boolean'],
        ]);

        $this->settings->setMany('payment', [
            'razorpay_enabled' => ['value' => $request->boolean('razorpay_enabled'), 'type' => 'boolean'],
            'razorpay_key_id' => ['value' => $validated['razorpay_key_id'] ?? '', 'type' => 'string', 'is_public' => true],
        ], ['razorpay_key_id']);

        $this->auditLog->log(
            module: 'settings',
            action: 'booking_rules_updated',
            oldValues: array_merge($oldBooking, $oldPayment),
            newValues: array_merge($this->settings->getGroup('booking'), $this->settings->getGroup('payment')),
        );

        return redirect()->route('admin.settings.booking')->with('success', 'Booking rules updated.');
    }
}

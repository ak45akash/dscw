<x-layouts.admin title="Booking Rules" breadcrumb="Bookings / Rules">
    <form method="POST" action="{{ route('admin.settings.booking.update') }}" class="max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        <x-card>
            <h3 class="mb-4 text-lg font-semibold">Availability</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="online_bookings_enabled" value="1" @checked(old('online_bookings_enabled', $booking['online_bookings_enabled'] ?? true))>
                        Online bookings enabled
                    </label>
                    <x-form-hint>When off, the public Book Now flow stops accepting new appointments.</x-form-hint>
                </div>
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="same_day_bookings" value="1" @checked(old('same_day_bookings', $booking['same_day_bookings'] ?? true))>
                        Allow same-day bookings
                    </label>
                    <x-form-hint>When off, customers can only book from tomorrow onward.</x-form-hint>
                </div>
                <div>
                    <x-label for="max_advance_days" required>Max advance days</x-label>
                    <x-input type="number" name="max_advance_days" id="max_advance_days" :value="old('max_advance_days', $booking['max_advance_days'] ?? 30)" required />
                    <x-form-hint>How far ahead customers may book (e.g. 30 = up to a month from today).</x-form-hint>
                </div>
                <div>
                    <x-label for="slot_interval_minutes" required>Slot interval (minutes)</x-label>
                    <x-input type="number" name="slot_interval_minutes" id="slot_interval_minutes" :value="old('slot_interval_minutes', $booking['slot_interval_minutes'] ?? 60)" required />
                    <x-form-hint>Spacing between offered start times (e.g. 60 = 9:00, 10:00, 11:00).</x-form-hint>
                </div>
                <div>
                    <x-label for="buffer_minutes" required>Buffer between bookings</x-label>
                    <x-input type="number" name="buffer_minutes" id="buffer_minutes" :value="old('buffer_minutes', $booking['buffer_minutes'] ?? 15)" required />
                    <x-form-hint>Extra minutes kept free after each booking for turnover/cleanup.</x-form-hint>
                </div>
                <div>
                    <x-label for="default_duration_minutes" required>Default duration</x-label>
                    <x-input type="number" name="default_duration_minutes" id="default_duration_minutes" :value="old('default_duration_minutes', $booking['default_duration_minutes'] ?? 60)" required />
                    <x-form-hint>Fallback only for new services. Each service’s own duration is what the booking engine uses for live slots.</x-form-hint>
                </div>
                <div>
                    <x-label for="max_bookings_per_day" required>Max bookings / day / location</x-label>
                    <x-input type="number" name="max_bookings_per_day" id="max_bookings_per_day" :value="old('max_bookings_per_day', $booking['max_bookings_per_day'] ?? 20)" required />
                    <x-form-hint>Hard cap on total appointments per location per calendar day.</x-form-hint>
                </div>
                <div>
                    <x-label for="max_bookings_per_slot" required>Max bookings / slot</x-label>
                    <x-input type="number" name="max_bookings_per_slot" id="max_bookings_per_slot" :value="old('max_bookings_per_slot', $booking['max_bookings_per_slot'] ?? 2)" required />
                    <x-form-hint>How many overlapping bookings can share the same start time (parallel capacity).</x-form-hint>
                </div>
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="require_admin_approval" value="1" @checked(old('require_admin_approval', $booking['require_admin_approval'] ?? false))>
                        Require admin approval
                    </label>
                    <x-form-hint>New bookings stay pending until an admin confirms them.</x-form-hint>
                </div>
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="auto_confirm_bookings" value="1" @checked(old('auto_confirm_bookings', $booking['auto_confirm_bookings'] ?? true))>
                        Auto-confirm when approval not required
                    </label>
                    <x-form-hint>If approval is off, bookings are confirmed immediately when created.</x-form-hint>
                </div>
            </div>
        </x-card>

        <x-card>
            <h3 class="mb-4 text-lg font-semibold">Razorpay (online payment)</h3>
            <p class="mb-4 text-sm text-graphite-500">Secret key stays in <code class="text-xs">.env</code> as <code class="text-xs">RAZORPAY_KEY_SECRET</code>. Without both keys, customers only see pay at location.</p>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="razorpay_enabled" value="1" @checked(old('razorpay_enabled', $payment['razorpay_enabled'] ?? false))>
                        Enable Razorpay online payments
                    </label>
                    <x-form-hint>Shows “Pay online” on Book Now when a Key ID and secret are also configured.</x-form-hint>
                </div>
                <div class="sm:col-span-2">
                    <x-label for="razorpay_key_id">Razorpay Key ID (public)</x-label>
                    <x-input name="razorpay_key_id" id="razorpay_key_id" :value="old('razorpay_key_id', $payment['razorpay_key_id'] ?? '')" placeholder="rzp_test_..." />
                    <x-form-hint>Public key from the Razorpay dashboard (safe to store here). The secret stays in .env.</x-form-hint>
                </div>
            </div>
        </x-card>

        <div class="flex justify-end">
            <x-button type="submit">Save Rules</x-button>
        </div>
    </form>
</x-layouts.admin>

<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription" seo-image="images/hero.jpg">
    <div class="relative flex min-h-[36vh] items-center overflow-hidden sm:min-h-[42vh] lg:min-h-[46vh]">
        <x-parallax-bg src="images/hero.jpg" alt="Book car wash" :speed="0.8" brightness="0.5" position="center 30%" height="180%">
            <div class="absolute inset-0 bg-gradient-to-r from-graphite-950/70 via-brand-950/45 to-transparent"></div>
        </x-parallax-bg>
        <div class="container-custom relative z-10 py-12 text-white sm:py-14 lg:py-16">
            <h1 class="text-3xl font-bold sm:text-4xl md:text-5xl">Book Your Service</h1>
            <p class="mt-3 max-w-2xl text-base text-white/90 sm:text-lg">Choose a location, pick a slot, and confirm — pay online or at the centre.</p>
        </div>
    </div>

    <section class="bg-graphite-50 py-10 dark:bg-graphite-950 sm:py-14 lg:py-16">
        <div class="container-custom">
            @if(! $bookingsEnabled)
                <div class="mx-auto max-w-2xl rounded-xl border border-amber-200 bg-amber-50 p-6 text-center dark:border-amber-800 dark:bg-amber-950/40 sm:p-8">
                    <h2 class="text-xl font-bold text-graphite-800 dark:text-white sm:text-2xl">Online booking is temporarily closed</h2>
                    <p class="mt-3 text-graphite-600 dark:text-graphite-300">Please <a href="{{ route('contact.index') }}" class="font-medium text-brand-700 hover:underline dark:text-accent-400">contact us</a> to schedule your appointment.</p>
                </div>
            @else
                <div
                    class="mx-auto max-w-4xl"
                    x-data="bookingWizard(@js([
                        'locations' => $locations->map(fn ($l) => ['id' => $l->id, 'name' => $l->name, 'city' => $l->city, 'address' => $l->fullAddress()])->values(),
                        'services' => $services->map(fn ($s) => [
                            'id' => $s->id,
                            'name' => $s->name,
                            'price' => (float) $s->price,
                            'duration' => $s->formattedDuration(),
                            'short' => $s->short_description,
                            'category' => $s->category?->name,
                            'addons' => $s->addons->map(fn ($a) => [
                                'id' => $a->id,
                                'name' => $a->name,
                                'price' => (float) $a->price,
                                'duration' => $a->formattedDuration(),
                                'description' => $a->description,
                            ])->values(),
                        ])->values(),
                        'preselectedServiceId' => $preselectedServiceId,
                        'razorpayEnabled' => $razorpayEnabled,
                        'maxAdvanceDays' => $maxAdvanceDays,
                        'sameDayBookings' => $sameDayBookings,
                        'slotsUrl' => route('booking.slots'),
                        'storeUrl' => route('booking.store'),
                        'verifyUrl' => route('booking.payment.verify'),
                        'couponUrl' => route('booking.coupon'),
                        'csrf' => csrf_token(),
                    ]))"
                >
                    {{-- Progress --}}
                    <div class="mb-10 flex flex-wrap justify-center gap-2 text-sm">
                        @foreach(['Location', 'Service', 'Schedule', 'Details', 'Payment'] as $i => $label)
                            <div
                                class="flex items-center gap-2 rounded-full px-3 py-1.5"
                                :class="step === {{ $i + 1 }}
                                    ? 'bg-brand-600 text-white'
                                    : (step > {{ $i + 1 }}
                                        ? 'bg-brand-100 text-brand-800 dark:bg-brand-950 dark:text-accent-300'
                                        : 'bg-graphite-200 text-graphite-600 dark:bg-graphite-800 dark:text-graphite-400')"
                            >
                                <span class="font-semibold">{{ $i + 1 }}</span>
                                <span class="hidden sm:inline">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="booking-form-surface rounded-2xl border border-graphite-200 bg-white p-6 shadow-lg dark:border-graphite-700 dark:bg-graphite-900 md:p-8">
                        <template x-if="error">
                            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300" x-text="error"></div>
                        </template>

                        {{-- Step 1: Location --}}
                        <div x-show="step === 1" x-cloak>
                            <h2 class="mb-2 text-2xl font-bold text-graphite-900 dark:text-white">Choose a location</h2>
                            <p class="mb-6 text-graphite-600 dark:text-graphite-300">We operate multiple centres — pick the one closest to you.</p>
                            <div class="grid items-stretch gap-4 sm:grid-cols-2">
                                <template x-for="loc in locations" :key="loc.id">
                                    <button
                                        type="button"
                                        @click="selectLocation(loc)"
                                        class="flex h-full flex-col rounded-xl border p-5 text-left transition hover:border-brand-400 dark:hover:border-brand-500"
                                        :class="form.location_id === loc.id
                                            ? 'border-brand-600 ring-2 ring-brand-200 dark:border-brand-400 dark:ring-brand-800'
                                            : 'border-graphite-200 dark:border-graphite-700'"
                                    >
                                        <div class="font-semibold text-graphite-900 dark:text-white" x-text="loc.name"></div>
                                        <div class="mt-2 flex-1 text-sm leading-relaxed text-graphite-500 dark:text-graphite-400" x-text="loc.address"></div>
                                    </button>
                                </template>
                            </div>
                            <div class="mt-8 flex justify-end">
                                <button type="button" class="btn-cta disabled:opacity-40" :disabled="!form.location_id" @click="step = 2">Continue</button>
                            </div>
                        </div>

                        {{-- Step 2: Service --}}
                        <div x-show="step === 2" x-cloak>
                            <h2 class="mb-2 text-2xl font-bold text-graphite-900 dark:text-white">Select a service</h2>
                            <p class="mb-6 text-graphite-600 dark:text-graphite-300">Prices shown are starting rates for standard vehicles.</p>
                            <div class="max-h-[28rem] space-y-3 overflow-y-auto pr-1">
                                <template x-for="svc in services" :key="svc.id">
                                    <button
                                        type="button"
                                        @click="selectService(svc)"
                                        class="flex w-full items-start justify-between gap-4 rounded-xl border p-4 text-left transition hover:border-brand-400 dark:hover:border-brand-500"
                                        :class="form.service_id === svc.id
                                            ? 'border-brand-600 ring-2 ring-brand-200 dark:border-brand-400 dark:ring-brand-800'
                                            : 'border-graphite-200 dark:border-graphite-700'"
                                    >
                                        <div>
                                            <div class="font-semibold text-graphite-900 dark:text-white" x-text="svc.name"></div>
                                            <div class="mt-1 text-sm text-graphite-500 dark:text-graphite-400" x-text="svc.short"></div>
                                            <div class="mt-2 text-xs text-graphite-400 dark:text-graphite-500" x-text="svc.duration"></div>
                                        </div>
                                        <div class="shrink-0 text-lg font-bold text-brand-700 dark:text-accent-400" x-text="'₹' + Number(svc.price).toLocaleString('en-IN')"></div>
                                    </button>
                                </template>
                            </div>
                            <div class="mt-8 flex justify-between gap-3">
                                <button type="button" class="rounded-lg border border-graphite-300 px-6 py-3 font-medium text-graphite-700 transition hover:bg-graphite-50 dark:border-graphite-600 dark:text-graphite-200 dark:hover:bg-graphite-800" @click="step = 1">Back</button>
                                <button type="button" class="btn-cta disabled:opacity-40" :disabled="!form.service_id" @click="goToSchedule()">Continue</button>
                            </div>
                        </div>

                        {{-- Step 3: Schedule --}}
                        <div x-show="step === 3" x-cloak>
                            <h2 class="mb-2 text-2xl font-bold text-graphite-900 dark:text-white">Pick date & time</h2>
                            <p class="mb-6 text-graphite-600 dark:text-graphite-300">Available slots update based on location hours and existing bookings.</p>

                            <div class="mb-6" x-show="availableAddons.length" x-cloak>
                                <h3 class="mb-2 text-sm font-semibold text-graphite-800 dark:text-graphite-100">Optional add-ons</h3>
                                <div class="space-y-2">
                                    <template x-for="addon in availableAddons" :key="addon.id">
                                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-graphite-200 p-3 hover:border-brand-300 dark:border-graphite-700 dark:hover:border-brand-500">
                                            <input type="checkbox" class="mt-1 accent-brand-600" :value="addon.id" @change="toggleAddon(addon.id)" :checked="form.addon_ids.includes(addon.id)">
                                            <span class="flex-1">
                                                <span class="font-medium text-graphite-900 dark:text-white" x-text="addon.name"></span>
                                                <span class="mt-0.5 block text-xs text-graphite-500 dark:text-graphite-400" x-text="addon.duration"></span>
                                                <span class="mt-1 block text-sm text-graphite-500 dark:text-graphite-400" x-show="addon.description" x-text="addon.description"></span>
                                            </span>
                                            <span class="shrink-0 font-semibold text-brand-700 dark:text-accent-400" x-text="'₹' + Number(addon.price).toLocaleString('en-IN')"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="mb-1.5 block text-sm font-medium text-graphite-700 dark:text-graphite-200">Date</label>
                                <input type="date" class="form-input max-w-xs" x-model="form.booking_date" :min="minDate" :max="maxDate" @change="loadSlots()">
                            </div>
                            <div x-show="loadingSlots" class="py-8 text-center text-graphite-500 dark:text-graphite-400">Loading slots…</div>
                            <div x-show="!loadingSlots && slots.length === 0 && form.booking_date" class="rounded-lg bg-graphite-100 p-6 text-center text-graphite-600 dark:bg-graphite-800 dark:text-graphite-300">No slots available for this date. Try another day.</div>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                                <template x-for="slot in slots" :key="slot.start">
                                    <button
                                        type="button"
                                        @click="form.start_time = slot.start"
                                        class="rounded-lg border px-3 py-3 text-sm font-medium transition"
                                        :class="form.start_time === slot.start
                                            ? 'border-brand-600 bg-brand-50 text-brand-800 dark:border-brand-400 dark:bg-brand-950 dark:text-accent-300'
                                            : 'border-graphite-200 text-graphite-700 hover:border-brand-300 dark:border-graphite-700 dark:text-graphite-200 dark:hover:border-brand-500'"
                                        x-text="slot.label"
                                    ></button>
                                </template>
                            </div>
                            <div class="mt-8 flex justify-between gap-3">
                                <button type="button" class="rounded-lg border border-graphite-300 px-6 py-3 font-medium text-graphite-700 transition hover:bg-graphite-50 dark:border-graphite-600 dark:text-graphite-200 dark:hover:bg-graphite-800" @click="step = 2">Back</button>
                                <button type="button" class="btn-cta disabled:opacity-40" :disabled="!form.booking_date || !form.start_time" @click="step = 4">Continue</button>
                            </div>
                        </div>

                        {{-- Step 4: Details --}}
                        <div x-show="step === 4" x-cloak>
                            <h2 class="mb-2 text-2xl font-bold text-graphite-900 dark:text-white">Your details</h2>
                            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="form-label">Full name</label>
                                    <input type="text" class="form-input" x-model="form.customer_name" required>
                                </div>
                                <div>
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-input" x-model="form.customer_phone" required>
                                </div>
                                <div>
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-input" x-model="form.customer_email" required>
                                </div>
                                <div>
                                    <label class="form-label">Vehicle make & model</label>
                                    <input type="text" class="form-input" x-model="form.vehicle_make_model" placeholder="e.g. Honda City 2022" required>
                                </div>
                                <div>
                                    <label class="form-label">Number plate (optional)</label>
                                    <input type="text" class="form-input" x-model="form.vehicle_plate">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="form-label">Notes (optional)</label>
                                    <textarea class="form-input" rows="3" x-model="form.notes"></textarea>
                                </div>
                            </div>
                            <div class="mt-8 flex justify-between gap-3">
                                <button type="button" class="rounded-lg border border-graphite-300 px-6 py-3 font-medium text-graphite-700 transition hover:bg-graphite-50 dark:border-graphite-600 dark:text-graphite-200 dark:hover:bg-graphite-800" @click="step = 3">Back</button>
                                <button type="button" class="btn-cta" @click="goToPayment()">Continue</button>
                            </div>
                        </div>

                        {{-- Step 5: Payment --}}
                        <div x-show="step === 5" x-cloak>
                            <h2 class="mb-2 text-2xl font-bold text-graphite-900 dark:text-white">Confirm & pay</h2>
                            <div class="mt-6 rounded-xl bg-graphite-50 p-5 text-sm text-graphite-700 dark:bg-graphite-800 dark:text-graphite-200">
                                <p><span class="text-graphite-500 dark:text-graphite-400">Service:</span> <span class="font-medium" x-text="selectedService?.name"></span></p>
                                <template x-if="selectedAddons.length">
                                    <p class="mt-2"><span class="text-graphite-500 dark:text-graphite-400">Add-ons:</span> <span class="font-medium" x-text="selectedAddons.map(a => a.name).join(', ')"></span></p>
                                </template>
                                <p class="mt-2"><span class="text-graphite-500 dark:text-graphite-400">Location:</span> <span class="font-medium" x-text="selectedLocation?.name"></span></p>
                                <p class="mt-2"><span class="text-graphite-500 dark:text-graphite-400">When:</span> <span class="font-medium" x-text="form.booking_date + ' at ' + form.start_time"></span></p>
                                <div class="mt-4 space-y-2">
                                    <label class="block text-sm font-medium text-graphite-700 dark:text-graphite-200">Coupon code (optional)</label>
                                    <div class="flex gap-2">
                                        <input type="text" class="form-input uppercase" x-model="form.coupon_code" placeholder="SAVE10" @keydown.enter.prevent="applyCoupon()">
                                        <button type="button" class="rounded-lg border border-graphite-300 px-4 py-2 text-sm font-medium text-graphite-700 hover:bg-graphite-100 dark:border-graphite-600 dark:text-graphite-200 dark:hover:bg-graphite-700" @click="applyCoupon()" :disabled="applyingCoupon">Apply</button>
                                    </div>
                                    <p x-show="couponMessage" class="text-sm" :class="couponValid ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'" x-text="couponMessage"></p>
                                </div>
                                <p class="mt-2 text-sm text-graphite-500 dark:text-graphite-400" x-show="couponValid && discount > 0">
                                    Subtotal <span x-text="'₹' + Number(subtotal).toLocaleString('en-IN')"></span>
                                    − discount <span x-text="'₹' + Number(discount).toLocaleString('en-IN')"></span>
                                </p>
                                <p class="mt-2 text-lg font-bold text-brand-700 dark:text-accent-400" x-text="'₹' + Number(payableTotal).toLocaleString('en-IN')"></p>
                            </div>

                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                <button
                                    type="button"
                                    @click="form.payment_method = 'at_location'"
                                    class="rounded-xl border p-4 text-left transition"
                                    :class="form.payment_method === 'at_location'
                                        ? 'border-brand-600 ring-2 ring-brand-200 dark:border-brand-400 dark:ring-brand-800'
                                        : 'border-graphite-200 dark:border-graphite-700'"
                                >
                                    <div class="font-semibold text-graphite-900 dark:text-white">Pay at location</div>
                                    <div class="mt-1 text-sm text-graphite-500 dark:text-graphite-400">Confirm now, settle when you arrive.</div>
                                </button>
                                <button
                                    type="button"
                                    @click="form.payment_method = 'online'"
                                    class="rounded-xl border p-4 text-left transition disabled:cursor-not-allowed disabled:opacity-50"
                                    :disabled="!razorpayEnabled"
                                    :class="form.payment_method === 'online'
                                        ? 'border-brand-600 ring-2 ring-brand-200 dark:border-brand-400 dark:ring-brand-800'
                                        : 'border-graphite-200 dark:border-graphite-700'"
                                >
                                    <div class="font-semibold text-graphite-900 dark:text-white">Pay online</div>
                                    <div class="mt-1 text-sm text-graphite-500 dark:text-graphite-400" x-text="razorpayEnabled ? 'Secure checkout via Razorpay' : 'Online payments not configured yet'"></div>
                                </button>
                            </div>

                            <div class="mt-8 flex justify-between gap-3">
                                <button type="button" class="rounded-lg border border-graphite-300 px-6 py-3 font-medium text-graphite-700 transition hover:bg-graphite-50 dark:border-graphite-600 dark:text-graphite-200 dark:hover:bg-graphite-800" @click="step = 4" :disabled="submitting">Back</button>
                                <button type="button" class="btn-cta disabled:opacity-40" :disabled="submitting || !form.payment_method" @click="submitBooking()">
                                    <span x-show="!submitting">Confirm booking</span>
                                    <span x-show="submitting" x-cloak>Processing…</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>

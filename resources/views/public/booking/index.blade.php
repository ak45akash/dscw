<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription">
    <div class="relative flex h-[40vh] min-h-[280px] items-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/hero.jpg') }}" alt="Book car wash" class="h-full w-full object-cover brightness-[0.55]">
        </div>
        <div class="container-custom relative z-10 text-white">
            <h1 class="text-4xl font-bold md:text-5xl">Book Your <span class="text-blue-400">Service</span></h1>
            <p class="mt-3 max-w-2xl text-lg text-white/90">Choose a location, pick a slot, and confirm — pay online or at the centre.</p>
        </div>
    </div>

    <section class="bg-gradient-to-b from-gray-50 to-white py-16">
        <div class="container-custom">
            @if(! $bookingsEnabled)
                <div class="mx-auto max-w-2xl rounded-2xl border border-amber-200 bg-amber-50 p-8 text-center">
                    <h2 class="text-2xl font-bold text-gray-800">Online booking is temporarily closed</h2>
                    <p class="mt-3 text-gray-600">Please <a href="{{ route('contact.index') }}" class="font-medium text-blue-600 hover:underline">contact us</a> to schedule your appointment.</p>
                </div>
            @else
                <div
                    class="mx-auto max-w-4xl"
                    x-data="bookingWizard(@js([
                        'locations' => $locations->map(fn ($l) => ['id' => $l->id, 'name' => $l->name, 'city' => $l->city, 'address' => $l->fullAddress()])->values(),
                        'services' => $services->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'price' => (float) $s->price, 'duration' => $s->formattedDuration(), 'short' => $s->short_description, 'category' => $s->category?->name])->values(),
                        'preselectedServiceId' => $preselectedServiceId,
                        'razorpayEnabled' => $razorpayEnabled,
                        'maxAdvanceDays' => $maxAdvanceDays,
                        'sameDayBookings' => $sameDayBookings,
                        'slotsUrl' => route('booking.slots'),
                        'storeUrl' => route('booking.store'),
                        'verifyUrl' => route('booking.payment.verify'),
                        'csrf' => csrf_token(),
                    ]))"
                >
                    {{-- Progress --}}
                    <div class="mb-10 flex flex-wrap justify-center gap-2 text-sm">
                        @foreach(['Location', 'Service', 'Schedule', 'Details', 'Payment'] as $i => $label)
                            <div class="flex items-center gap-2 rounded-full px-3 py-1" :class="step === {{ $i + 1 }} ? 'bg-blue-600 text-white' : (step > {{ $i + 1 }} ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500')">
                                <span class="font-semibold">{{ $i + 1 }}</span>
                                <span class="hidden sm:inline">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="booking-form-surface rounded-2xl border border-gray-100 bg-white p-6 shadow-lg md:p-8">
                        <template x-if="error">
                            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="error"></div>
                        </template>

                        {{-- Step 1: Location --}}
                        <div x-show="step === 1" x-cloak>
                            <h2 class="mb-2 text-2xl font-bold text-gray-800">Choose a location</h2>
                            <p class="mb-6 text-gray-600">We operate multiple centres — pick the one closest to you.</p>
                            <div class="grid gap-4 md:grid-cols-2">
                                <template x-for="loc in locations" :key="loc.id">
                                    <button type="button" @click="selectLocation(loc)" class="rounded-xl border p-5 text-left transition hover:border-blue-400" :class="form.location_id === loc.id ? 'border-blue-600 ring-2 ring-blue-200' : 'border-gray-200'">
                                        <div class="font-semibold text-gray-900" x-text="loc.name"></div>
                                        <div class="mt-1 text-sm text-gray-500" x-text="loc.address"></div>
                                    </button>
                                </template>
                            </div>
                            <div class="mt-8 flex justify-end">
                                <button type="button" class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700 disabled:opacity-40" :disabled="!form.location_id" @click="step = 2">Continue</button>
                            </div>
                        </div>

                        {{-- Step 2: Service --}}
                        <div x-show="step === 2" x-cloak>
                            <h2 class="mb-2 text-2xl font-bold text-gray-800">Select a service</h2>
                            <p class="mb-6 text-gray-600">Prices shown are starting rates for standard vehicles.</p>
                            <div class="max-h-[28rem] space-y-3 overflow-y-auto pr-1">
                                <template x-for="svc in services" :key="svc.id">
                                    <button type="button" @click="selectService(svc)" class="flex w-full items-start justify-between gap-4 rounded-xl border p-4 text-left transition hover:border-blue-400" :class="form.service_id === svc.id ? 'border-blue-600 ring-2 ring-blue-200' : 'border-gray-200'">
                                        <div>
                                            <div class="font-semibold text-gray-900" x-text="svc.name"></div>
                                            <div class="mt-1 text-sm text-gray-500" x-text="svc.short"></div>
                                            <div class="mt-2 text-xs text-gray-400" x-text="svc.duration"></div>
                                        </div>
                                        <div class="shrink-0 text-lg font-bold text-blue-600" x-text="'₹' + Number(svc.price).toLocaleString('en-IN')"></div>
                                    </button>
                                </template>
                            </div>
                            <div class="mt-8 flex justify-between">
                                <button type="button" class="rounded-lg border border-gray-300 px-6 py-3 font-medium text-gray-700" @click="step = 1">Back</button>
                                <button type="button" class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700 disabled:opacity-40" :disabled="!form.service_id" @click="goToSchedule()">Continue</button>
                            </div>
                        </div>

                        {{-- Step 3: Schedule --}}
                        <div x-show="step === 3" x-cloak>
                            <h2 class="mb-2 text-2xl font-bold text-gray-800">Pick date & time</h2>
                            <p class="mb-6 text-gray-600">Available slots update based on location hours and existing bookings.</p>
                            <div class="mb-6">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700">Date</label>
                                <input type="date" class="form-input max-w-xs" x-model="form.booking_date" :min="minDate" :max="maxDate" @change="loadSlots()">
                            </div>
                            <div x-show="loadingSlots" class="py-8 text-center text-gray-500">Loading slots…</div>
                            <div x-show="!loadingSlots && slots.length === 0 && form.booking_date" class="rounded-lg bg-gray-50 p-6 text-center text-gray-600">No slots available for this date. Try another day.</div>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                                <template x-for="slot in slots" :key="slot.start">
                                    <button type="button" @click="form.start_time = slot.start" class="rounded-lg border px-3 py-3 text-sm font-medium transition" :class="form.start_time === slot.start ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 hover:border-blue-300'" x-text="slot.label"></button>
                                </template>
                            </div>
                            <div class="mt-8 flex justify-between">
                                <button type="button" class="rounded-lg border border-gray-300 px-6 py-3 font-medium text-gray-700" @click="step = 2">Back</button>
                                <button type="button" class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700 disabled:opacity-40" :disabled="!form.booking_date || !form.start_time" @click="step = 4">Continue</button>
                            </div>
                        </div>

                        {{-- Step 4: Details --}}
                        <div x-show="step === 4" x-cloak>
                            <h2 class="mb-2 text-2xl font-bold text-gray-800">Your details</h2>
                            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium">Full name</label>
                                    <input type="text" class="form-input" x-model="form.customer_name" required>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium">Phone</label>
                                    <input type="tel" class="form-input" x-model="form.customer_phone" required>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium">Email</label>
                                    <input type="email" class="form-input" x-model="form.customer_email" required>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium">Vehicle make & model</label>
                                    <input type="text" class="form-input" x-model="form.vehicle_make_model" placeholder="e.g. Honda City 2022" required>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium">Number plate (optional)</label>
                                    <input type="text" class="form-input" x-model="form.vehicle_plate">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium">Notes (optional)</label>
                                    <textarea class="form-input" rows="3" x-model="form.notes"></textarea>
                                </div>
                            </div>
                            <div class="mt-8 flex justify-between">
                                <button type="button" class="rounded-lg border border-gray-300 px-6 py-3 font-medium text-gray-700" @click="step = 3">Back</button>
                                <button type="button" class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700" @click="goToPayment()">Continue</button>
                            </div>
                        </div>

                        {{-- Step 5: Payment --}}
                        <div x-show="step === 5" x-cloak>
                            <h2 class="mb-2 text-2xl font-bold text-gray-800">Confirm & pay</h2>
                            <div class="mt-6 rounded-xl bg-gray-50 p-5 text-sm text-gray-700">
                                <p><span class="text-gray-500">Service:</span> <span class="font-medium" x-text="selectedService?.name"></span></p>
                                <p class="mt-2"><span class="text-gray-500">Location:</span> <span class="font-medium" x-text="selectedLocation?.name"></span></p>
                                <p class="mt-2"><span class="text-gray-500">When:</span> <span class="font-medium" x-text="form.booking_date + ' at ' + form.start_time"></span></p>
                                <p class="mt-2 text-lg font-bold text-blue-600" x-text="'₹' + Number(selectedService?.price || 0).toLocaleString('en-IN')"></p>
                            </div>

                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                <button type="button" @click="form.payment_method = 'at_location'" class="rounded-xl border p-4 text-left" :class="form.payment_method === 'at_location' ? 'border-blue-600 ring-2 ring-blue-200' : 'border-gray-200'">
                                    <div class="font-semibold">Pay at location</div>
                                    <div class="mt-1 text-sm text-gray-500">Confirm now, settle when you arrive.</div>
                                </button>
                                <button type="button" @click="form.payment_method = 'online'" class="rounded-xl border p-4 text-left disabled:cursor-not-allowed disabled:opacity-50" :disabled="!razorpayEnabled" :class="form.payment_method === 'online' ? 'border-blue-600 ring-2 ring-blue-200' : 'border-gray-200'">
                                    <div class="font-semibold">Pay online</div>
                                    <div class="mt-1 text-sm text-gray-500" x-text="razorpayEnabled ? 'Secure checkout via Razorpay' : 'Online payments not configured yet'"></div>
                                </button>
                            </div>

                            <div class="mt-8 flex justify-between">
                                <button type="button" class="rounded-lg border border-gray-300 px-6 py-3 font-medium text-gray-700" @click="step = 4" :disabled="submitting">Back</button>
                                <button type="button" class="rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 font-medium text-white shadow-lg hover:from-blue-700 hover:to-blue-800 disabled:opacity-40" :disabled="submitting || !form.payment_method" @click="submitBooking()">
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

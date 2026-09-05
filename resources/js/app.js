import Alpine from 'alpinejs';
import { registerWysiwygEditor } from './wysiwyg';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    registerWysiwygEditor(Alpine);

    Alpine.store('theme', {
        mode: 'system',
        preference: localStorage.getItem('dscw-theme') || 'system',

        init(adminMode = 'system') {
            this.mode = adminMode;
            this.apply();
        },

        isDark() {
            if (this.mode === 'disabled') {
                return false;
            }

            if (this.mode === 'enabled') {
                if (this.preference === 'dark') return true;
                if (this.preference === 'light') return false;
            }

            return window.matchMedia('(prefers-color-scheme: dark)').matches;
        },

        apply() {
            document.documentElement.classList.toggle('dark', this.isDark());
        },

        setPreference(value) {
            this.preference = value;
            localStorage.setItem('dscw-theme', value);
            this.apply();
        },

        toggle() {
            this.setPreference(this.isDark() ? 'light' : 'dark');
        },
    });

    Alpine.data('adminNavGroup', (key, forceOpen = false) => ({
        open: !!forceOpen,
        init() {
            if (forceOpen) {
                this.open = true;

                return;
            }

            const stored = localStorage.getItem(`dscw-admin-nav:${key}`);
            this.open = stored === '1';
        },
        toggle() {
            this.open = !this.open;
            localStorage.setItem(`dscw-admin-nav:${key}`, this.open ? '1' : '0');
        },
    }));

    Alpine.data('mobileNav', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        },
    }));

    Alpine.data('adminSidebar', () => ({
        mobileOpen: false,
        desktopCollapsed: localStorage.getItem('dscw-admin-sidebar-collapsed') === '1',
        toggle() {
            if (window.matchMedia('(min-width: 1024px)').matches) {
                this.desktopCollapsed = !this.desktopCollapsed;
                localStorage.setItem(
                    'dscw-admin-sidebar-collapsed',
                    this.desktopCollapsed ? '1' : '0'
                );

                return;
            }

            this.mobileOpen = !this.mobileOpen;
        },
        close() {
            this.mobileOpen = false;
        },
    }));

    Alpine.data('durationPicker', (config = {}) => ({
        days: Number(config.days || 0),
        hours: Number(config.hours || 0),
        minutes: Number(config.minutes || 0),
        minTotal: Number(config.minTotal || 15),
        maxTotal: Number(config.maxTotal || 20160),
        get totalMinutes() {
            return (this.days * 24 * 60) + (this.hours * 60) + this.minutes;
        },
        get summary() {
            const parts = [];
            if (this.days > 0) {
                parts.push(`${this.days} day${this.days === 1 ? '' : 's'}`);
            }
            if (this.hours > 0) {
                parts.push(`${this.hours} hour${this.hours === 1 ? '' : 's'}`);
            }
            if (this.minutes > 0) {
                parts.push(`${this.minutes} minute${this.minutes === 1 ? '' : 's'}`);
            }

            const label = parts.length ? parts.join(', ') : '0 minutes';

            return `Total: ${label} (${this.totalMinutes} minutes).`;
        },
        normalize() {
            this.days = Math.max(0, Math.min(14, Number(this.days) || 0));
            this.hours = Math.max(0, Math.min(23, Number(this.hours) || 0));
            this.minutes = Math.max(0, Math.min(59, Number(this.minutes) || 0));
        },
    }));

    Alpine.data('scrollReveal', () => ({
        init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                this.$el.classList.add('is-visible');

                return;
            }

            const observer = new IntersectionObserver(
                ([entry]) => {
                    if (entry.isIntersecting) {
                        this.$el.classList.add('is-visible');
                        observer.unobserve(this.$el);
                    }
                },
                { threshold: 0.12 }
            );

            observer.observe(this.$el);
        },
    }));

    Alpine.data('scrollSection', () => ({
        visible: false,
        init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                this.visible = true;

                return;
            }

            const observer = new IntersectionObserver(
                ([entry]) => {
                    if (entry.isIntersecting) {
                        this.visible = true;
                        observer.unobserve(this.$el);
                    }
                },
                { threshold: 0.15 }
            );

            observer.observe(this.$el);
        },
    }));

    Alpine.data('statsSection', () => ({
        visible: false,
        counts: [0, 0, 0, 0],
        targets: [5000, 15000, 98, 3],
        init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                this.visible = true;
                this.counts = [...this.targets];

                return;
            }

            const observer = new IntersectionObserver(
                ([entry]) => {
                    if (entry.isIntersecting) {
                        this.visible = true;
                        this.animateCounts();
                        observer.unobserve(this.$el);
                    }
                },
                { threshold: 0.15 }
            );

            observer.observe(this.$el);
        },
        animateCounts() {
            this.targets.forEach((target, index) => {
                const duration = 2000;
                const steps = 60;
                const increment = target / steps;
                let current = 0;
                let step = 0;

                const timer = setInterval(() => {
                    step += 1;
                    current += increment;

                    if (step >= steps) {
                        this.counts[index] = target;
                        clearInterval(timer);
                    } else {
                        this.counts[index] = Math.floor(current);
                    }
                }, duration / steps);
            });
        },
    }));

    Alpine.data('testimonialCarousel', (items) => ({
        items,
        current: 0,
        autoplay: true,
        interval: null,
        init() {
            this.startAutoplay();
        },
        startAutoplay() {
            this.interval = setInterval(() => {
                if (this.autoplay) {
                    this.next();
                }
            }, 5000);
        },
        prev() {
            this.current = this.current === 0 ? this.items.length - 1 : this.current - 1;
        },
        next() {
            this.current = this.current === this.items.length - 1 ? 0 : this.current + 1;
        },
        destroy() {
            clearInterval(this.interval);
        },
    }));

    Alpine.data('bookingWizard', (config) => ({
        step: 1,
        locations: config.locations || [],
        services: config.services || [],
        razorpayEnabled: !!config.razorpayEnabled,
        slotsUrl: config.slotsUrl,
        storeUrl: config.storeUrl,
        verifyUrl: config.verifyUrl,
        couponUrl: config.couponUrl,
        csrf: config.csrf,
        slots: [],
        loadingSlots: false,
        submitting: false,
        applyingCoupon: false,
        couponValid: false,
        couponMessage: null,
        discount: 0,
        error: null,
        form: {
            location_id: null,
            service_id: config.preselectedServiceId || null,
            addon_ids: [],
            booking_date: '',
            start_time: '',
            customer_name: '',
            customer_email: '',
            customer_phone: '',
            vehicle_make_model: '',
            vehicle_plate: '',
            notes: '',
            payment_method: 'at_location',
            coupon_code: '',
        },
        formatLocalDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        },
        get minDate() {
            const d = new Date();
            if (!config.sameDayBookings) {
                d.setDate(d.getDate() + 1);
            }

            return this.formatLocalDate(d);
        },
        get maxDate() {
            const d = new Date();
            d.setDate(d.getDate() + (config.maxAdvanceDays || 30));

            return this.formatLocalDate(d);
        },
        get selectedLocation() {
            return this.locations.find((l) => l.id === this.form.location_id);
        },
        get selectedService() {
            return this.services.find((s) => s.id === this.form.service_id);
        },
        get availableAddons() {
            return this.selectedService?.addons || [];
        },
        get selectedAddons() {
            const ids = this.form.addon_ids || [];
            return this.availableAddons.filter((a) => ids.includes(a.id));
        },
        get subtotal() {
            const servicePrice = Number(this.selectedService?.price || 0);
            const addonsPrice = this.selectedAddons.reduce((sum, a) => sum + Number(a.price || 0), 0);
            return servicePrice + addonsPrice;
        },
        get payableTotal() {
            if (!this.couponValid) {
                return this.subtotal;
            }

            return Math.max(0, this.subtotal - Number(this.discount || 0));
        },
        selectLocation(loc) {
            this.form.location_id = loc.id;
            this.form.start_time = '';
            this.slots = [];
        },
        selectService(svc) {
            this.form.service_id = svc.id;
            this.form.addon_ids = [];
            this.form.start_time = '';
            this.slots = [];
            this.resetCoupon();
        },
        toggleAddon(id) {
            const ids = this.form.addon_ids || [];
            if (ids.includes(id)) {
                this.form.addon_ids = ids.filter((v) => v !== id);
            } else {
                this.form.addon_ids = [...ids, id];
            }
            this.resetCoupon();
            if (this.form.booking_date) {
                this.loadSlots();
            }
        },
        resetCoupon() {
            this.couponValid = false;
            this.couponMessage = null;
            this.discount = 0;
        },
        async applyCoupon() {
            this.couponMessage = null;
            this.couponValid = false;
            this.discount = 0;
            const code = (this.form.coupon_code || '').trim();
            if (!code) {
                this.couponMessage = 'Enter a coupon code.';
                return;
            }
            if (!this.form.service_id) {
                this.couponMessage = 'Select a service first.';
                return;
            }
            this.applyingCoupon = true;
            try {
                const res = await fetch(this.couponUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                    body: JSON.stringify({
                        code,
                        service_id: this.form.service_id,
                        addon_ids: this.form.addon_ids || [],
                    }),
                });
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || 'Invalid coupon.');
                }
                this.form.coupon_code = data.code;
                this.discount = data.discount;
                this.couponValid = true;
                this.couponMessage = `Coupon applied — you save ₹${Number(data.discount).toLocaleString('en-IN')}.`;
            } catch (e) {
                this.couponMessage = e.message || 'Could not apply coupon.';
            } finally {
                this.applyingCoupon = false;
            }
        },
        goToSchedule() {
            this.step = 3;
            if (this.form.booking_date) {
                this.loadSlots();
            }
        },
        goToPayment() {
            this.error = null;
            if (!this.form.customer_name || !this.form.customer_email || !this.form.customer_phone || !this.form.vehicle_make_model) {
                this.error = 'Please fill in all required contact and vehicle fields.';
                return;
            }
            this.step = 5;
        },
        async loadSlots() {
            this.form.start_time = '';
            this.slots = [];
            if (!this.form.location_id || !this.form.service_id || !this.form.booking_date) {
                return;
            }
            this.loadingSlots = true;
            this.error = null;
            try {
                const params = new URLSearchParams({
                    location_id: this.form.location_id,
                    service_id: this.form.service_id,
                    date: this.form.booking_date,
                });
                (this.form.addon_ids || []).forEach((id) => params.append('addon_ids[]', id));
                const res = await fetch(`${this.slotsUrl}?${params.toString()}`, {
                    headers: { Accept: 'application/json' },
                });
                const data = await res.json();
                this.slots = data.slots || [];
            } catch (e) {
                this.error = 'Could not load available slots. Please try again.';
            } finally {
                this.loadingSlots = false;
            }
        },
        async submitBooking() {
            this.submitting = true;
            this.error = null;
            try {
                const payload = { ...this.form };
                if (!this.couponValid) {
                    payload.coupon_code = null;
                }
                const res = await fetch(this.storeUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || Object.values(data.errors || {}).flat()[0] || 'Booking failed.');
                }
                if (data.razorpay) {
                    await this.openRazorpay(data);
                    return;
                }
                window.location.href = data.confirmation_url;
            } catch (e) {
                this.error = e.message || 'Something went wrong.';
                this.submitting = false;
            }
        },
        openRazorpay(data) {
            return new Promise((resolve, reject) => {
                const launch = () => {
                    const options = {
                        key: data.razorpay.key,
                        amount: data.razorpay.amount,
                        currency: data.razorpay.currency,
                        name: 'Diamond Steam Car Wash',
                        description: data.booking.service,
                        order_id: data.razorpay.id,
                        prefill: {
                            name: data.booking.customer_name,
                            email: data.booking.customer_email,
                            contact: data.booking.customer_phone,
                        },
                        handler: async (response) => {
                            try {
                                const verify = await fetch(this.verifyUrl, {
                                    method: 'POST',
                                    headers: {
                                        Accept: 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': this.csrf,
                                    },
                                    body: JSON.stringify({
                                        reference: data.reference,
                                        razorpay_order_id: response.razorpay_order_id,
                                        razorpay_payment_id: response.razorpay_payment_id,
                                        razorpay_signature: response.razorpay_signature,
                                    }),
                                });
                                const payload = await verify.json();
                                if (!verify.ok) {
                                    throw new Error(payload.message || 'Payment verification failed.');
                                }
                                window.location.href = payload.confirmation_url;
                                resolve();
                            } catch (err) {
                                this.error = err.message;
                                this.submitting = false;
                                reject(err);
                            }
                        },
                        modal: {
                            ondismiss: () => {
                                this.error = 'Payment was cancelled. Your booking is reserved as unpaid — contact us or try again.';
                                this.submitting = false;
                                window.location.href = data.confirmation_url;
                                resolve();
                            },
                        },
                    };
                    const rzp = new window.Razorpay(options);
                    rzp.open();
                };

                if (window.Razorpay) {
                    launch();
                    return;
                }

                const script = document.createElement('script');
                script.src = 'https://checkout.razorpay.com/v1/checkout.js';
                script.onload = launch;
                script.onerror = () => {
                    this.error = 'Could not load Razorpay checkout.';
                    this.submitting = false;
                    reject(new Error('Razorpay script failed'));
                };
                document.body.appendChild(script);
            });
        },
        init() {
            if (this.form.service_id && this.locations.length === 1) {
                this.form.location_id = this.locations[0].id;
            }
        },
    }));
});

Alpine.start();

document.addEventListener('click', (event) => {
    const target = event.target;
    if (!(target instanceof HTMLInputElement)) {
        return;
    }

    if (!['date', 'time', 'datetime-local', 'month', 'week'].includes(target.type)) {
        return;
    }

    if (typeof target.showPicker !== 'function') {
        return;
    }

    try {
        target.showPicker();
    } catch {
        // Browsers may reject showPicker without a trusted gesture or when already open.
    }
});

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
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
        open: false,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
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
});

Alpine.start();

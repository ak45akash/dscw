<nav class="sticky top-0 z-40 border-b border-graphite-200 bg-white/95 py-3 shadow-sm backdrop-blur-sm dark:border-graphite-800 dark:bg-graphite-950/95 sm:py-4" x-data="mobileNav()">
    <div class="container-custom flex items-center justify-between gap-3">
        <a href="{{ route('home') }}" class="group flex min-w-0 items-center">
            <div class="relative mr-2 h-10 w-10 shrink-0 sm:mr-3 sm:h-12 sm:w-12">
                <img src="{{ asset('logo.png') }}" alt="Diamond Steam Car Wash Logo" class="h-full w-full object-contain" width="48" height="48">
            </div>
            <div class="min-w-0">
                <span class="block truncate text-base font-bold leading-tight text-brand-700 dark:text-accent-400 sm:text-xl">Diamond</span>
                <span class="block truncate text-xs leading-tight text-graphite-600 dark:text-graphite-300 sm:text-sm">Steam Car Wash</span>
            </div>
        </a>

        <div class="hidden items-center gap-5 lg:flex xl:gap-6">
            <a href="{{ route('home') }}" @class([request()->routeIs('home') ? 'nav-link-active' : 'nav-link'])>Home</a>
            <a href="{{ route('services.index') }}" @class([request()->routeIs('services.*') ? 'nav-link-active' : 'nav-link'])>Services</a>
            <a href="{{ route('blog.index') }}" @class([request()->routeIs('blog.*') ? 'nav-link-active' : 'nav-link'])>Blog</a>
            <a href="{{ route('gallery.index') }}" @class([request()->routeIs('gallery.*') ? 'nav-link-active' : 'nav-link'])>Gallery</a>
            <a href="{{ route('about') }}" @class([request()->routeIs('about') ? 'nav-link-active' : 'nav-link'])>About</a>
            <a href="{{ route('contact.index') }}" @class([request()->routeIs('contact.*') ? 'nav-link-active' : 'nav-link'])>Contact</a>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            @if(($themeMode ?? 'system') !== 'disabled')
                <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-graphite-300 bg-graphite-50 px-2.5 py-2 text-sm font-medium text-graphite-700 transition hover:bg-graphite-100 dark:border-graphite-600 dark:bg-graphite-800 dark:text-graphite-100 dark:hover:bg-graphite-700 sm:px-3 sm:py-2.5"
                        @click="open = !open"
                        :aria-expanded="open.toString()"
                        aria-label="Theme mode"
                    >
                        <svg x-show="$store.theme.preference === 'light'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05L5.636 5.636m12.728 0L16.95 7.05M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
                        <svg x-cloak x-show="$store.theme.preference === 'dark'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path></svg>
                        <svg x-cloak x-show="$store.theme.preference === 'system'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17h4.5M4 7a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2V7z"></path></svg>
                        <span class="hidden capitalize sm:inline" x-text="$store.theme.preference"></span>
                    </button>
                    <div
                        x-show="open"
                        x-transition
                        x-cloak
                        @click.outside="open = false"
                        class="absolute right-0 z-50 mt-2 w-40 overflow-hidden rounded-lg border border-graphite-200 bg-white py-1 shadow-lg dark:border-graphite-700 dark:bg-graphite-900"
                    >
                        @foreach(['light' => 'Light', 'dark' => 'Dark', 'system' => 'System'] as $value => $label)
                            <button
                                type="button"
                                class="flex w-full items-center px-3 py-2.5 text-left text-sm text-graphite-700 hover:bg-graphite-50 dark:text-graphite-200 dark:hover:bg-graphite-800"
                                :class="$store.theme.preference === '{{ $value }}' ? 'bg-brand-50 font-semibold text-brand-800 dark:bg-brand-950 dark:text-accent-400' : ''"
                                @click="$store.theme.setPreference('{{ $value }}'); open = false"
                            >{{ $label }}</button>
                        @endforeach
                    </div>
                </div>
            @endif

            <a
                href="{{ route('booking.index') }}"
                @class([
                    'btn-cta hidden !px-5 !py-2.5 !text-sm sm:inline-flex',
                    'ring-2 ring-brand-200' => request()->routeIs('booking.*'),
                ])
            >
                Book Now
            </a>

            <button type="button" class="rounded-lg p-2.5 text-graphite-800 hover:bg-graphite-100 dark:text-graphite-100 dark:hover:bg-graphite-800 lg:hidden" @click="toggle()" aria-label="Toggle menu" :aria-expanded="open.toString()">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="open ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"></path>
                </svg>
            </button>
        </div>
    </div>

    <div
        x-show="open"
        x-transition
        x-cloak
        class="absolute inset-x-0 top-full z-50 border-b border-graphite-100 bg-white shadow-lg dark:border-graphite-800 dark:bg-graphite-950 lg:hidden"
    >
        <div class="container-custom flex flex-col gap-1 py-3">
            <a href="{{ route('home') }}" @class(['rounded-lg px-3 py-3', request()->routeIs('home') ? 'bg-brand-50 font-semibold text-brand-700 dark:bg-brand-950 dark:text-accent-400' : 'text-graphite-700 hover:bg-graphite-50 dark:text-graphite-200 dark:hover:bg-graphite-900'])" @click="close()">Home</a>
            <a href="{{ route('services.index') }}" @class(['rounded-lg px-3 py-3', request()->routeIs('services.*') ? 'bg-brand-50 font-semibold text-brand-700 dark:bg-brand-950 dark:text-accent-400' : 'text-graphite-700 hover:bg-graphite-50 dark:text-graphite-200 dark:hover:bg-graphite-900'])" @click="close()">Services</a>
            <a href="{{ route('blog.index') }}" @class(['rounded-lg px-3 py-3', request()->routeIs('blog.*') ? 'bg-brand-50 font-semibold text-brand-700 dark:bg-brand-950 dark:text-accent-400' : 'text-graphite-700 hover:bg-graphite-50 dark:text-graphite-200 dark:hover:bg-graphite-900'])" @click="close()">Blog</a>
            <a href="{{ route('gallery.index') }}" @class(['rounded-lg px-3 py-3', request()->routeIs('gallery.*') ? 'bg-brand-50 font-semibold text-brand-700 dark:bg-brand-950 dark:text-accent-400' : 'text-graphite-700 hover:bg-graphite-50 dark:text-graphite-200 dark:hover:bg-graphite-900'])" @click="close()">Gallery</a>
            <a href="{{ route('about') }}" @class(['rounded-lg px-3 py-3', request()->routeIs('about') ? 'bg-brand-50 font-semibold text-brand-700 dark:bg-brand-950 dark:text-accent-400' : 'text-graphite-700 hover:bg-graphite-50 dark:text-graphite-200 dark:hover:bg-graphite-900'])" @click="close()">About</a>
            <a href="{{ route('contact.index') }}" @class(['rounded-lg px-3 py-3', request()->routeIs('contact.*') ? 'bg-brand-50 font-semibold text-brand-700 dark:bg-brand-950 dark:text-accent-400' : 'text-graphite-700 hover:bg-graphite-50 dark:text-graphite-200 dark:hover:bg-graphite-900'])" @click="close()">Contact</a>
            <a href="{{ route('booking.index') }}" class="btn-cta mt-2 w-full" @click="close()">Book Now</a>
        </div>
    </div>
</nav>

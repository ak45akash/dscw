<nav class="sticky top-0 z-40 bg-white/95 py-4 shadow-md backdrop-blur-sm transition-all duration-300" x-data="mobileNav()">
    <div class="container-custom flex items-center justify-between">
        <a href="{{ route('home') }}" class="group flex items-center">
            <div class="relative mr-3 h-12 w-12 transition-transform duration-300 group-hover:scale-110">
                <img src="{{ asset('logo.png') }}" alt="Diamond Steam Car Wash Logo" class="h-12 w-12 object-contain" width="48" height="48">
            </div>
            <div>
                <span class="block text-xl font-bold leading-tight text-blue-600 transition-colors duration-300 group-hover:text-blue-700">Diamond</span>
                <span class="block text-sm leading-tight text-gray-600 transition-colors duration-300 group-hover:text-gray-700">Steam Car Wash</span>
            </div>
        </a>

        <div class="hidden items-center space-x-6 md:flex">
            <a href="{{ route('home') }}" @class([request()->routeIs('home') ? 'nav-link-active' : 'nav-link'])>Home</a>
            <a href="{{ route('services.index') }}" @class([request()->routeIs('services.*') ? 'nav-link-active' : 'nav-link'])>Services</a>
            <a href="{{ route('about') }}" @class([request()->routeIs('about') ? 'nav-link-active' : 'nav-link'])>About</a>
            <a href="{{ route('contact.index') }}" @class([request()->routeIs('contact.*') ? 'nav-link-active' : 'nav-link'])>Contact</a>
        </div>

        <button type="button" class="rounded p-2 md:hidden" @click="toggle()" aria-label="Toggle menu">
            <svg class="h-6 w-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="open ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"></path>
            </svg>
        </button>

        <div class="hidden md:block">
            <a
                href="{{ route('booking.index') }}"
                @class([
                    'rounded-full bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 text-white shadow-lg transition-all duration-300 hover:from-blue-700 hover:to-blue-800 hover:shadow-xl transform hover:scale-105',
                    'ring-2 ring-blue-300' => request()->routeIs('booking.*'),
                ])
            >
                Book Now
            </a>
        </div>
    </div>

    <div
        x-show="open"
        x-transition
        class="absolute top-16 right-0 left-0 z-50 bg-white shadow-lg md:hidden"
        style="display: none;"
    >
        <div class="flex flex-col p-4">
            <a href="{{ route('home') }}" @class(['py-2', request()->routeIs('home') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-blue-600'])" @click="close()">Home</a>
            <a href="{{ route('services.index') }}" @class(['py-2', request()->routeIs('services.*') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-blue-600'])" @click="close()">Services</a>
            <a href="{{ route('about') }}" @class(['py-2', request()->routeIs('about') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-blue-600'])" @click="close()">About</a>
            <a href="{{ route('contact.index') }}" @class(['py-2', request()->routeIs('contact.*') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-blue-600'])" @click="close()">Contact</a>
            <a href="{{ route('booking.index') }}" class="mt-2 rounded-full bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 text-center text-white" @click="close()">Book Now</a>
        </div>
    </div>
</nav>

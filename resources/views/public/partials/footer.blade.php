@php
    $city = $business['city'] ?? 'Sahibzada Ajit Singh Nagar';
    $phone = $business['phone'] ?? '+91 98765 43210';
    $email = $business['email'] ?? 'hello@diamondsteamcarwash.com';
    $address = $business['address'] ?? 'Plot Number 589, Sector 66, Near Bestech Mall And Business Towers, Sahibzada Ajit Singh Nagar, Punjab';
@endphp

<footer class="bg-gray-900 text-white">
    <div class="container-custom py-12 sm:py-16">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <h3 class="mb-4 text-lg font-bold sm:text-xl">Diamond Steam Car Wash</h3>
                <p class="mb-4 text-sm leading-relaxed text-gray-400">
                    Premium car wash and detailing in {{ $city }} and Matour, Punjab.
                </p>
            </div>

            <div>
                <h3 class="mb-4 text-lg font-bold">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-gray-400 transition-colors hover:text-white">Home</a></li>
                    <li><a href="{{ route('services.index') }}" class="text-gray-400 transition-colors hover:text-white">Services</a></li>
                    <li><a href="{{ route('blog.index') }}" class="text-gray-400 transition-colors hover:text-white">Blog</a></li>
                    <li><a href="{{ route('gallery.index') }}" class="text-gray-400 transition-colors hover:text-white">Gallery</a></li>
                    <li><a href="{{ route('faq.index') }}" class="text-gray-400 transition-colors hover:text-white">FAQ</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-400 transition-colors hover:text-white">About Us</a></li>
                    <li><a href="{{ route('contact.index') }}" class="text-gray-400 transition-colors hover:text-white">Contact</a></li>
                </ul>
            </div>

            <div>
                <h3 class="mb-4 text-lg font-bold">Our Services</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('services.index') }}#basic-wash" class="text-gray-400 transition-colors hover:text-white">Basic Wash</a></li>
                    <li><a href="{{ route('services.index') }}#premium-steam-wash" class="text-gray-400 transition-colors hover:text-white">Premium Steam Wash</a></li>
                    <li><a href="{{ route('services.index') }}#interior-deep-clean" class="text-gray-400 transition-colors hover:text-white">Interior Detailing</a></li>
                    <li><a href="{{ route('services.index') }}#ceramic-coating" class="text-gray-400 transition-colors hover:text-white">Ceramic Coating</a></li>
                </ul>
            </div>

            <div>
                <h3 class="mb-4 text-lg font-bold">Contact Us</h3>
                <address class="space-y-3 text-sm not-italic text-gray-400">
                    <p class="flex items-start gap-2">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>{{ $address }}</span>
                    </p>
                    <p class="flex items-start gap-2">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span>{{ $phone }}</span>
                    </p>
                    <p class="flex items-start gap-2">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="break-all">{{ $email }}</span>
                    </p>
                </address>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-800">
        <div class="container-custom flex flex-col items-center justify-between gap-4 py-6 text-center md:flex-row md:text-left">
            <p class="text-sm text-gray-400">
                &copy; {{ date('Y') }} Diamond Steam Car Wash. All rights reserved.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2">
                <a href="{{ route('privacy') }}" class="text-sm text-gray-400 transition-colors hover:text-white">Privacy Policy</a>
                <a href="{{ route('terms') }}" class="text-sm text-gray-400 transition-colors hover:text-white">Terms of Service</a>
                <a href="https://iakash.dev" target="_blank" rel="noopener noreferrer" class="flex items-center text-sm text-gray-400 transition-colors hover:text-white">
                    <span>Powered by</span>
                    <span class="ml-1 font-semibold text-brand-400 hover:text-brand-300">ak45</span>
                </a>
            </div>
        </div>
    </div>
</footer>

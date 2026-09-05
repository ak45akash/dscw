@props(['service'])

<article class="group flex h-full flex-col overflow-hidden rounded-lg border border-gray-100 bg-white shadow-lg transition-all duration-300 hover:border-blue-200 hover-lift">
    <div class="relative h-60 overflow-hidden">
        <img
            src="{{ $service->imageUrl() }}"
            alt="{{ $service->name }}"
            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
            loading="lazy"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>

        @if($service->category)
            <div class="absolute top-3 right-3 animate-shimmer rounded-full bg-blue-600 px-3 py-1 text-xs font-medium text-white transition-colors duration-300 group-hover:bg-blue-700 sm:text-sm">
                {{ $service->category->name }}
            </div>
        @endif

        <div class="absolute top-3 left-3 rounded-full bg-white/90 px-3 py-1 text-sm font-bold text-blue-600 shadow-lg backdrop-blur-sm">
            {{ $service->formattedPrice() }}
        </div>
    </div>

    <div class="relative flex flex-grow flex-col p-4 sm:p-6">
        <div class="mb-4">
            <h3 class="text-xl font-bold text-gray-800 transition-colors duration-300 group-hover:text-blue-600">{{ $service->name }}</h3>
        </div>

        <div class="mb-4 flex items-center text-sm text-gray-500">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Duration: {{ $service->formattedDuration() }}
        </div>

        <div class="mb-6 flex-grow text-sm leading-relaxed text-gray-600">
            {{ $service->short_description }}
        </div>

        <div class="flex items-center justify-between">
            <div class="text-2xl font-bold text-blue-600">{{ $service->formattedPrice() }}</div>
            <a
                href="{{ route('booking.index', ['service' => $service->slug]) }}"
                class="flex transform items-center rounded-full bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 text-sm font-medium text-white shadow-lg transition-all duration-300 hover:from-blue-700 hover:to-blue-800 hover:shadow-xl group-hover:animate-pulse hover:scale-105"
            >
                Book Now
                <svg class="ml-2 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
        </div>

        <div class="absolute bottom-0 left-0 h-1 w-0 bg-gradient-to-r from-blue-500 to-purple-500 transition-all duration-500 group-hover:w-full"></div>
    </div>
</article>

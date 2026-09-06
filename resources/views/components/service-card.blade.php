@props(['service'])

<article class="group hover-lift flex h-full flex-col overflow-hidden rounded-xl border border-graphite-200 bg-white shadow-sm transition hover:border-brand-200 dark:border-graphite-700 dark:bg-graphite-900 dark:hover:border-brand-700">
    <div class="relative h-48 overflow-hidden sm:h-56">
        <img
            src="{{ $service->imageUrl() }}"
            alt="{{ $service->name }}"
            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
            loading="lazy"
        >
        @if($service->category)
            <div class="chip absolute top-3 right-3 bg-brand-600 text-white">
                {{ $service->category->name }}
            </div>
        @endif
        <div class="chip absolute top-3 left-3 bg-white/95 text-brand-800 shadow-sm dark:bg-graphite-900/95 dark:text-accent-400">
            {{ $service->formattedPrice() }}
        </div>
    </div>

    <div class="flex flex-1 flex-col p-4 sm:p-5">
        <h3 class="text-lg font-semibold text-graphite-900 transition group-hover:text-brand-700 dark:text-white dark:group-hover:text-accent-400 sm:text-xl">{{ $service->name }}</h3>

        <div class="mt-2 flex items-center text-xs text-graphite-500 sm:text-sm">
            <svg class="mr-1.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ $service->formattedDuration() }}
        </div>

        <p class="mt-3 flex-1 text-sm leading-relaxed text-graphite-600 dark:text-graphite-300">
            {{ $service->short_description }}
        </p>

        <div class="mt-5 flex items-center justify-between gap-3 border-t border-graphite-100 pt-4 dark:border-graphite-800">
            <div class="text-lg font-bold text-brand-700 dark:text-accent-400 sm:text-xl">{{ $service->formattedPrice() }}</div>
            <a
                href="{{ route('booking.index', ['service' => $service->slug]) }}"
                class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700"
            >
                Book Now
            </a>
        </div>
    </div>
</article>

@props([
    'title',
    'subtitle' => null,
    'badge' => null,
    'image' => 'images/hero.jpg',
    'speed' => 0.75,
])

<section {{ $attributes->merge(['class' => 'relative flex min-h-[38vh] items-center overflow-hidden sm:min-h-[44vh] lg:min-h-[48vh]']) }}>
    <x-parallax-bg :src="$image" :speed="$speed" brightness="0.45" position="center 30%" height="180%">
        <div class="absolute inset-0 bg-gradient-to-r from-graphite-950/75 via-brand-900/55 to-brand-800/35"></div>
    </x-parallax-bg>

    <div class="container-site relative z-10 py-14 text-white sm:py-16 lg:py-20">
        @if($badge)
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-accent-400 sm:text-sm">{{ $badge }}</p>
        @endif
        <h1 class="max-w-4xl text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-4 max-w-3xl text-base leading-relaxed text-white/90 sm:mt-5 sm:text-lg">{{ $subtitle }}</p>
        @endif
        @if(isset($actions))
            <div class="mt-8 flex flex-wrap gap-4">{{ $actions }}</div>
        @endif
    </div>
</section>

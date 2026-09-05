@props([
    'title',
    'subtitle' => null,
    'badge' => null,
])

<section {{ $attributes->merge(['class' => 'relative overflow-hidden bg-gradient-to-br from-brand-700 via-brand-600 to-brand-800 text-white']) }}>
    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, #38bdf8 0, transparent 40%), radial-gradient(circle at 80% 0%, #60a5fa 0, transparent 35%);"></div>
    <div class="container-site relative py-16 sm:py-20">
        @if($badge)
            <p class="mb-3 text-sm font-semibold uppercase tracking-[0.2em] text-accent-400">{{ $badge }}</p>
        @endif
        <h1 class="max-w-4xl text-4xl font-bold tracking-tight sm:text-5xl">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-5 max-w-3xl text-lg text-brand-100">{{ $subtitle }}</p>
        @endif
        @if(isset($actions))
            <div class="mt-8 flex flex-wrap gap-4">{{ $actions }}</div>
        @endif
    </div>
</section>

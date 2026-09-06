@props([
    'src',
    'alt' => '',
    'speed' => 0.7,
    'brightness' => '0.5',
    'position' => 'center 30%',
    'height' => '175%',
])

<div
    {{ $attributes->class(['absolute inset-0 z-0 overflow-hidden']) }}
    x-data="parallaxBg({{ (float) $speed }})"
>
    <img
        x-ref="bg"
        data-parallax-img
        src="{{ str_starts_with($src, 'http') || str_starts_with($src, '/') ? $src : asset($src) }}"
        alt="{{ $alt }}"
        class="parallax-layer absolute inset-x-0 top-[-35%] w-full object-cover will-change-transform"
        :style="`height: {{ $height }}; object-position: {{ $position }}; filter: brightness({{ $brightness }}); transform: translate3d(0, ${offset}px, 0)`"
        loading="eager"
        decoding="async"
    >
    {{ $slot }}
</div>

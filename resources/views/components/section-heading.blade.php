@props([
    'title',
    'subtitle' => null,
    'align' => 'left',
])

@php
    $alignment = $align === 'center' ? 'text-center mx-auto' : '';
@endphp

<div {{ $attributes->merge(['class' => $alignment]) }}>
    <h2 class="section-heading">{{ $title }}</h2>
    @if($subtitle)
        <p class="section-subheading {{ $align === 'center' ? 'mx-auto' : '' }}">{{ $subtitle }}</p>
    @endif
</div>

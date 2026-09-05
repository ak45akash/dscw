@props([
    'color' => 'gray',
])

@php
    $styles = match ($color) {
        'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'blue' => 'bg-brand-100 text-brand-800 dark:bg-brand-900 dark:text-brand-100',
        'amber' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
        default => 'bg-graphite-100 text-graphite-700 dark:bg-graphite-800 dark:text-graphite-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "badge {$styles}"]) }}>
    {{ $slot }}
</span>

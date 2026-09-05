@props([
    'type' => 'info',
])

@php
    $styles = match ($type) {
        'success' => 'border-green-200 bg-green-50 text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-200',
        'error' => 'border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-200',
        default => 'border-brand-200 bg-brand-50 text-brand-800 dark:border-brand-800 dark:bg-brand-950 dark:text-brand-100',
    };
@endphp

<div {{ $attributes->merge(['class' => "rounded-lg border px-4 py-3 text-sm {$styles}"]) }}>
    {{ $slot }}
</div>

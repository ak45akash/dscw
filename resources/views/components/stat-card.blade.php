@props([
    'title',
    'value',
    'hint' => null,
])

<div {{ $attributes->merge(['class' => 'card p-5']) }}>
    <p class="text-sm font-medium text-graphite-500 dark:text-graphite-400">{{ $title }}</p>
    <p class="mt-2 text-3xl font-bold text-graphite-900 dark:text-white">{{ $value }}</p>
    @if($hint)
        <p class="mt-1 text-xs text-graphite-500 dark:text-graphite-400">{{ $hint }}</p>
    @endif
</div>

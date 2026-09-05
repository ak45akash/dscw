@props([])

<p {{ $attributes->merge(['class' => 'mt-1.5 text-xs leading-relaxed text-graphite-500 dark:text-graphite-400']) }}>
    {{ $slot }}
</p>

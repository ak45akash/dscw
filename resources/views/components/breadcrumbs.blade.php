@props(['items' => []])

@if(count($items))
<nav aria-label="Breadcrumb" class="border-b border-graphite-200 bg-graphite-50 py-3 dark:border-graphite-800 dark:bg-graphite-900/50">
    <div class="container-site">
        <ol class="flex flex-wrap items-center gap-2 text-sm text-graphite-500 dark:text-graphite-400">
            @foreach($items as $i => $item)
                @if($i > 0)
                    <li aria-hidden="true">/</li>
                @endif
                <li>
                    @if(!empty($item['url']) && $i < count($items) - 1)
                        <a href="{{ $item['url'] }}" class="hover:text-brand-600 dark:hover:text-accent-400">{{ $item['label'] }}</a>
                    @else
                        <span class="text-graphite-700 dark:text-graphite-200">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>
@endif

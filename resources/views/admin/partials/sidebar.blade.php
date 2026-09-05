<aside
    class="fixed inset-y-0 left-0 z-50 w-72 border-r border-graphite-200 bg-white transition-transform duration-200 dark:border-graphite-800 dark:bg-graphite-900"
    :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>
    <div class="flex h-16 items-center border-b border-graphite-200 px-6 dark:border-graphite-800">
        <a href="{{ route('admin.dashboard') }}" class="font-semibold text-brand-700 dark:text-brand-300">
            DSCW Admin
        </a>
    </div>

    <nav class="h-[calc(100vh-4rem)] overflow-y-auto p-4">
        @foreach($adminNavigation ?? [] as $group)
            <div class="mb-6">
                <p class="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-graphite-400">
                    {{ $group['label'] }}
                </p>
                <ul class="space-y-1">
                    @foreach($group['items'] as $item)
                        @php
                            $isPlaceholder = ! empty($item['badge']);
                            $routeName = $item['route'] ?? null;
                            $isActive = ! $isPlaceholder
                                && $routeName
                                && Route::has($routeName)
                                && request()->routeIs($item['active'] ?? $routeName);
                        @endphp
                        <li>
                            <a
                                href="{{ (! $isPlaceholder && $routeName && Route::has($routeName)) ? route($routeName) : '#' }}"
                                @if($isPlaceholder) aria-disabled="true" @click.prevent @endif
                                @class([
                                    'flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                                    'bg-blue-50 text-blue-700 dark:bg-blue-600/25 dark:text-blue-200' => $isActive,
                                    'cursor-not-allowed opacity-70' => $isPlaceholder,
                                    'text-graphite-700 hover:bg-graphite-100 dark:text-graphite-200 dark:hover:bg-graphite-800' => ! $isActive && ! $isPlaceholder,
                                    'text-graphite-500 dark:text-graphite-400' => $isPlaceholder,
                                ])
                            >
                                <span>{{ $item['label'] }}</span>
                                @if(!empty($item['badge']))
                                    <x-badge color="gray">{{ $item['badge'] }}</x-badge>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>
</aside>

<aside
    class="admin-sidebar fixed inset-y-0 left-0 z-50 w-72 border-r border-graphite-200 bg-white transition-transform duration-200 dark:border-graphite-800 dark:bg-graphite-900"
    :class="[
        mobileOpen ? 'translate-x-0' : '-translate-x-full',
        desktopCollapsed ? 'lg:-translate-x-full' : 'lg:translate-x-0',
    ]"
    :aria-hidden="(desktopCollapsed && !mobileOpen).toString()"
>
    <div class="flex h-16 items-center justify-between gap-2 border-b border-graphite-200 px-4 dark:border-graphite-800 sm:px-5">
        <a href="{{ route('admin.dashboard') }}" class="truncate font-semibold text-brand-700 dark:text-brand-300">
            DSCW Admin
        </a>
        <button
            type="button"
            class="btn-ghost hidden px-2 py-2 lg:inline-flex"
            @click="toggle()"
            aria-label="Collapse sidebar"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
        <button
            type="button"
            class="btn-ghost px-2 py-2 lg:hidden"
            @click="close()"
            aria-label="Close sidebar"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <nav class="admin-sidebar-scroll h-[calc(100vh-4rem)] overflow-y-auto p-4" aria-label="Admin">
        @foreach($adminNavigation ?? [] as $group)
            @php
                $groupKey = \Illuminate\Support\Str::slug($group['label']);
                $groupHasActive = \App\Support\AdminNavigation::groupContainsActiveItem($group);
            @endphp
            <div
                class="mb-2"
                x-data="adminNavGroup(@js($groupKey), @js($groupHasActive))"
                data-nav-group="{{ $groupKey }}"
                data-nav-default-open="{{ $groupHasActive ? '1' : '0' }}"
            >
                <button
                    type="button"
                    class="flex w-full items-center justify-between rounded-lg px-2 py-2 text-left text-xs font-semibold uppercase tracking-wide text-graphite-400 hover:bg-graphite-100 hover:text-graphite-600 dark:hover:bg-graphite-800 dark:hover:text-graphite-200"
                    @click="toggle()"
                    :aria-expanded="open.toString()"
                    :aria-controls="'nav-group-{{ $groupKey }}'"
                    id="nav-group-toggle-{{ $groupKey }}"
                >
                    <span>{{ $group['label'] }}</span>
                    <svg
                        class="h-3.5 w-3.5 shrink-0 transition-transform duration-200"
                        :class="open ? 'rotate-180' : ''"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
                <ul
                    id="nav-group-{{ $groupKey }}"
                    class="mt-1 space-y-1"
                    x-show="open"
                    @unless($groupHasActive) x-cloak @endunless
                    role="list"
                    :aria-hidden="(!open).toString()"
                >
                    @foreach($group['items'] as $item)
                        @php
                            $isPlaceholder = ! empty($item['badge']);
                            $routeName = $item['route'] ?? null;
                            $isActive = \App\Support\AdminNavigation::itemIsActive($item);
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

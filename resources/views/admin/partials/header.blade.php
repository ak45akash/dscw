<header class="sticky top-0 z-30 border-b border-graphite-200 bg-white/90 backdrop-blur dark:border-graphite-800 dark:bg-graphite-900/90">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6">
        <div class="flex items-center gap-3">
            <button type="button" class="btn-ghost px-2 py-2 lg:hidden" @click="toggle()" aria-label="Toggle sidebar">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div>
                <h1 class="text-lg font-semibold text-graphite-900 dark:text-white">{{ $title ?? 'Dashboard' }}</h1>
                @if(!empty($breadcrumb))
                    <p class="text-xs text-graphite-500">{{ $breadcrumb }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" target="_blank" class="btn-ghost hidden sm:inline-flex">View Site</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <x-button type="submit" variant="secondary">Logout</x-button>
            </form>
        </div>
    </div>
</header>

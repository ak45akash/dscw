<x-layouts.admin title="Dashboard">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-graphite-900 dark:text-white">Welcome back, {{ auth()->user()->name }}</h2>
        <p class="mt-1 text-sm text-graphite-500">Overview of today's operations and quick actions.</p>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Today's Bookings" :value="$stats['today_bookings']" hint="Booking module coming soon" />
        <x-stat-card title="Pending" :value="$stats['pending_bookings']" />
        <x-stat-card title="Confirmed" :value="$stats['confirmed_bookings']" />
        <x-stat-card title="Completed" :value="$stats['completed_bookings']" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card>
            <h3 class="text-lg font-semibold">Quick Actions</h3>
            <div class="mt-4 flex flex-wrap gap-3">
                <x-button variant="secondary" href="{{ route('admin.settings.business') }}">Business Settings</x-button>
                <x-badge color="amber">Bookings — Soon</x-badge>
                <x-badge color="amber">Add Service — Soon</x-badge>
                <x-badge color="amber">Add Blog — Soon</x-badge>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-lg font-semibold">Phase 1 Complete</h3>
            <ul class="mt-4 space-y-2 text-sm text-graphite-600 dark:text-graphite-300">
                <li>✓ Laravel + Tailwind + Alpine foundation</li>
                <li>✓ Admin authentication & grouped sidebar</li>
                <li>✓ Roles, permissions & audit logging</li>
                <li>✓ Centralized business settings</li>
                <li>→ Next: Services, locations & booking engine</li>
            </ul>
        </x-card>
    </div>
</x-layouts.admin>

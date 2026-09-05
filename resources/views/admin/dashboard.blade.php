<x-layouts.admin title="Dashboard">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-graphite-900 dark:text-white">Welcome back, {{ auth()->user()->name }}</h2>
        <p class="mt-1 text-sm text-graphite-500">Overview of today's operations and quick actions.</p>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Today's Bookings" :value="$stats['today_bookings']" hint="Active bookings for today" />
        <x-stat-card title="Pending" :value="$stats['pending_bookings']" />
        <x-stat-card title="Confirmed" :value="$stats['confirmed_bookings']" />
        <x-stat-card title="Completed" :value="$stats['completed_bookings']" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card>
            <h3 class="text-lg font-semibold">Quick Actions</h3>
            <div class="mt-4 flex flex-wrap gap-3">
                <x-button href="{{ route('admin.bookings.today') }}">Today's Bookings</x-button>
                <x-button variant="secondary" href="{{ route('admin.services.create') }}">Add Service</x-button>
                <x-button variant="secondary" href="{{ route('admin.locations.index') }}">Locations</x-button>
                <x-button variant="secondary" href="{{ route('admin.settings.booking') }}">Booking Rules</x-button>
            </div>
            <p class="mt-4 text-sm text-graphite-500">{{ $counts['services'] }} services · {{ $counts['locations'] }} locations</p>
        </x-card>

        <x-card>
            <h3 class="text-lg font-semibold">Project status</h3>
            <div class="mt-4 space-y-5 text-sm text-graphite-600 dark:text-graphite-300">
                <div>
                    <p class="font-semibold text-graphite-900 dark:text-white">Phase 1 Complete</p>
                    <ul class="mt-2 space-y-1">
                        <li>✓ Laravel + Tailwind + Alpine foundation</li>
                        <li>✓ Admin authentication & grouped sidebar</li>
                        <li>✓ Roles, permissions & audit logging</li>
                        <li>✓ Centralized business settings</li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold text-graphite-900 dark:text-white">Phase 2 Complete</p>
                    <ul class="mt-2 space-y-1">
                        <li>✓ Services & categories admin CRUD</li>
                        <li>✓ Multi-location + working hours</li>
                        <li>✓ Booking engine with live availability</li>
                        <li>✓ Dual payment (Razorpay or pay at location)</li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold text-graphite-900 dark:text-white">→ Next: Phase 3</p>
                    <p class="mt-1">Content CMS (blog, pages, FAQ, gallery), coupons, email notifications, and booking calendar UI.</p>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.admin>

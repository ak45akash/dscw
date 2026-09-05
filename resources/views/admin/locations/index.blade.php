<x-layouts.admin title="Locations" breadcrumb="Business / Locations">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-graphite-500">Each location has its own address and weekly working hours.</p>
        <x-button href="{{ route('admin.locations.create') }}">Add Location</x-button>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        @forelse($locations as $location)
            <x-card>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $location->name }}</h3>
                        <p class="mt-1 text-sm text-graphite-500">{{ $location->fullAddress() }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($location->is_default)<x-badge color="blue">Default</x-badge>@endif
                            <x-badge :color="$location->is_active ? 'green' : 'gray'">{{ $location->is_active ? 'Active' : 'Inactive' }}</x-badge>
                            <x-badge>{{ $location->bookings_count }} bookings</x-badge>
                        </div>
                    </div>
                    <a href="{{ route('admin.locations.edit', $location) }}" class="btn-secondary">Edit</a>
                </div>
            </x-card>
        @empty
            <x-card class="md:col-span-2 text-center text-graphite-500">No locations yet.</x-card>
        @endforelse
    </div>
</x-layouts.admin>

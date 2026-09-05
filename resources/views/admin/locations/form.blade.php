@php
    $dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
@endphp
<x-layouts.admin :title="$location->exists ? 'Edit Location' : 'Add Location'" breadcrumb="Locations / Form">
    <form method="POST" action="{{ $location->exists ? route('admin.locations.update', $location) : route('admin.locations.store') }}" class="max-w-4xl space-y-6">
        @csrf
        @if($location->exists) @method('PUT') @endif

        <x-card>
            <h3 class="mb-4 text-lg font-semibold">Location details</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-label for="name" required>Name</x-label>
                    <x-input name="name" id="name" :value="old('name', $location->name)" required />
                </div>
                <div>
                    <x-label for="phone">Phone</x-label>
                    <x-input name="phone" id="phone" :value="old('phone', $location->phone)" />
                </div>
                <div>
                    <x-label for="email">Email</x-label>
                    <x-input type="email" name="email" id="email" :value="old('email', $location->email)" />
                </div>
                <div class="sm:col-span-2">
                    <x-label for="address" required>Address</x-label>
                    <textarea name="address" id="address" rows="2" class="form-input" required>{{ old('address', $location->address) }}</textarea>
                </div>
                <div>
                    <x-label for="city" required>City</x-label>
                    <x-input name="city" id="city" :value="old('city', $location->city)" required />
                </div>
                <div>
                    <x-label for="state">State</x-label>
                    <x-input name="state" id="state" :value="old('state', $location->state)" />
                </div>
                <div>
                    <x-label for="pincode">Pincode</x-label>
                    <x-input name="pincode" id="pincode" :value="old('pincode', $location->pincode)" />
                </div>
                <div>
                    <x-label for="display_order">Display order</x-label>
                    <x-input type="number" name="display_order" id="display_order" :value="old('display_order', $location->display_order ?? 0)" />
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $location->is_active ?? true))>
                    <x-label for="is_active" class="mb-0">Active</x-label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_default" id="is_default" value="1" @checked(old('is_default', $location->is_default ?? false))>
                    <x-label for="is_default" class="mb-0">Default location</x-label>
                </div>
            </div>
        </x-card>

        <x-card>
            <h3 class="mb-4 text-lg font-semibold">Working hours</h3>
            <div class="space-y-3">
                @foreach($dayNames as $day => $label)
                    @php $row = old('hours.'.$day, $hours[$day] ?? ['is_closed' => false, 'opens_at' => '09:00', 'closes_at' => '20:00']); @endphp
                    <div class="grid items-center gap-3 rounded-lg border border-graphite-200 p-3 dark:border-graphite-700 sm:grid-cols-4" x-data="{ closed: {{ !empty($row['is_closed']) ? 'true' : 'false' }} }">
                        <div class="font-medium">{{ $label }}</div>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="hidden" name="hours[{{ $day }}][is_closed]" value="0">
                            <input type="checkbox" name="hours[{{ $day }}][is_closed]" value="1" x-model="closed" @checked(!empty($row['is_closed']))>
                            Closed
                        </label>
                        <div>
                            <input type="time" name="hours[{{ $day }}][opens_at]" value="{{ $row['opens_at'] ?? '09:00' }}" class="form-input" :class="closed && 'opacity-40'">
                        </div>
                        <div>
                            <input type="time" name="hours[{{ $day }}][closes_at]" value="{{ $row['closes_at'] ?? '20:00' }}" class="form-input" :class="closed && 'opacity-40'">
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>

        <div class="flex justify-between">
            <a href="{{ route('admin.locations.index') }}" class="btn-secondary">Cancel</a>
            <x-button type="submit">Save Location</x-button>
        </div>
    </form>
</x-layouts.admin>

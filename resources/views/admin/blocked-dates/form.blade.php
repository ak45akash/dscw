<x-layouts.admin :title="$blockedDate->exists ? 'Edit Blocked Date' : 'Add Blocked Date'" breadcrumb="Blocked Dates / Form">
    <form method="POST" action="{{ $blockedDate->exists ? route('admin.blocked-dates.update', $blockedDate) : route('admin.blocked-dates.store') }}" class="max-w-xl space-y-6" x-data="{ fullDay: {{ old('is_full_day', $blockedDate->is_full_day ?? true) ? 'true' : 'false' }} }">
        @csrf
        @if($blockedDate->exists) @method('PUT') @endif
        <x-card class="space-y-4">
            <div>
                <x-label for="date" required>Date</x-label>
                <x-input type="date" name="date" id="date" :value="old('date', optional($blockedDate->date)->toDateString())" required />
            </div>
            <div>
                <x-label for="location_id">Location</x-label>
                <select name="location_id" id="location_id" class="form-input">
                    <option value="">All locations</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" @selected(old('location_id', $blockedDate->location_id) == $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-label for="reason">Reason</x-label>
                <x-input name="reason" id="reason" :value="old('reason', $blockedDate->reason)" />
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_full_day" value="1" x-model="fullDay" @checked(old('is_full_day', $blockedDate->is_full_day ?? true))>
                Full day block
            </label>
            <div class="grid grid-cols-2 gap-3" x-show="!fullDay">
                <div>
                    <x-label for="start_time">Start</x-label>
                    <x-input type="time" name="start_time" id="start_time" :value="old('start_time', $blockedDate->start_time ? substr($blockedDate->start_time,0,5) : '09:00')" />
                </div>
                <div>
                    <x-label for="end_time">End</x-label>
                    <x-input type="time" name="end_time" id="end_time" :value="old('end_time', $blockedDate->end_time ? substr($blockedDate->end_time,0,5) : '12:00')" />
                </div>
            </div>
        </x-card>
        <div class="flex justify-between">
            <a href="{{ route('admin.blocked-dates.index') }}" class="btn-secondary">Cancel</a>
            <x-button type="submit">Save</x-button>
        </div>
    </form>
</x-layouts.admin>

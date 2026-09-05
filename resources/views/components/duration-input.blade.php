@props([
    'totalMinutes' => 60,
    'name' => 'duration_minutes',
    'id' => 'duration_minutes',
])

@php
    $parts = \App\Support\Duration::toParts((int) old($name, $totalMinutes));
    $days = (int) old('duration_days', $parts['days']);
    $hours = (int) old('duration_hours', $parts['hours']);
    $minutes = (int) old('duration_part_minutes', $parts['minutes']);
@endphp

<div
    class="sm:col-span-2"
    x-data="durationPicker({
        days: {{ $days }},
        hours: {{ $hours }},
        minutes: {{ $minutes }},
        minTotal: {{ \App\Support\Duration::MIN_MINUTES }},
        maxTotal: {{ \App\Support\Duration::MAX_MINUTES }},
    })"
>
    <x-label :for="$id" required>Duration</x-label>
    <div class="grid grid-cols-3 gap-3">
        <div>
            <label for="duration_days" class="mb-1 block text-xs font-medium text-graphite-500">Days</label>
            <input
                type="number"
                name="duration_days"
                id="duration_days"
                min="0"
                max="14"
                class="form-input"
                x-model.number="days"
                @input="normalize()"
            >
        </div>
        <div>
            <label for="duration_hours" class="mb-1 block text-xs font-medium text-graphite-500">Hours</label>
            <input
                type="number"
                name="duration_hours"
                id="duration_hours"
                min="0"
                max="23"
                class="form-input"
                x-model.number="hours"
                @input="normalize()"
            >
        </div>
        <div>
            <label for="duration_part_minutes" class="mb-1 block text-xs font-medium text-graphite-500">Minutes</label>
            <input
                type="number"
                name="duration_part_minutes"
                id="duration_part_minutes"
                min="0"
                max="59"
                class="form-input"
                x-model.number="minutes"
                @input="normalize()"
            >
        </div>
    </div>
    <input type="hidden" name="{{ $name }}" id="{{ $id }}" :value="totalMinutes" value="{{ ($days * 1440) + ($hours * 60) + $minutes }}">
    <x-form-hint>
        <span x-text="summary"></span>
        Minimum {{ \App\Support\Duration::MIN_MINUTES }} minutes. Maximum 14 days.
    </x-form-hint>
</div>

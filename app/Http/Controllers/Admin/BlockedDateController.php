<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedDate;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlockedDateController extends Controller
{
    public function index(): View
    {
        $blockedDates = BlockedDate::query()
            ->with('location')
            ->orderByDesc('date')
            ->paginate(20);

        return view('admin.blocked-dates.index', compact('blockedDates'));
    }

    public function create(): View
    {
        return view('admin.blocked-dates.form', [
            'blockedDate' => new BlockedDate(['is_full_day' => true, 'date' => now()->addDay()->toDateString()]),
            'locations' => Location::query()->orderBy('display_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        BlockedDate::query()->create($data);

        return redirect()->route('admin.blocked-dates.index')->with('success', 'Blocked date added.');
    }

    public function edit(BlockedDate $blockedDate): View
    {
        return view('admin.blocked-dates.form', [
            'blockedDate' => $blockedDate,
            'locations' => Location::query()->orderBy('display_order')->get(),
        ]);
    }

    public function update(Request $request, BlockedDate $blockedDate): RedirectResponse
    {
        $blockedDate->update($this->validated($request));

        return redirect()->route('admin.blocked-dates.index')->with('success', 'Blocked date updated.');
    }

    public function destroy(BlockedDate $blockedDate): RedirectResponse
    {
        $blockedDate->delete();

        return redirect()->route('admin.blocked-dates.index')->with('success', 'Blocked date removed.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'location_id' => ['nullable', 'exists:locations,id'],
            'date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
            'is_full_day' => ['sometimes', 'boolean'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
        ]);

        $fullDay = $request->boolean('is_full_day', true);

        return [
            ...$data,
            'location_id' => $data['location_id'] ?: null,
            'is_full_day' => $fullDay,
            'start_time' => $fullDay ? null : ($data['start_time'] ?? null),
            'end_time' => $fullDay ? null : ($data['end_time'] ?? null),
        ];
    }
}

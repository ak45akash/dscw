<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\LocationWorkingHour;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function __construct(private AuditLogService $auditLog) {}

    public function index(): View
    {
        $locations = Location::query()->withCount('bookings')->orderBy('display_order')->get();

        return view('admin.locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('admin.locations.form', [
            'location' => new Location(['is_active' => true, 'city' => 'Mumbai', 'state' => 'Maharashtra']),
            'hours' => $this->defaultHours(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $location = DB::transaction(function () use ($request, $data) {
            if (! empty($data['is_default'])) {
                Location::query()->update(['is_default' => false]);
            }

            $location = Location::query()->create([
                ...$data,
                'slug' => $this->uniqueSlug($data['name']),
            ]);

            $this->syncHours($location, $request->input('hours', []));

            return $location;
        });

        $this->auditLog->log('locations', 'created', $location, null, $location->toArray());

        return redirect()->route('admin.locations.index')->with('success', 'Location created.');
    }

    public function edit(Location $location): View
    {
        $location->load('workingHours');
        $hours = [];

        foreach (range(0, 6) as $day) {
            $row = $location->workingHours->firstWhere('day_of_week', $day);
            $hours[$day] = [
                'is_closed' => $row?->is_closed ?? false,
                'opens_at' => $row?->opens_at ? substr((string) $row->opens_at, 0, 5) : '09:00',
                'closes_at' => $row?->closes_at ? substr((string) $row->closes_at, 0, 5) : '20:00',
            ];
        }

        return view('admin.locations.form', compact('location', 'hours'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $data = $this->validated($request);
        $old = $location->toArray();

        DB::transaction(function () use ($request, $location, $data) {
            if (! empty($data['is_default'])) {
                Location::query()->where('id', '!=', $location->id)->update(['is_default' => false]);
            }

            $location->update($data);
            $this->syncHours($location, $request->input('hours', []));
        });

        $this->auditLog->log('locations', 'updated', $location, $old, $location->fresh()->toArray());

        return redirect()->route('admin.locations.index')->with('success', 'Location updated.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->bookings()->exists()) {
            return back()->withErrors(['location' => 'Cannot delete a location with bookings. Deactivate it instead.']);
        }

        $old = $location->toArray();
        $location->delete();
        $this->auditLog->log('locations', 'deleted', null, $old, null);

        return redirect()->route('admin.locations.index')->with('success', 'Location deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
            'hours' => ['array'],
            'hours.*.is_closed' => ['sometimes', 'boolean'],
            'hours.*.opens_at' => ['nullable', 'date_format:H:i'],
            'hours.*.closes_at' => ['nullable', 'date_format:H:i'],
        ]);

        return [
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'] ?? null,
            'pincode' => $data['pincode'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'is_default' => $request->boolean('is_default'),
            'display_order' => (int) ($data['display_order'] ?? 0),
        ];
    }

    private function syncHours(Location $location, array $hours): void
    {
        foreach (range(0, 6) as $day) {
            $row = $hours[$day] ?? [];
            $closed = filter_var($row['is_closed'] ?? false, FILTER_VALIDATE_BOOLEAN);

            LocationWorkingHour::query()->updateOrCreate(
                ['location_id' => $location->id, 'day_of_week' => $day],
                [
                    'is_closed' => $closed,
                    'opens_at' => $closed ? null : ($row['opens_at'] ?? '09:00'),
                    'closes_at' => $closed ? null : ($row['closes_at'] ?? '20:00'),
                ]
            );
        }
    }

    private function defaultHours(): array
    {
        $hours = [];
        foreach (range(0, 6) as $day) {
            $hours[$day] = [
                'is_closed' => $day === 0,
                'opens_at' => '09:00',
                'closes_at' => '20:00',
            ];
        }

        return $hours;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Location::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}

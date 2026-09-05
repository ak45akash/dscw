<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceAddon;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceAddonController extends Controller
{
    public function __construct(private AuditLogService $auditLog) {}

    public function index(): View
    {
        abort_unless(auth()->user()?->hasPermission('services.manage'), 403);

        $addons = ServiceAddon::query()->orderBy('display_order')->orderBy('name')->paginate(20);

        return view('admin.service-addons.index', compact('addons'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()?->hasPermission('services.manage'), 403);

        return view('admin.service-addons.form', [
            'addon' => new ServiceAddon(['is_active' => true, 'price' => 0, 'duration_minutes' => 0]),
            'services' => Service::query()->orderBy('name')->get(),
            'selectedServiceIds' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->hasPermission('services.manage'), 403);

        $data = $this->validated($request);
        $serviceIds = $data['service_ids'];
        unset($data['service_ids']);
        $data['slug'] = $this->uniqueSlug($data['name']);

        $addon = ServiceAddon::query()->create($data);
        $addon->services()->sync($serviceIds);

        $this->auditLog->log('service_addons', 'created', $addon, null, $addon->toArray());

        return redirect()->route('admin.service-addons.index')->with('success', 'Add-on created.');
    }

    public function edit(ServiceAddon $serviceAddon): View
    {
        abort_unless(auth()->user()?->hasPermission('services.manage'), 403);

        return view('admin.service-addons.form', [
            'addon' => $serviceAddon,
            'services' => Service::query()->orderBy('name')->get(),
            'selectedServiceIds' => $serviceAddon->services()->pluck('services.id')->all(),
        ]);
    }

    public function update(Request $request, ServiceAddon $serviceAddon): RedirectResponse
    {
        abort_unless(auth()->user()?->hasPermission('services.manage'), 403);

        $data = $this->validated($request);
        $serviceIds = $data['service_ids'];
        unset($data['service_ids']);
        $old = $serviceAddon->toArray();

        $serviceAddon->update($data);
        $serviceAddon->services()->sync($serviceIds);

        $this->auditLog->log('service_addons', 'updated', $serviceAddon, $old, $serviceAddon->fresh()->toArray());

        return redirect()->route('admin.service-addons.index')->with('success', 'Add-on updated.');
    }

    public function destroy(ServiceAddon $serviceAddon): RedirectResponse
    {
        abort_unless(auth()->user()?->hasPermission('services.manage'), 403);

        $old = $serviceAddon->toArray();
        $serviceAddon->services()->detach();
        $serviceAddon->delete();
        $this->auditLog->log('service_addons', 'deleted', null, $old, null);

        return redirect()->route('admin.service-addons.index')->with('success', 'Add-on deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:0', 'max:480'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
        ]);

        return [
            ...$data,
            'display_order' => (int) ($data['display_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
            'service_ids' => $data['service_ids'] ?? [],
        ];
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'addon';
        $slug = $base;
        $i = 1;

        while (ServiceAddon::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}

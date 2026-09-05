<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Services\AuditLogService;
use App\Services\HtmlSanitizer;
use App\Services\ServiceImageService;
use App\Support\Duration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class ServiceController extends Controller
{
    public function __construct(
        private AuditLogService $auditLog,
        private HtmlSanitizer $htmlSanitizer,
        private ServiceImageService $serviceImages,
    ) {}

    public function index(): View
    {
        $services = Service::query()->with('category')->orderBy('display_order')->paginate(20);

        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.services.form', [
            'service' => new Service(['is_active' => true, 'duration_minutes' => 60, 'price' => 0]),
            'categories' => ServiceCategory::query()->orderBy('display_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['image'] = $this->resolveImage($request);

        $service = Service::query()->create($data);
        $this->auditLog->log('services', 'created', $service, null, $service->toArray());

        return redirect()->route('admin.services.index')->with('success', 'Service created.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.form', [
            'service' => $service,
            'categories' => ServiceCategory::query()->orderBy('display_order')->get(),
        ]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $this->validated($request, $service);
        $old = $service->toArray();
        $data['image'] = $this->resolveImage($request, $service);
        $service->update($data);
        $this->auditLog->log('services', 'updated', $service, $old, $service->fresh()->toArray());

        return redirect()->route('admin.services.index')->with('success', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->bookings()->exists()) {
            return back()->withErrors(['service' => 'Cannot delete a service that has bookings. Deactivate it instead.']);
        }

        $old = $service->toArray();
        $imagePath = $service->image;
        $service->delete();
        $this->serviceImages->delete($imagePath);
        $this->auditLog->log('services', 'deleted', null, $old, null);

        return redirect()->route('admin.services.index')->with('success', 'Service deleted.');
    }

    private function validated(Request $request, ?Service $service = null): array
    {
        $data = $request->validate([
            'service_category_id' => ['nullable', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['nullable', 'integer', 'min:0', 'max:14'],
            'duration_hours' => ['nullable', 'integer', 'min:0', 'max:23'],
            'duration_part_minutes' => ['nullable', 'integer', 'min:0', 'max:59'],
            'duration_minutes' => ['nullable', 'integer', 'min:'.Duration::MIN_MINUTES, 'max:'.Duration::MAX_MINUTES],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'remove_image' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'featured_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]) + [
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
            'display_order' => (int) $request->input('display_order', $service?->display_order ?? 0),
        ];

        $data['duration_minutes'] = $this->resolveDurationMinutes($request);
        unset(
            $data['duration_days'],
            $data['duration_hours'],
            $data['duration_part_minutes'],
            $data['image'],
            $data['remove_image'],
        );

        $data['short_description'] = trim(strip_tags((string) $data['short_description']));
        $data['description'] = $this->htmlSanitizer->sanitize($data['description']);

        return $data;
    }

    private function resolveImage(Request $request, ?Service $service = null): ?string
    {
        $current = $service?->image;

        if ($request->boolean('remove_image') && ! $request->hasFile('image')) {
            $this->serviceImages->delete($current);

            return null;
        }

        if (! $request->hasFile('image')) {
            return $current;
        }

        try {
            $path = $this->serviceImages->store($request->file('image'));
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'image' => $exception->getMessage(),
            ]);
        }

        $this->serviceImages->delete($current);

        return $path;
    }

    private function resolveDurationMinutes(Request $request): int
    {
        if ($request->has(['duration_days', 'duration_hours', 'duration_part_minutes'])) {
            $total = Duration::fromParts(
                (int) $request->input('duration_days', 0),
                (int) $request->input('duration_hours', 0),
                (int) $request->input('duration_part_minutes', 0),
            );
        } else {
            $total = (int) $request->input('duration_minutes', 0);
        }

        if ($total < Duration::MIN_MINUTES || $total > Duration::MAX_MINUTES) {
            throw ValidationException::withMessages([
                'duration_minutes' => 'Duration must be between '.Duration::MIN_MINUTES.' minutes and 14 days.',
            ]);
        }

        return $total;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Service::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}

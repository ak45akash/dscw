<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Services\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    public function __construct(private HtmlSanitizer $htmlSanitizer) {}

    public function index(): View
    {
        $categories = ServiceCategory::query()->withCount('services')->orderBy('display_order')->get();

        return view('admin.service-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.service-categories.form', [
            'category' => new ServiceCategory(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        ServiceCategory::query()->create([
            ...$data,
            'slug' => Str::slug($data['name']),
            'is_active' => $request->boolean('is_active', true),
            'display_order' => (int) ($data['display_order'] ?? 0),
        ]);

        return redirect()->route('admin.service-categories.index')->with('success', 'Category created.');
    }

    public function edit(ServiceCategory $serviceCategory): View
    {
        return view('admin.service-categories.form', ['category' => $serviceCategory]);
    }

    public function update(Request $request, ServiceCategory $serviceCategory): RedirectResponse
    {
        $data = $this->validated($request);

        $serviceCategory->update([
            ...$data,
            'is_active' => $request->boolean('is_active'),
            'display_order' => (int) ($data['display_order'] ?? 0),
        ]);

        return redirect()->route('admin.service-categories.index')->with('success', 'Category updated.');
    }

    public function destroy(ServiceCategory $serviceCategory): RedirectResponse
    {
        if ($serviceCategory->services()->exists()) {
            return back()->withErrors(['category' => 'Reassign or delete services before removing this category.']);
        }

        $serviceCategory->delete();

        return redirect()->route('admin.service-categories.index')->with('success', 'Category deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['description'] = $this->htmlSanitizer->sanitize($data['description'] ?? '');

        return $data;
    }
}

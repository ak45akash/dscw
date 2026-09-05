<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private HtmlSanitizer $htmlSanitizer) {}

    public function index(): View
    {
        $pages = Page::query()->orderBy('title')->paginate(20);

        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin.pages.form', [
            'page' => new Page(['is_active' => true, 'template' => 'default']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);

        Page::query()->create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page created.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $this->validated($request, $page);
        if ($request->filled('slug')) {
            $data['slug'] = Str::slug($request->string('slug')->toString());
        }

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }

    private function validated(Request $request, ?Page $page = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('pages', 'slug')->ignore($page?->id)],
            'content' => ['required', 'string'],
            'template' => ['nullable', 'string', 'max:50'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return [
            'title' => $data['title'],
            'content' => $this->htmlSanitizer->sanitize($data['content']),
            'template' => $data['template'] ?? 'default',
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (Page::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}

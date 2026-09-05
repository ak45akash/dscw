<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogTaxonomyController extends Controller
{
    public function index(): View
    {
        return view('admin.blog-taxonomies.index', [
            'categories' => BlogCategory::query()->withCount('posts')->orderBy('name')->get(),
            'tags' => BlogTag::query()->withCount('posts')->orderBy('name')->get(),
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        BlogCategory::query()->create([
            'name' => $data['name'],
            'slug' => $this->uniqueCategorySlug($data['name']),
            'description' => $data['description'] ?? null,
        ]);

        return back()->with('success', 'Blog category created.');
    }

    public function storeTag(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        BlogTag::query()->create([
            'name' => $data['name'],
            'slug' => $this->uniqueTagSlug($data['name']),
        ]);

        return back()->with('success', 'Blog tag created.');
    }

    public function destroyCategory(BlogCategory $blogCategory): RedirectResponse
    {
        if ($blogCategory->posts()->exists()) {
            return back()->withErrors(['category' => 'Reassign posts before deleting this category.']);
        }

        $blogCategory->delete();

        return back()->with('success', 'Category deleted.');
    }

    public function destroyTag(BlogTag $blogTag): RedirectResponse
    {
        $blogTag->posts()->detach();
        $blogTag->delete();

        return back()->with('success', 'Tag deleted.');
    }

    private function uniqueCategorySlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;
        while (BlogCategory::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function uniqueTagSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;
        while (BlogTag::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}

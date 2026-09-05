<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Services\HtmlSanitizer;
use App\Services\ServiceImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class BlogPostController extends Controller
{
    public function __construct(
        private HtmlSanitizer $htmlSanitizer,
        private ServiceImageService $images,
    ) {}

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = $request->string('s')->toString();

        $posts = BlogPost::query()
            ->with(['category', 'author', 'tags'])
            ->when($status === 'published', fn ($q) => $q->where('status', 'published'))
            ->when($status === 'draft', fn ($q) => $q->where('status', 'draft'))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'all' => BlogPost::query()->count(),
            'published' => BlogPost::query()->where('status', 'published')->count(),
            'draft' => BlogPost::query()->where('status', 'draft')->count(),
        ];

        return view('admin.blog-posts.index', compact('posts', 'counts', 'status', 'search'));
    }

    public function create(): View
    {
        return view('admin.blog-posts.form', [
            'post' => new BlogPost(['status' => 'draft']),
            'categories' => BlogCategory::query()->orderBy('name')->get(),
            'tags' => BlogTag::query()->orderBy('name')->get(),
            'selectedTagIds' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->resolveSlug($request->input('slug'), $data['title']);
        $data['user_id'] = $request->user()->id;
        $data['featured_image'] = $this->resolveImage($request);

        $post = BlogPost::query()->create($data);
        $post->tags()->sync($request->input('tag_ids', []));

        return redirect()
            ->route('admin.blog-posts.edit', $post)
            ->with('success', $post->status === 'published' ? 'Post published.' : 'Draft saved.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.form', [
            'post' => $blogPost->load('tags'),
            'categories' => BlogCategory::query()->orderBy('name')->get(),
            'tags' => BlogTag::query()->orderBy('name')->get(),
            'selectedTagIds' => $blogPost->tags->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $this->validated($request, $blogPost);
        $data['slug'] = $this->resolveSlug($request->input('slug'), $data['title'], $blogPost->id);
        $data['featured_image'] = $this->resolveImage($request, $blogPost);
        $blogPost->update($data);
        $blogPost->tags()->sync($request->input('tag_ids', []));

        return redirect()
            ->route('admin.blog-posts.edit', $blogPost)
            ->with('success', $blogPost->status === 'published' ? 'Post updated.' : 'Draft updated.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $this->images->delete($blogPost->featured_image);
        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')->with('success', 'Post moved to trash (deleted).');
    }

    private function validated(Request $request, ?BlogPost $post = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($post?->id)],
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'remove_image' => ['sometimes', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:blog_tags,id'],
        ]);

        unset($data['featured_image'], $data['remove_image'], $data['tag_ids'], $data['slug']);

        $data['content'] = $this->htmlSanitizer->sanitize($data['content']);
        $data['excerpt'] = filled($data['excerpt'] ?? null)
            ? $data['excerpt']
            : Str::limit(strip_tags($data['content']), 160);

        $publishedAt = filled($request->input('published_at'))
            ? $request->date('published_at')
            : null;

        if ($data['status'] === 'published') {
            $data['published_at'] = $publishedAt ?? $post?->published_at ?? now();
        } else {
            $data['published_at'] = $publishedAt;
        }

        return $data;
    }

    private function resolveSlug(?string $slug, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug(filled($slug) ? $slug : $title) ?: 'post';
        $candidate = $base;
        $i = 1;

        while (
            BlogPost::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$i++;
        }

        return $candidate;
    }

    private function resolveImage(Request $request, ?BlogPost $post = null): ?string
    {
        $current = $post?->featured_image;

        if ($request->boolean('remove_image') && ! $request->hasFile('featured_image')) {
            $this->images->delete($current);

            return null;
        }

        if (! $request->hasFile('featured_image')) {
            return $current;
        }

        try {
            $path = $this->images->store($request->file('featured_image'), 'blog');
        } catch (InvalidArgumentException $e) {
            throw ValidationException::withMessages(['featured_image' => $e->getMessage()]);
        }

        $this->images->delete($current);

        return $path;
    }
}

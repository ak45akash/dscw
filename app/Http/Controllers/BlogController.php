<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = BlogPost::query()->published()->with(['category', 'tags']);

        if ($category = $request->query('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }

        $posts = $query->latest('published_at')->paginate(9)->withQueryString();
        $categories = BlogCategory::query()->withCount(['posts' => fn ($q) => $q->published()])->get();
        $featured = BlogPost::query()->published()->latest('published_at')->first();

        return view('public.blog.index', [
            'posts' => $posts,
            'categories' => $categories,
            'featured' => $featured,
            'activeCategory' => $category,
            'seoTitle' => 'Car Care Blog & Tips | Diamond Steam Car Wash',
            'seoDescription' => 'Expert guides on car washing, detailing, ceramic coating, PPF, and vehicle maintenance from Diamond Steam Car Wash Mumbai.',
        ]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->with(['category', 'tags', 'author'])
            ->firstOrFail();

        $related = BlogPost::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, fn ($q) => $q->where('blog_category_id', $post->blog_category_id))
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('public.blog.show', [
            'post' => $post,
            'related' => $related,
            'seoTitle' => $post->meta_title ?? $post->title,
            'seoDescription' => $post->meta_description ?? $post->excerpt,
        ]);
    }
}

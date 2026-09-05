<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()->where('email', 'admin@diamondsteamcarwash.com')->first();

        $categories = [
            'car-care' => BlogCategory::query()->updateOrCreate(['slug' => 'car-care'], ['name' => 'Car Care', 'description' => 'General car care tips and maintenance advice.']),
            'detailing' => BlogCategory::query()->updateOrCreate(['slug' => 'detailing'], ['name' => 'Detailing', 'description' => 'Professional detailing guides and techniques.']),
            'ceramic-coating' => BlogCategory::query()->updateOrCreate(['slug' => 'ceramic-coating'], ['name' => 'Ceramic Coating', 'description' => 'Ceramic coating benefits, application, and maintenance.']),
            'ppf' => BlogCategory::query()->updateOrCreate(['slug' => 'ppf'], ['name' => 'PPF', 'description' => 'Paint protection film guides and comparisons.']),
            'cleaning' => BlogCategory::query()->updateOrCreate(['slug' => 'cleaning'], ['name' => 'Cleaning', 'description' => 'Interior and exterior cleaning best practices.']),
            'maintenance' => BlogCategory::query()->updateOrCreate(['slug' => 'maintenance'], ['name' => 'Maintenance', 'description' => 'Long-term vehicle maintenance strategies.']),
        ];

        $tagMap = [];
        foreach (['car-wash', 'detailing', 'ceramic-coating', 'paint-protection', 'ppf', 'interior-cleaning', 'exterior-care', 'maintenance', 'steam-wash', 'monsoon-care'] as $tagSlug) {
            $tagMap[$tagSlug] = BlogTag::query()->updateOrCreate(
                ['slug' => $tagSlug],
                ['name' => Str::title(str_replace('-', ' ', $tagSlug))]
            );
        }

        $posts = require __DIR__.'/content/BlogPosts.php';

        foreach ($posts as $post) {
            $category = $categories[$post['category_slug']] ?? $categories['car-care'];

            $blogPost = BlogPost::query()->updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'blog_category_id' => $category->id,
                    'user_id' => $author?->id,
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'content' => $post['content'],
                    'status' => 'published',
                    'published_at' => $post['published_at'],
                    'meta_title' => $post['meta_title'],
                    'meta_description' => $post['meta_description'],
                    'og_title' => $post['meta_title'],
                    'og_description' => $post['meta_description'],
                ]
            );

            $tagIds = collect($post['tags'] ?? [])
                ->map(fn ($slug) => $tagMap[$slug]->id ?? null)
                ->filter()
                ->values()
                ->all();

            $blogPost->tags()->sync($tagIds);
        }
    }
}

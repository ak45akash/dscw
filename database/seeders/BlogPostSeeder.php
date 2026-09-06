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
                    'featured_image' => $post['featured_image'] ?? $this->imageForSlug($post['slug']),
                    'featured_image_alt' => $post['featured_image_alt'] ?? $post['title'],
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

    private function imageForSlug(string $slug): string
    {
        $pool = [
            'images/steam-wash.jpg',
            'images/ceramic-coating.jpg',
            'images/ppf.jpg',
            'images/exterior-detailing.jpg',
            'images/interior-detail.jpg',
            'images/interior-detailing.jpg',
            'images/full-detailing.jpg',
            'images/premium-wash.jpg',
            'images/dry-clean.jpg',
            'images/detailing.jpg',
            'images/car-wash.jpg',
            'images/facility-2.jpg',
            'images/facility-3.jpg',
        ];

        $map = [
            'why-steam-car-wash-safer-mumbai' => 'images/steam-wash.jpg',
            'ceramic-coating-guide-indian-climate' => 'images/ceramic-coating.jpg',
            'ppf-vs-ceramic-coating-comparison' => 'images/ppf.jpg',
            'monsoon-car-care-tips-mumbai' => 'images/exterior-detailing.jpg',
            'interior-steam-cleaning-benefits-process' => 'images/interior-detail.jpg',
            'ppf-maintenance-keeping-film-looking-new' => 'images/ppf.jpg',
            'paint-correction-before-ceramic-coating' => 'images/ceramic-coating.jpg',
            'engine-bay-steam-cleaning-safety-benefits' => 'images/steam-wash.jpg',
            'full-car-detailing-package-whats-included' => 'images/full-detailing.jpg',
        ];

        if (isset($map[$slug])) {
            return $map[$slug];
        }

        return $pool[crc32($slug) % count($pool)];
    }
}

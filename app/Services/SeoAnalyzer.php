<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SeoAnalyzer
{
    /**
     * @return list<array{key: string, label: string, type: string, score: int, max: int, url: ?string, edit_url: ?string, suggestions: list<string>}>
     */
    public function analyzeSite(SettingsService $settings): array
    {
        $business = $settings->getGroup('business');
        $seo = $settings->getGroup('seo');
        $city = $business['city'] ?? 'Punjab';

        $pages = [
            $this->scorePage('home', 'Homepage', [
                'title' => $seo['meta_title'] ?? '',
                'description' => $seo['meta_description'] ?? '',
                'content' => 'Diamond Steam Car Wash '.$city.' steam wash detailing ceramic coating booking',
                'has_image' => true,
                'has_h1' => true,
                'canonical' => url('/'),
                'og_title' => $seo['meta_title'] ?? '',
                'og_description' => $seo['meta_description'] ?? '',
            ], route('home'), route('admin.system.settings')),
        ];

        foreach (Page::query()->where('is_active', true)->orderBy('title')->get() as $page) {
            $publicUrl = match ($page->slug) {
                'about-us' => route('about'),
                'privacy-policy' => route('privacy'),
                'terms-and-conditions' => route('terms'),
                default => url('/'.$page->slug),
            };

            $pages[] = $this->scorePage('page:'.$page->slug, $page->title, [
                'title' => $page->meta_title ?: $page->title,
                'description' => $page->meta_description ?? '',
                'content' => strip_tags((string) $page->content),
                'has_image' => false,
                'has_h1' => true,
                'canonical' => $publicUrl,
                'og_title' => $page->meta_title ?: $page->title,
                'og_description' => $page->meta_description ?? '',
            ], $publicUrl, route('admin.pages.edit', $page));
        }

        foreach (Service::query()->active()->orderBy('name')->get() as $service) {
            $pages[] = $this->scorePage('service:'.$service->slug, 'Service: '.$service->name, [
                'title' => $service->meta_title ?: $service->name,
                'description' => $service->meta_description ?: ($service->short_description ?? ''),
                'content' => strip_tags((string) ($service->description ?? $service->short_description)),
                'has_image' => filled($service->image),
                'has_h1' => true,
                'canonical' => route('services.show', $service->slug),
                'og_title' => $service->meta_title ?: $service->name,
                'og_description' => $service->meta_description ?: ($service->short_description ?? ''),
            ], route('services.show', $service->slug), route('admin.services.edit', $service));
        }

        foreach (BlogPost::query()->published()->latest('published_at')->take(40)->get() as $post) {
            $pages[] = $this->scorePage('blog:'.$post->slug, 'Blog: '.$post->title, [
                'title' => $post->meta_title ?: $post->title,
                'description' => $post->meta_description ?: ($post->excerpt ?? ''),
                'content' => strip_tags((string) $post->content),
                'has_image' => filled($post->featured_image),
                'has_h1' => true,
                'canonical' => $post->canonical_url ?: route('blog.show', $post->slug),
                'og_title' => $post->og_title ?: ($post->meta_title ?: $post->title),
                'og_description' => $post->og_description ?: ($post->meta_description ?: ($post->excerpt ?? '')),
            ], route('blog.show', $post->slug), route('admin.blog-posts.edit', $post));
        }

        return $pages;
    }

    /**
     * @param  array{title: string, description: string, content: string, has_image: bool, has_h1: bool, canonical: string, og_title: string, og_description: string}  $data
     * @return array{key: string, label: string, type: string, score: int, max: int, url: ?string, edit_url: ?string, suggestions: list<string>}
     */
    public function scorePage(string $key, string $label, array $data, ?string $url = null, ?string $editUrl = null): array
    {
        $score = 0;
        $max = 100;
        $suggestions = [];

        $title = trim((string) ($data['title'] ?? ''));
        $titleLen = Str::length($title);
        if ($titleLen >= 30 && $titleLen <= 60) {
            $score += 20;
        } elseif ($titleLen > 0) {
            $score += 10;
            $suggestions[] = $titleLen < 30
                ? 'Meta title is short ('.$titleLen.' chars). Aim for 30–60 characters with location keywords.'
                : 'Meta title is long ('.$titleLen.' chars). Keep it under 60 characters.';
        } else {
            $suggestions[] = 'Add a unique meta title (30–60 characters).';
        }

        $description = trim((string) ($data['description'] ?? ''));
        $descLen = Str::length($description);
        if ($descLen >= 120 && $descLen <= 160) {
            $score += 20;
        } elseif ($descLen >= 70) {
            $score += 12;
            $suggestions[] = $descLen < 120
                ? 'Meta description is a bit short ('.$descLen.' chars). Aim for 120–160 characters.'
                : 'Meta description is long ('.$descLen.' chars). Trim to about 160 characters.';
        } else {
            $suggestions[] = 'Write a meta description of 120–160 characters that includes service + location.';
        }

        $content = trim((string) ($data['content'] ?? ''));
        $wordCount = str_word_count($content);
        if ($wordCount >= 300) {
            $score += 20;
        } elseif ($wordCount >= 150) {
            $score += 12;
            $suggestions[] = 'Expand page content toward 300+ words for stronger topical coverage.';
        } else {
            $suggestions[] = 'Add more useful content (target 300+ words) with clear headings.';
        }

        if (! empty($data['has_h1'])) {
            $score += 10;
        } else {
            $suggestions[] = 'Ensure the page has a single clear H1 heading.';
        }

        if (! empty($data['has_image'])) {
            $score += 10;
        } else {
            $suggestions[] = 'Add a featured/hero image with descriptive alt text.';
        }

        if (filled($data['canonical'] ?? null)) {
            $score += 10;
        } else {
            $suggestions[] = 'Set a canonical URL for this page.';
        }

        $ogTitle = trim((string) ($data['og_title'] ?? ''));
        $ogDescription = trim((string) ($data['og_description'] ?? ''));
        if ($ogTitle !== '' && $ogDescription !== '') {
            $score += 10;
        } else {
            $suggestions[] = 'Complete Open Graph title and description for social sharing.';
        }

        if ($this->mentionsLocation($title.' '.$description.' '.$content)) {
            $score += 0; // already counted via content quality; keep suggestions only when missing
        } else {
            $suggestions[] = 'Mention Punjab / Sector 66 / Matour in title or description where natural.';
        }

        if ($suggestions === [] && $score >= 90) {
            $suggestions[] = 'Looking strong — re-check rankings after the next sitemap crawl.';
        }

        return [
            'key' => $key,
            'label' => $label,
            'type' => Str::before($key, ':') ?: $key,
            'score' => min($max, $score),
            'max' => $max,
            'url' => $url,
            'edit_url' => $editUrl,
            'suggestions' => array_values(array_unique($suggestions)),
        ];
    }

    /**
     * @param  list<array{score: int}>  $pages
     */
    public function averageScore(array $pages): int
    {
        if ($pages === []) {
            return 0;
        }

        return (int) round(Collection::make($pages)->avg('score'));
    }

    private function mentionsLocation(string $text): bool
    {
        $haystack = Str::lower($text);

        foreach (['punjab', 'sector 66', 'matour', 'sahibzada', 'sas nagar', 'mohali'] as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}

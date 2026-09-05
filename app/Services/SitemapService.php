<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;

class SitemapService
{
    public function urls(): array
    {
        $urls = [
            ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => route('services.index'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => route('blog.index'), 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => route('gallery.index'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('faq.index'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('contact.index'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('booking.index'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('privacy'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('terms'), 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        foreach (Service::query()->active()->orderBy('display_order')->get(['slug', 'updated_at']) as $service) {
            $urls[] = [
                'loc' => route('services.show', $service->slug),
                'lastmod' => optional($service->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        foreach (BlogPost::query()->published()->latest('published_at')->get(['slug', 'updated_at', 'published_at']) as $post) {
            $urls[] = [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => optional($post->updated_at ?? $post->published_at)->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        return $urls;
    }

    public function toXml(): string
    {
        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($this->urls() as $url) {
            $lines[] = '  <url>';
            $lines[] = '    <loc>'.e($url['loc']).'</loc>';
            if (! empty($url['lastmod'])) {
                $lines[] = '    <lastmod>'.e($url['lastmod']).'</lastmod>';
            }
            if (! empty($url['changefreq'])) {
                $lines[] = '    <changefreq>'.e($url['changefreq']).'</changefreq>';
            }
            if (! empty($url['priority'])) {
                $lines[] = '    <priority>'.e($url['priority']).'</priority>';
            }
            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines)."\n";
    }

    public function writeCachedFile(): string
    {
        $xml = $this->toXml();
        Storage::disk('local')->put('sitemap.xml', $xml);

        return $xml;
    }

    public function cachedOrGenerate(): string
    {
        if (Storage::disk('local')->exists('sitemap.xml')) {
            return Storage::disk('local')->get('sitemap.xml');
        }

        return $this->writeCachedFile();
    }
}

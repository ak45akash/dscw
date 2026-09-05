<x-layouts.admin title="SEO" breadcrumb="Marketing / SEO">
    <div class="grid gap-6 lg:grid-cols-2">
        <x-card class="space-y-3">
            <h3 class="font-semibold">Site-wide defaults</h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-graphite-500">Meta title</dt>
                    <dd class="font-medium">{{ $seo['meta_title'] ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-graphite-500">Meta description</dt>
                    <dd class="font-medium">{{ $seo['meta_description'] ?? '—' }}</dd>
                </div>
            </dl>
            <a href="{{ route('admin.system.settings') }}" class="btn-secondary inline-flex">Edit in Cache &amp; Settings</a>
        </x-card>

        <x-card class="space-y-3">
            <h3 class="font-semibold">Crawl files</h3>
            <ul class="space-y-2 text-sm">
                <li>
                    <a href="{{ $sitemapUrl }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline">{{ $sitemapUrl }}</a>
                </li>
                <li>
                    <a href="{{ $robotsUrl }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline">{{ $robotsUrl }}</a>
                </li>
            </ul>
            <p class="text-xs text-graphite-500">Sitemap regenerates via <code>php artisan sitemap:generate</code> (scheduled daily) and on first request if missing.</p>
        </x-card>
    </div>
</x-layouts.admin>

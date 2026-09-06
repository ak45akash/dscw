<x-layouts.admin title="SEO" breadcrumb="Marketing / SEO">
    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-card>
            <p class="text-sm text-graphite-500">Average score</p>
            <p class="mt-1 text-3xl font-bold {{ $averageScore >= 80 ? 'text-emerald-600' : ($averageScore >= 60 ? 'text-amber-600' : 'text-red-600') }}">{{ $averageScore }}/100</p>
        </x-card>
        <x-card>
            <p class="text-sm text-graphite-500">Pages analysed</p>
            <p class="mt-1 text-3xl font-bold">{{ count($pages) }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-graphite-500">Needs attention (&lt; 70)</p>
            <p class="mt-1 text-3xl font-bold text-amber-600">{{ $lowScoreCount }}</p>
        </x-card>
    </div>

    <div class="mb-6 grid gap-6 lg:grid-cols-2">
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
                    <a href="{{ $sitemapUrl }}" target="_blank" rel="noopener" class="text-brand-700 hover:underline">{{ $sitemapUrl }}</a>
                </li>
                <li>
                    <a href="{{ $robotsUrl }}" target="_blank" rel="noopener" class="text-brand-700 hover:underline">{{ $robotsUrl }}</a>
                </li>
            </ul>
            <p class="text-xs text-graphite-500">Sitemap regenerates via <code>php artisan sitemap:generate</code> (scheduled daily) and on first request if missing.</p>
        </x-card>
    </div>

    <x-card>
        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h3 class="font-semibold">Page scores &amp; suggestions</h3>
                <p class="mt-1 text-sm text-graphite-500">Sorted lowest score first so you can improve weak pages manually.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-graphite-200 text-xs uppercase tracking-wide text-graphite-500">
                    <tr>
                        <th class="px-3 py-2">Page</th>
                        <th class="px-3 py-2">Score</th>
                        <th class="px-3 py-2">Suggestions</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100">
                    @foreach($pages as $page)
                        <tr>
                            <td class="px-3 py-3 align-top">
                                <div class="font-medium text-graphite-900">{{ $page['label'] }}</div>
                                <div class="text-xs text-graphite-500">{{ $page['type'] }}</div>
                            </td>
                            <td class="px-3 py-3 align-top">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-emerald-50 text-emerald-700' => $page['score'] >= 80,
                                    'bg-amber-50 text-amber-700' => $page['score'] >= 60 && $page['score'] < 80,
                                    'bg-red-50 text-red-700' => $page['score'] < 60,
                                ])>{{ $page['score'] }}/{{ $page['max'] }}</span>
                            </td>
                            <td class="px-3 py-3 align-top">
                                <ul class="list-disc space-y-1 pl-4 text-graphite-600">
                                    @foreach($page['suggestions'] as $suggestion)
                                        <li>{{ $suggestion }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-3 py-3 align-top text-right whitespace-nowrap">
                                @if($page['edit_url'])
                                    <a href="{{ $page['edit_url'] }}" class="text-brand-700 hover:underline">Edit</a>
                                @endif
                                @if($page['url'])
                                    <a href="{{ $page['url'] }}" target="_blank" rel="noopener" class="ml-3 text-graphite-500 hover:underline">View</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.admin>

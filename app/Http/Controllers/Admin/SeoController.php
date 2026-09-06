<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SeoAnalyzer;
use App\Services\SettingsService;
use Illuminate\View\View;

class SeoController extends Controller
{
    public function __construct(
        private SettingsService $settings,
        private SeoAnalyzer $analyzer,
    ) {}

    public function __invoke(): View
    {
        abort_unless(auth()->user()?->hasPermission('marketing.view'), 403);

        $pages = $this->analyzer->analyzeSite($this->settings);
        usort($pages, fn ($a, $b) => $a['score'] <=> $b['score']);

        return view('admin.seo.index', [
            'seo' => $this->settings->getGroup('seo'),
            'sitemapUrl' => url('/sitemap.xml'),
            'robotsUrl' => url('/robots.txt'),
            'pages' => $pages,
            'averageScore' => $this->analyzer->averageScore($pages),
            'lowScoreCount' => collect($pages)->where('score', '<', 70)->count(),
        ]);
    }
}

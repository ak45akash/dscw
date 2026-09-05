<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\View\View;

class SeoController extends Controller
{
    public function __construct(private SettingsService $settings) {}

    public function __invoke(): View
    {
        abort_unless(auth()->user()?->hasPermission('marketing.view'), 403);

        return view('admin.seo.index', [
            'seo' => $this->settings->getGroup('seo'),
            'sitemapUrl' => url('/sitemap.xml'),
            'robotsUrl' => url('/robots.txt'),
        ]);
    }
}

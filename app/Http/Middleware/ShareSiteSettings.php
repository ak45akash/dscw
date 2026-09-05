<?php

namespace App\Http\Middleware;

use App\Services\SettingsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareSiteSettings
{
    public function __construct(private SettingsService $settings) {}

    public function handle(Request $request, Closure $next): Response
    {
        View::share('siteSettings', $this->settings->publicSettings());

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class SystemSettingsController extends Controller
{
    public function __construct(
        private SettingsService $settings,
        private AuditLogService $auditLog,
    ) {}

    public function edit(): View
    {
        $this->authorizePermission('system.manage');

        return view('admin.system.settings', [
            'seo' => $this->settings->getGroup('seo'),
            'email' => $this->settings->getGroup('email'),
            'analytics' => $this->settings->getGroup('analytics'),
            'cacheDriver' => config('cache.default'),
            'queueConnection' => config('queue.default'),
            'appEnv' => config('app.env'),
            'appDebug' => config('app.debug'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizePermission('system.manage');

        $data = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'ga_measurement_id' => ['nullable', 'string', 'max:50'],
            'gtm_container_id' => ['nullable', 'string', 'max:50'],
        ]);

        $old = [
            'seo' => $this->settings->getGroup('seo'),
            'email' => $this->settings->getGroup('email'),
            'analytics' => $this->settings->getGroup('analytics'),
        ];

        $this->settings->setMany('seo', [
            'meta_title' => $data['meta_title'] ?? '',
            'meta_description' => $data['meta_description'] ?? '',
        ], ['meta_title', 'meta_description']);

        $this->settings->setMany('email', [
            'mail_from_name' => $data['mail_from_name'] ?? '',
            'mail_from_address' => $data['mail_from_address'] ?? '',
        ]);

        $this->settings->setMany('analytics', [
            'ga_measurement_id' => $data['ga_measurement_id'] ?? '',
            'gtm_container_id' => $data['gtm_container_id'] ?? '',
        ]);

        $this->auditLog->log(
            module: 'settings',
            action: 'system_updated',
            oldValues: $old,
            newValues: [
                'seo' => $this->settings->getGroup('seo'),
                'email' => $this->settings->getGroup('email'),
                'analytics' => $this->settings->getGroup('analytics'),
            ],
        );

        return back()->with('success', 'System settings saved.');
    }

    public function clearCache(Request $request): RedirectResponse
    {
        $this->authorizePermission('system.manage');

        $type = $request->validate([
            'type' => ['required', 'in:application,views,config,routes,all'],
        ])['type'];

        match ($type) {
            'application' => tap(null, function () {
                Artisan::call('cache:clear');
                $this->settings->clearCache();
            }),
            'views' => Artisan::call('view:clear'),
            'config' => Artisan::call('config:clear'),
            'routes' => Artisan::call('route:clear'),
            'all' => tap(null, function () {
                $this->settings->clearCache();
                Artisan::call('optimize:clear');
            }),
        };

        $this->auditLog->log(
            module: 'system',
            action: 'cache_cleared',
            newValues: ['type' => $type],
        );

        return back()->with('success', 'Cache cleared: '.$type.'.');
    }

    private function authorizePermission(string $permission): void
    {
        abort_unless(auth()->user()?->hasPermission($permission), 403);
    }
}

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
            'sms' => $this->settings->getGroup('sms'),
            'mediaDisk' => config('dscw.media_disk'),
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
            'sms_enabled' => ['sometimes', 'boolean'],
            'sms_provider' => ['nullable', 'in:null,msg91,twilio'],
            'sms_api_key' => ['nullable', 'string', 'max:255'],
            'sms_account_sid' => ['nullable', 'string', 'max:255'],
            'sms_sender_id' => ['nullable', 'string', 'max:20'],
            'sms_from_number' => ['nullable', 'string', 'max:30'],
            'sms_template_id' => ['nullable', 'string', 'max:100'],
            'sms_confirmations_enabled' => ['sometimes', 'boolean'],
            'sms_reminders_enabled' => ['sometimes', 'boolean'],
            'sms_reminder_hours_before' => ['nullable', 'integer', 'min:1', 'max:168'],
            'sms_confirmation_template' => ['nullable', 'string', 'max:500'],
            'sms_reminder_template' => ['nullable', 'string', 'max:500'],
        ]);

        $old = [
            'seo' => $this->settings->getGroup('seo'),
            'email' => $this->settings->getGroup('email'),
            'analytics' => $this->settings->getGroup('analytics'),
            'sms' => $this->settings->getGroup('sms'),
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

        $this->settings->setMany('sms', [
            'enabled' => ['value' => $request->boolean('sms_enabled'), 'type' => 'boolean'],
            'provider' => $data['sms_provider'] ?? 'null',
            'api_key' => $data['sms_api_key'] ?? '',
            'account_sid' => $data['sms_account_sid'] ?? '',
            'sender_id' => $data['sms_sender_id'] ?? 'DSCW',
            'from_number' => $data['sms_from_number'] ?? '',
            'template_id' => $data['sms_template_id'] ?? '',
            'confirmations_enabled' => ['value' => $request->boolean('sms_confirmations_enabled'), 'type' => 'boolean'],
            'reminders_enabled' => ['value' => $request->boolean('sms_reminders_enabled'), 'type' => 'boolean'],
            'reminder_hours_before' => ['value' => (int) ($data['sms_reminder_hours_before'] ?? 24), 'type' => 'integer'],
            'confirmation_template' => $data['sms_confirmation_template'] ?? '',
            'reminder_template' => $data['sms_reminder_template'] ?? '',
        ]);

        $this->auditLog->log(
            module: 'settings',
            action: 'system_updated',
            oldValues: $old,
            newValues: [
                'seo' => $this->settings->getGroup('seo'),
                'email' => $this->settings->getGroup('email'),
                'analytics' => $this->settings->getGroup('analytics'),
                'sms' => $this->settings->getGroup('sms'),
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

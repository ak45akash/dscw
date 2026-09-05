<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBusinessSettingsRequest;
use App\Services\AuditLogService;
use App\Services\SettingsService;
use App\Support\AdminNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BusinessSettingsController extends Controller
{
    public function __construct(
        private SettingsService $settings,
        private AuditLogService $auditLog,
    ) {}

    public function edit(): View
    {
        $business = $this->settings->getGroup('business');
        $theme = $this->settings->getGroup('theme');

        return view('admin.settings.business', [
            'navigation' => AdminNavigation::visibleForUser(auth()->user()),
            'settings' => array_merge($business, $theme),
        ]);
    }

    public function update(UpdateBusinessSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $oldBusiness = $this->settings->getGroup('business');
        $oldTheme = $this->settings->getGroup('theme');

        $businessKeys = ['business_name', 'tagline', 'phone', 'email', 'address', 'currency', 'timezone', 'facebook_url', 'instagram_url', 'youtube_url', 'whatsapp_number'];
        $businessData = collect($validated)->only($businessKeys)->all();

        $this->settings->setMany('business', $businessData, [
            'business_name', 'tagline', 'phone', 'email', 'address', 'currency', 'timezone',
            'facebook_url', 'instagram_url', 'youtube_url', 'whatsapp_number',
        ]);

        $this->settings->set('theme', 'mode', $validated['theme_mode'], 'string', true);

        $this->auditLog->log(
            module: 'settings',
            action: 'updated',
            oldValues: array_merge($oldBusiness, $oldTheme),
            newValues: array_merge($businessData, ['theme_mode' => $validated['theme_mode']]),
        );

        return redirect()
            ->route('admin.settings.business')
            ->with('success', 'Business settings updated successfully.');
    }
}

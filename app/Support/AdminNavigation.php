<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class AdminNavigation
{
    public static function groups(): array
    {
        return [
            [
                'label' => 'Dashboard',
                'items' => [
                    ['label' => 'Overview', 'route' => 'admin.dashboard', 'permission' => 'dashboard.view'],
                    ['label' => "Today's Bookings", 'route' => 'admin.bookings.today', 'permission' => 'bookings.view'],
                    ['label' => 'Booking Calendar', 'route' => 'admin.dashboard', 'permission' => 'bookings.view', 'badge' => 'soon'],
                ],
            ],
            [
                'label' => 'Bookings',
                'items' => [
                    ['label' => 'All Bookings', 'route' => 'admin.bookings.index', 'permission' => 'bookings.view'],
                    ['label' => 'Blocked Dates', 'route' => 'admin.blocked-dates.index', 'permission' => 'bookings.manage'],
                    ['label' => 'Booking Rules', 'route' => 'admin.settings.booking', 'permission' => 'bookings.manage'],
                ],
            ],
            [
                'label' => 'Services',
                'items' => [
                    ['label' => 'Services', 'route' => 'admin.services.index', 'permission' => 'services.view'],
                    ['label' => 'Categories', 'route' => 'admin.service-categories.index', 'permission' => 'services.view'],
                    ['label' => 'Add-ons', 'route' => 'admin.dashboard', 'permission' => 'services.manage', 'badge' => 'soon'],
                ],
            ],
            [
                'label' => 'Content',
                'items' => [
                    ['label' => 'Pages', 'route' => 'admin.dashboard', 'permission' => 'content.view', 'badge' => 'soon'],
                    ['label' => 'Blog Posts', 'route' => 'admin.dashboard', 'permission' => 'content.view', 'badge' => 'soon'],
                    ['label' => 'Media Library', 'route' => 'admin.dashboard', 'permission' => 'content.manage', 'badge' => 'soon'],
                    ['label' => 'Testimonials', 'route' => 'admin.dashboard', 'permission' => 'content.view', 'badge' => 'soon'],
                    ['label' => 'Gallery', 'route' => 'admin.dashboard', 'permission' => 'content.view', 'badge' => 'soon'],
                    ['label' => 'FAQs', 'route' => 'admin.dashboard', 'permission' => 'content.view', 'badge' => 'soon'],
                ],
            ],
            [
                'label' => 'Marketing',
                'items' => [
                    ['label' => 'Coupons', 'route' => 'admin.dashboard', 'permission' => 'marketing.view', 'badge' => 'soon'],
                    ['label' => 'SEO', 'route' => 'admin.dashboard', 'permission' => 'marketing.view', 'badge' => 'soon'],
                ],
            ],
            [
                'label' => 'Business',
                'items' => [
                    ['label' => 'Business Settings', 'route' => 'admin.settings.business', 'permission' => 'business.manage'],
                    ['label' => 'Locations', 'route' => 'admin.locations.index', 'permission' => 'business.manage'],
                ],
            ],
            [
                'label' => 'Reports',
                'items' => [
                    ['label' => 'Booking Reports', 'route' => 'admin.dashboard', 'permission' => 'reports.view', 'badge' => 'soon'],
                    ['label' => 'Revenue', 'route' => 'admin.dashboard', 'permission' => 'reports.view', 'badge' => 'soon'],
                ],
            ],
            [
                'label' => 'System',
                'items' => [
                    ['label' => 'Users & Admins', 'route' => 'admin.dashboard', 'permission' => 'system.manage', 'badge' => 'soon'],
                    ['label' => 'Audit Logs', 'route' => 'admin.dashboard', 'permission' => 'system.view', 'badge' => 'soon'],
                    ['label' => 'Cache & Settings', 'route' => 'admin.dashboard', 'permission' => 'system.manage', 'badge' => 'soon'],
                ],
            ],
        ];
    }

    public static function itemIsActive(array $item, ?string $currentRoute = null): bool
    {
        if (! empty($item['badge'])) {
            return false;
        }

        $routeName = $item['route'] ?? null;
        if (! $routeName || ! Route::has($routeName)) {
            return false;
        }

        $pattern = $item['active'] ?? $routeName;

        if ($currentRoute === null) {
            return request()->routeIs($pattern);
        }

        foreach ((array) $pattern as $candidate) {
            if ($currentRoute === $candidate || str($currentRoute)->is($candidate)) {
                return true;
            }
        }

        return false;
    }

    public static function groupContainsActiveItem(array $group, ?string $currentRoute = null): bool
    {
        foreach ($group['items'] as $item) {
            if (self::itemIsActive($item, $currentRoute)) {
                return true;
            }
        }

        return false;
    }

    public static function visibleForUser($user): array
    {
        return collect(self::groups())
            ->map(function (array $group) use ($user) {
                $items = collect($group['items'])
                    ->filter(fn (array $item) => $user->hasPermission($item['permission']))
                    ->values()
                    ->all();

                if (empty($items)) {
                    return null;
                }

                return [
                    'label' => $group['label'],
                    'items' => $items,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}

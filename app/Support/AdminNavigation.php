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
                    ['label' => 'Booking Calendar', 'route' => 'admin.bookings.calendar', 'permission' => 'bookings.view', 'active' => 'admin.bookings.calendar'],
                ],
            ],
            [
                'label' => 'Bookings',
                'items' => [
                    ['label' => 'All Bookings', 'route' => 'admin.bookings.index', 'permission' => 'bookings.view', 'active' => ['admin.bookings.index', 'admin.bookings.show']],
                    ['label' => 'Blocked Dates', 'route' => 'admin.blocked-dates.index', 'permission' => 'bookings.manage'],
                    ['label' => 'Booking Rules', 'route' => 'admin.settings.booking', 'permission' => 'bookings.manage'],
                ],
            ],
            [
                'label' => 'Services',
                'items' => [
                    ['label' => 'Services', 'route' => 'admin.services.index', 'permission' => 'services.view'],
                    ['label' => 'Categories', 'route' => 'admin.service-categories.index', 'permission' => 'services.view'],
                    ['label' => 'Add-ons', 'route' => 'admin.service-addons.index', 'permission' => 'services.manage', 'active' => ['admin.service-addons.*']],
                ],
            ],
            [
                'label' => 'Content',
                'items' => [
                    ['label' => 'Pages', 'route' => 'admin.pages.index', 'permission' => 'content.view'],
                    ['label' => 'Posts', 'route' => 'admin.blog-posts.index', 'permission' => 'content.view', 'active' => ['admin.blog-posts.*']],
                    ['label' => 'Categories & Tags', 'route' => 'admin.blog-taxonomies.index', 'permission' => 'content.manage'],
                    ['label' => 'Media Library', 'route' => 'admin.media.index', 'permission' => 'content.manage', 'active' => ['admin.media.*']],
                    ['label' => 'Testimonials', 'route' => 'admin.testimonials.index', 'permission' => 'content.view'],
                    ['label' => 'Gallery', 'route' => 'admin.gallery-items.index', 'permission' => 'content.view'],
                    ['label' => 'FAQs', 'route' => 'admin.faqs.index', 'permission' => 'content.view'],
                    ['label' => 'Enquiries', 'route' => 'admin.enquiries.index', 'permission' => 'content.view', 'active' => ['admin.enquiries.index', 'admin.enquiries.show']],
                ],
            ],
            [
                'label' => 'Marketing',
                'items' => [
                    ['label' => 'Coupons', 'route' => 'admin.coupons.index', 'permission' => 'marketing.view'],
                    ['label' => 'SEO', 'route' => 'admin.seo.index', 'permission' => 'marketing.view'],
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
                    ['label' => 'Booking Reports', 'route' => 'admin.reports.bookings', 'permission' => 'reports.view'],
                    ['label' => 'Revenue', 'route' => 'admin.reports.revenue', 'permission' => 'reports.view'],
                ],
            ],
            [
                'label' => 'System',
                'items' => [
                    ['label' => 'Users & Admins', 'route' => 'admin.users.index', 'permission' => 'system.manage', 'active' => ['admin.users.*']],
                    ['label' => 'Audit Logs', 'route' => 'admin.audit-logs.index', 'permission' => 'system.view', 'active' => ['admin.audit-logs.*']],
                    ['label' => 'Cache & Settings', 'route' => 'admin.system.settings', 'permission' => 'system.manage'],
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

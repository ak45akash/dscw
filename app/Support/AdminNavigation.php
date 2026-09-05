<?php

namespace App\Support;

class AdminNavigation
{
    public static function groups(): array
    {
        return [
            [
                'label' => 'Dashboard',
                'items' => [
                    ['label' => 'Overview', 'route' => 'admin.dashboard', 'permission' => 'dashboard.view'],
                    ['label' => "Today's Bookings", 'route' => 'admin.dashboard', 'permission' => 'bookings.view', 'badge' => 'soon'],
                    ['label' => 'Booking Calendar', 'route' => 'admin.dashboard', 'permission' => 'bookings.view', 'badge' => 'soon'],
                ],
            ],
            [
                'label' => 'Bookings',
                'items' => [
                    ['label' => 'All Bookings', 'route' => 'admin.dashboard', 'permission' => 'bookings.view', 'badge' => 'soon'],
                    ['label' => 'Blocked Dates', 'route' => 'admin.dashboard', 'permission' => 'bookings.manage', 'badge' => 'soon'],
                    ['label' => 'Booking Rules', 'route' => 'admin.dashboard', 'permission' => 'bookings.manage', 'badge' => 'soon'],
                ],
            ],
            [
                'label' => 'Services',
                'items' => [
                    ['label' => 'Services', 'route' => 'admin.dashboard', 'permission' => 'services.view', 'badge' => 'soon'],
                    ['label' => 'Categories', 'route' => 'admin.dashboard', 'permission' => 'services.view', 'badge' => 'soon'],
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
                    ['label' => 'Locations', 'route' => 'admin.dashboard', 'permission' => 'business.manage', 'badge' => 'soon'],
                    ['label' => 'Working Hours', 'route' => 'admin.dashboard', 'permission' => 'business.manage', 'badge' => 'soon'],
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

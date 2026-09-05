<?php

namespace Tests\Unit;

use App\Support\AdminNavigation;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    public function test_groups_include_expected_top_level_categories(): void
    {
        $labels = collect(AdminNavigation::groups())->pluck('label')->all();

        $this->assertSame([
            'Dashboard',
            'Bookings',
            'Services',
            'Content',
            'Marketing',
            'Business',
            'Reports',
            'System',
        ], $labels);
    }

    public function test_placeholder_items_are_never_active(): void
    {
        $item = [
            'label' => 'Booking Calendar',
            'route' => 'admin.dashboard',
            'permission' => 'bookings.view',
            'badge' => 'soon',
        ];

        $this->assertFalse(AdminNavigation::itemIsActive($item, 'admin.dashboard'));
    }

    public function test_group_contains_active_item_for_matching_route(): void
    {
        $bookings = collect(AdminNavigation::groups())
            ->firstWhere('label', 'Bookings');

        $this->assertNotNull($bookings);
        $this->assertTrue(AdminNavigation::groupContainsActiveItem($bookings, 'admin.bookings.index'));
        $this->assertFalse(AdminNavigation::groupContainsActiveItem($bookings, 'admin.services.index'));
    }
}

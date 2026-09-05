<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUiPolishTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(DatabaseSeeder::class);

        return User::query()->where('email', 'admin@diamondsteamcarwash.com')->firstOrFail();
    }

    public function test_booking_rules_page_shows_helper_text(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.settings.booking'))
            ->assertOk()
            ->assertSee('Fallback only for new services', false)
            ->assertSee('Spacing between offered start times', false)
            ->assertSee('Hard cap on total appointments', false)
            ->assertSee('Shows “Pay online” on Book Now', false);
    }

    public function test_admin_sidebar_groups_are_collapsible_and_collapsed_by_default(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $response
            ->assertSee('x-data="adminNavGroup(', false)
            ->assertSee('data-nav-group="bookings"', false)
            ->assertSee('data-nav-default-open="0"', false)
            ->assertSee('id="nav-group-toggle-bookings"', false)
            ->assertSee('id="nav-group-bookings"', false);

        // Active Dashboard group should start open so Overview stays visible.
        $response
            ->assertSee('data-nav-group="dashboard"', false)
            ->assertSee('data-nav-default-open="1"', false);
    }

    public function test_admin_sidebar_has_themed_scroll_and_global_toggle(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('admin-sidebar-scroll', false)
            ->assertSee('adminSidebar()', false)
            ->assertSee('data-sidebar-toggle', false)
            ->assertSee('aria-label="Toggle sidebar"', false)
            ->assertSee('aria-label="Collapse sidebar"', false)
            ->assertSee('desktopCollapsed', false);
    }

    public function test_admin_pages_render_native_date_inputs(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.bookings.index'))
            ->assertOk()
            ->assertSee('type="date"', false);

        $this->actingAs($admin)
            ->get(route('admin.blocked-dates.create'))
            ->assertOk()
            ->assertSee('type="date"', false);
    }

    public function test_public_booking_page_includes_date_input(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('booking.index'))
            ->assertOk()
            ->assertSee('type="date"', false)
            ->assertSee('form-input', false)
            ->assertSee('booking-form-surface', false);
    }
}

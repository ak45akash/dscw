<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SettingsService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('home'))->assertOk()->assertSee('Diamond Steam Car Wash');
    }

    public function test_guest_visiting_admin_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post(route('admin.login.store'), [
            'email' => 'admin@diamondsteamcarwash.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_settings_service_returns_seeded_values(): void
    {
        $this->seed(DatabaseSeeder::class);

        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        $this->assertSame('Diamond Steam Car Wash', $settings->get('business', 'business_name'));
    }
}

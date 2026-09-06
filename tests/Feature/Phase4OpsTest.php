<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class Phase4OpsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function admin(): User
    {
        return User::query()->where('email', 'admin@diamondsteamcarwash.com')->firstOrFail();
    }

    private function managerWithoutSystem(): User
    {
        $role = Role::query()->where('name', 'admin')->firstOrFail();

        $user = User::query()->create([
            'name' => 'Manager',
            'email' => 'manager@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        $user->roles()->sync([$role->id]);

        return $user;
    }

    public function test_guest_cannot_access_phase4_admin_routes(): void
    {
        $this->get(route('admin.audit-logs.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.users.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.system.settings'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.reports.bookings'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.reports.revenue'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.seo.index'))->assertRedirect(route('admin.login'));
    }

    public function test_manager_without_system_permission_is_forbidden(): void
    {
        $user = $this->managerWithoutSystem();

        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.audit-logs.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.system.settings'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.reports.bookings'))->assertOk();
    }

    public function test_admin_can_view_audit_logs(): void
    {
        $admin = $this->admin();

        $log = AuditLog::query()->create([
            'user_id' => $admin->id,
            'module' => 'settings',
            'action' => 'updated',
            'old_values' => ['a' => 1],
            'new_values' => ['a' => 2],
            'ip_address' => '127.0.0.1',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index'))
            ->assertOk()
            ->assertSee('settings');

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.show', $log))
            ->assertOk()
            ->assertSee('updated');
    }

    public function test_admin_can_create_user_with_roles(): void
    {
        $admin = $this->admin();
        $role = Role::query()->where('name', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Ops Staff',
                'email' => 'ops@example.com',
                'password' => 'Password1!',
                'password_confirmation' => 'Password1!',
                'is_active' => 1,
                'role_ids' => [$role->id],
            ])
            ->assertRedirect(route('admin.users.index'));

        $user = User::query()->where('email', 'ops@example.com')->firstOrFail();
        $this->assertTrue($user->roles->contains('id', $role->id));
    }

    public function test_cannot_delete_own_account_or_last_super_admin(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_update_system_settings_and_clear_cache(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('admin.system.settings.update'), [
                'meta_title' => 'DSCW SEO',
                'meta_description' => 'Steam car wash Punjab',
                'mail_from_name' => 'DSCW',
                'mail_from_address' => 'hello@example.com',
                'ga_measurement_id' => 'G-TEST123',
                'gtm_container_id' => 'GTM-TEST',
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.system.cache.clear'), ['type' => 'application'])
            ->assertRedirect();
    }

    public function test_sitemap_and_robots_are_public(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<urlset', false);

        $this->get(route('robots'))
            ->assertOk()
            ->assertSee('Sitemap:')
            ->assertSee('Disallow: /admin');
    }

    public function test_sitemap_generate_command_writes_cache(): void
    {
        Artisan::call('sitemap:generate');
        $this->assertStringContainsString('Sitemap generated', Artisan::output());

        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists('sitemap.xml'));
    }

    public function test_booking_and_revenue_reports_with_csv(): void
    {
        $admin = $this->admin();
        $location = Location::query()->firstOrFail();
        $service = Service::query()->active()->firstOrFail();

        Booking::query()->create([
            'reference' => 'RPT-PAID-1',
            'location_id' => $location->id,
            'service_id' => $service->id,
            'customer_name' => 'Paid Customer',
            'customer_email' => 'paid@example.com',
            'customer_phone' => '+919999999991',
            'vehicle_make_model' => 'City',
            'booking_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration_minutes' => 60,
            'price' => 1500,
            'status' => Booking::STATUS_CONFIRMED,
            'payment_method' => Booking::PAYMENT_ONLINE,
            'payment_status' => Booking::PAYMENT_PAID,
        ]);

        Booking::query()->create([
            'reference' => 'RPT-LOC-1',
            'location_id' => $location->id,
            'service_id' => $service->id,
            'customer_name' => 'Location Pay',
            'customer_email' => 'loc@example.com',
            'customer_phone' => '+919999999992',
            'vehicle_make_model' => 'Swift',
            'booking_date' => now()->toDateString(),
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
            'duration_minutes' => 60,
            'price' => 800,
            'status' => Booking::STATUS_CONFIRMED,
            'payment_method' => Booking::PAYMENT_AT_LOCATION,
            'payment_status' => Booking::PAYMENT_UNPAID,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports.bookings', [
                'from' => now()->startOfMonth()->toDateString(),
                'to' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('RPT-PAID-1')
            ->assertSee('RPT-LOC-1');

        $this->actingAs($admin)
            ->get(route('admin.reports.bookings', [
                'from' => now()->startOfMonth()->toDateString(),
                'to' => now()->toDateString(),
                'export' => 1,
            ]))
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->actingAs($admin)
            ->get(route('admin.reports.revenue', [
                'from' => now()->startOfMonth()->toDateString(),
                'to' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('1,500')
            ->assertSee('800');

        $this->actingAs($admin)
            ->get(route('admin.seo.index'))
            ->assertOk()
            ->assertSee('/sitemap.xml')
            ->assertSee('Page scores');
    }

    public function test_dashboard_shows_phase_4_ops_complete(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Phase 4 Complete', false)
            ->assertSee('Add-ons, media library', false);
    }
}

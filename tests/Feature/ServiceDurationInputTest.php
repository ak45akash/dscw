<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceDurationInputTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(DatabaseSeeder::class);

        return User::query()->where('email', 'admin@diamondsteamcarwash.com')->firstOrFail();
    }

    public function test_service_form_shows_days_hours_minutes_fields(): void
    {
        $admin = $this->admin();
        $service = Service::query()->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.services.edit', $service))
            ->assertOk()
            ->assertSee('durationPicker(', false)
            ->assertSee('name="duration_days"', false)
            ->assertSee('name="duration_hours"', false)
            ->assertSee('name="duration_part_minutes"', false)
            ->assertSee('name="duration_minutes"', false);
    }

    public function test_service_can_be_saved_with_days_hours_and_minutes(): void
    {
        $admin = $this->admin();
        $service = Service::query()->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.services.update', $service), [
                'name' => $service->name,
                'short_description' => $service->short_description,
                'description' => $service->description,
                'price' => $service->price,
                'duration_days' => 1,
                'duration_hours' => 2,
                'duration_part_minutes' => 15,
                'is_active' => 1,
                'display_order' => $service->display_order,
            ])
            ->assertRedirect(route('admin.services.index'));

        $this->assertSame(1575, $service->fresh()->duration_minutes);
    }

    public function test_service_duration_rejects_too_short_values(): void
    {
        $admin = $this->admin();
        $service = Service::query()->firstOrFail();

        $this->actingAs($admin)
            ->from(route('admin.services.edit', $service))
            ->put(route('admin.services.update', $service), [
                'name' => $service->name,
                'short_description' => $service->short_description,
                'description' => $service->description,
                'price' => $service->price,
                'duration_days' => 0,
                'duration_hours' => 0,
                'duration_part_minutes' => 5,
                'is_active' => 1,
                'display_order' => $service->display_order,
            ])
            ->assertRedirect(route('admin.services.edit', $service))
            ->assertSessionHasErrors('duration_minutes');
    }
}

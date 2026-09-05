<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(DatabaseSeeder::class);

        return User::query()->where('email', 'admin@diamondsteamcarwash.com')->firstOrFail();
    }

    public function test_service_form_includes_image_upload_field(): void
    {
        $admin = $this->admin();
        $service = Service::query()->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.services.edit', $service))
            ->assertOk()
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="image"', false)
            ->assertSee('Service image', false);
    }

    public function test_admin_can_upload_service_image_and_frontend_uses_it(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $service = Service::query()->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.services.update', $service), [
                'name' => $service->name,
                'short_description' => $service->short_description,
                'description' => $service->description,
                'price' => $service->price,
                'duration_days' => 0,
                'duration_hours' => 1,
                'duration_part_minutes' => 0,
                'is_active' => 1,
                'display_order' => $service->display_order,
                'image' => UploadedFile::fake()->image('premium.jpg', 800, 600),
            ])
            ->assertRedirect(route('admin.services.index'));

        $service->refresh();
        $this->assertNotNull($service->image);
        $this->assertTrue(str_starts_with($service->image, 'services/'));
        Storage::disk('public')->assertExists($service->image);

        $this->get(route('services.show', $service->slug))
            ->assertOk()
            ->assertSee($service->imageUrl(), false);

        $this->get(route('services.index'))
            ->assertOk()
            ->assertSee($service->imageUrl(), false);
    }

    public function test_admin_can_remove_uploaded_service_image(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $service = Service::query()->firstOrFail();

        $path = 'services/2026/09/keep-me.jpg';
        Storage::disk('public')->put($path, 'fake-image');
        $service->update(['image' => $path]);

        $this->actingAs($admin)
            ->put(route('admin.services.update', $service), [
                'name' => $service->name,
                'short_description' => $service->short_description,
                'description' => $service->description,
                'price' => $service->price,
                'duration_days' => 0,
                'duration_hours' => 1,
                'duration_part_minutes' => 0,
                'is_active' => 1,
                'display_order' => $service->display_order,
                'remove_image' => 1,
            ])
            ->assertRedirect(route('admin.services.index'));

        $this->assertNull($service->fresh()->image);
        Storage::disk('public')->assertMissing($path);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EditorUploadTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(DatabaseSeeder::class);

        return User::query()->where('email', 'admin@diamondsteamcarwash.com')->firstOrFail();
    }

    public function test_admin_can_upload_multiple_editor_images(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $response = $this->actingAs($admin)->postJson(route('admin.editor.uploads.store'), [
            'images' => [
                UploadedFile::fake()->image('one.jpg'),
                UploadedFile::fake()->image('two.png'),
            ],
        ]);

        $response->assertOk()
            ->assertJsonStructure(['urls', 'files']);

        $this->assertCount(2, $response->json('urls'));
        Storage::disk('public')->assertExists(str_replace('/storage/', '', parse_url($response->json('urls.0'), PHP_URL_PATH)));
    }

    public function test_editor_single_file_upload_returns_location(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $response = $this->actingAs($admin)->postJson(route('admin.editor.uploads.store'), [
            'file' => UploadedFile::fake()->image('paste.webp'),
        ]);

        $response->assertOk()->assertJsonStructure(['location', 'url']);
        $this->assertNotEmpty($response->json('location'));
    }

    public function test_guest_cannot_upload_editor_images(): void
    {
        $this->postJson(route('admin.editor.uploads.store'), [
            'file' => UploadedFile::fake()->image('x.jpg'),
        ])->assertUnauthorized();
    }

    public function test_service_form_includes_quill_wysiwyg_markup(): void
    {
        $admin = $this->admin();
        $service = Service::query()->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.services.edit', $service))
            ->assertOk()
            ->assertSee('wysiwygEditor(', false)
            ->assertSee('wysiwyg-field', false)
            ->assertSee('data-wysiwyg-engine="quill"', false)
            ->assertSee('data-quill-host', false)
            ->assertSee('image button accepts multiple files', false);
    }

    public function test_service_description_is_sanitized_on_save(): void
    {
        $admin = $this->admin();
        $service = Service::query()->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.services.update', $service), [
                'name' => $service->name,
                'short_description' => $service->short_description,
                'description' => '<p>Clean <script>alert(1)</script><strong>steam</strong></p>',
                'price' => $service->price,
                'duration_minutes' => $service->duration_minutes,
                'is_active' => 1,
                'display_order' => $service->display_order,
            ])
            ->assertRedirect(route('admin.services.index'));

        $service->refresh();
        $this->assertStringContainsString('<strong>steam</strong>', $service->description);
        $this->assertStringNotContainsString('<script', $service->description);
    }
}

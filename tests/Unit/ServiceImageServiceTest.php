<?php

namespace Tests\Unit;

use App\Models\Service;
use App\Services\ServiceImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class ServiceImageServiceTest extends TestCase
{
    public function test_it_stores_service_images_on_the_public_disk(): void
    {
        Storage::fake('public');

        $path = app(ServiceImageService::class)->store(
            UploadedFile::fake()->image('service.jpg')
        );

        $this->assertTrue(str_starts_with($path, 'services/'));
        Storage::disk('public')->assertExists($path);
    }

    public function test_it_rejects_non_images(): void
    {
        Storage::fake('public');

        $this->expectException(InvalidArgumentException::class);

        app(ServiceImageService::class)->store(
            UploadedFile::fake()->create('notes.txt', 10, 'text/plain')
        );
    }

    public function test_service_image_url_uses_storage_for_uploaded_paths(): void
    {
        $service = new Service([
            'slug' => 'basic-wash',
            'image' => 'services/2026/09/example.jpg',
        ]);

        $this->assertStringContainsString('/storage/services/2026/09/example.jpg', $service->imageUrl());
    }

    public function test_service_falls_back_to_default_image_when_none_uploaded(): void
    {
        $service = new Service([
            'slug' => 'basic-wash',
            'image' => null,
        ]);

        $this->assertStringContainsString('images/car-wash.jpg', $service->imageUrl());
    }
}

<?php

namespace Tests\Unit;

use App\Services\EditorUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class EditorUploadServiceTest extends TestCase
{
    public function test_it_stores_an_image_on_the_public_disk(): void
    {
        Storage::fake('public');

        $stored = app(EditorUploadService::class)->store(
            UploadedFile::fake()->image('wash.jpg')
        );

        $this->assertArrayHasKey('url', $stored);
        $this->assertArrayHasKey('path', $stored);
        Storage::disk('public')->assertExists($stored['path']);
    }

    public function test_it_rejects_non_image_files(): void
    {
        Storage::fake('public');

        $this->expectException(InvalidArgumentException::class);

        app(EditorUploadService::class)->store(
            UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf')
        );
    }
}

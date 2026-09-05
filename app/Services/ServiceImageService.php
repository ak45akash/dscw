<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class ServiceImageService
{
    public function __construct(private MediaLibraryService $media) {}

    /**
     * Store an uploaded image on the configured media disk.
     *
     * @return string Relative path (e.g. services/2026/09/uuid.jpg)
     */
    public function store(UploadedFile $file, string $folder = 'services'): string
    {
        try {
            return $this->media->storeFile($file, $folder);
        } catch (InvalidArgumentException $e) {
            throw $e;
        }
    }

    public function delete(?string $path): void
    {
        $this->media->delete($path);
    }

    public function isManagedPath(?string $path): bool
    {
        return $this->media->isManagedPath($path);
    }

    public function url(?string $path, ?string $fallback = null): ?string
    {
        return $this->media->url($path, $fallback);
    }
}

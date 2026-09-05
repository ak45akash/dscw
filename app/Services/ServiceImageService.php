<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ServiceImageService
{
    /**
     * @var list<string>
     */
    private array $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Store an uploaded service image on the public disk.
     *
     * @return string Relative path on the public disk (e.g. services/2026/09/uuid.jpg)
     */
    public function store(UploadedFile $file): string
    {
        if (! in_array($file->getMimeType(), $this->allowedMimes, true)) {
            throw new InvalidArgumentException('Only JPEG, PNG, GIF, and WebP images are allowed.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new InvalidArgumentException('Each image must be 5MB or smaller.');
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = Str::uuid()->toString().'.'.$extension;

        return $file->storeAs('services/'.now()->format('Y/m'), $filename, 'public');
    }

    public function delete(?string $path): void
    {
        if (! $path || ! $this->isManagedPath($path)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    public function isManagedPath(?string $path): bool
    {
        return is_string($path) && str_starts_with($path, 'services/');
    }
}

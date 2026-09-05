<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MediaLibraryService
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

    public function disk(): string
    {
        return MediaDisk::name();
    }

    /**
     * @return string Relative path on the media disk
     */
    public function storeFile(UploadedFile $file, string $folder = 'media'): string
    {
        if (! in_array($file->getMimeType(), $this->allowedMimes, true)) {
            throw new InvalidArgumentException('Only JPEG, PNG, GIF, and WebP images are allowed.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new InvalidArgumentException('Each image must be 5MB or smaller.');
        }

        $folder = trim($folder, '/');
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = Str::uuid()->toString().'.'.$extension;

        return $file->storeAs($folder.'/'.now()->format('Y/m'), $filename, $this->disk());
    }

    public function storeAsMedia(UploadedFile $file, string $folder = 'media', ?string $alt = null, ?int $userId = null): Media
    {
        $path = $this->storeFile($file, $folder);

        return Media::query()->create([
            'user_id' => $userId,
            'disk' => $this->disk(),
            'path' => $path,
            'filename' => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => (int) $file->getSize(),
            'alt' => $alt,
            'folder' => trim($folder, '/'),
        ]);
    }

    public function delete(?string $path, ?string $disk = null): void
    {
        if (! $path || ! $this->isManagedPath($path)) {
            return;
        }

        Storage::disk($disk ?: $this->disk())->delete($path);
    }

    public function deleteMedia(Media $media): void
    {
        $this->delete($media->path, $media->disk);
        $media->delete();
    }

    public function isManagedPath(?string $path): bool
    {
        if (! is_string($path)) {
            return false;
        }

        foreach (['services/', 'gallery/', 'blog/', 'content/', 'editor/', 'media/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public function url(?string $path, ?string $fallback = null, ?string $disk = null): ?string
    {
        if (! $path) {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if ($this->isManagedPath($path)) {
            return Storage::disk($disk ?: $this->disk())->url($path);
        }

        return asset($path);
    }
}

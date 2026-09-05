<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class EditorUploadService
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
     * @return array{url: string, path: string, name: string}
     */
    public function store(UploadedFile $file): array
    {
        if (! in_array($file->getMimeType(), $this->allowedMimes, true)) {
            throw new InvalidArgumentException('Only JPEG, PNG, GIF, and WebP images are allowed.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new InvalidArgumentException('Each image must be 5MB or smaller.');
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = Str::uuid()->toString().'.'.$extension;
        $path = $file->storeAs('editor/'.now()->format('Y/m'), $filename, 'public');

        return [
            'url' => Storage::disk('public')->url($path),
            'path' => $path,
            'name' => $file->getClientOriginalName(),
        ];
    }

    /**
     * @param  list<UploadedFile>  $files
     * @return list<array{url: string, path: string, name: string}>
     */
    public function storeMany(array $files): array
    {
        return array_map(fn (UploadedFile $file) => $this->store($file), array_values($files));
    }
}

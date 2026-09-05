<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class EditorUploadService
{
    public function __construct(private MediaLibraryService $media) {}

    /**
     * @return array{url: string, path: string, name: string}
     */
    public function store(UploadedFile $file): array
    {
        try {
            $path = $this->media->storeFile($file, 'editor');
        } catch (InvalidArgumentException $e) {
            throw $e;
        }

        return [
            'url' => Storage::disk($this->media->disk())->url($path),
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

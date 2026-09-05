<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EditorUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class EditorUploadController extends Controller
{
    public function __construct(private EditorUploadService $uploads) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['nullable', 'file', 'max:5120'],
            'images' => ['nullable', 'array', 'max:12'],
            'images.*' => ['file', 'max:5120'],
        ]);

        try {
            if ($request->hasFile('images')) {
                $stored = $this->uploads->storeMany($request->file('images', []));

                return response()->json([
                    'urls' => array_column($stored, 'url'),
                    'files' => $stored,
                ]);
            }

            if ($request->hasFile('file')) {
                $stored = $this->uploads->store($request->file('file'));

                return response()->json([
                    'location' => $stored['url'],
                    'url' => $stored['url'],
                ]);
            }
        } catch (InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json(['message' => 'No images were uploaded.'], 422);
    }
}

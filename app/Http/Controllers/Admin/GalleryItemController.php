<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Services\ServiceImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class GalleryItemController extends Controller
{
    public function __construct(private ServiceImageService $images) {}

    public function index(): View
    {
        $items = GalleryItem::query()->orderBy('display_order')->paginate(20);

        return view('admin.gallery-items.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.gallery-items.form', [
            'item' => new GalleryItem(['is_active' => true, 'display_order' => 0]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['before_image'] = $this->storeImage($request, 'before_image');
        $data['after_image'] = $this->storeImage($request, 'after_image');

        GalleryItem::query()->create($data);

        return redirect()->route('admin.gallery-items.index')->with('success', 'Gallery item created.');
    }

    public function edit(GalleryItem $galleryItem): View
    {
        return view('admin.gallery-items.form', ['item' => $galleryItem]);
    }

    public function update(Request $request, GalleryItem $galleryItem): RedirectResponse
    {
        $data = $this->validated($request);
        $data['before_image'] = $this->resolveImage($request, 'before_image', $galleryItem->before_image);
        $data['after_image'] = $this->resolveImage($request, 'after_image', $galleryItem->after_image);
        $galleryItem->update($data);

        return redirect()->route('admin.gallery-items.index')->with('success', 'Gallery item updated.');
    }

    public function destroy(GalleryItem $galleryItem): RedirectResponse
    {
        $this->images->delete($galleryItem->before_image);
        $this->images->delete($galleryItem->after_image);
        $galleryItem->delete();

        return redirect()->route('admin.gallery-items.index')->with('success', 'Gallery item deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'service' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'before_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'after_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'remove_before_image' => ['sometimes', 'boolean'],
            'remove_after_image' => ['sometimes', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        unset(
            $data['before_image'],
            $data['after_image'],
            $data['remove_before_image'],
            $data['remove_after_image'],
        );

        return [
            ...$data,
            'display_order' => (int) ($data['display_order'] ?? 0),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    private function storeImage(Request $request, string $field): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        try {
            return $this->images->store($request->file($field), 'gallery');
        } catch (InvalidArgumentException $e) {
            throw ValidationException::withMessages([$field => $e->getMessage()]);
        }
    }

    private function resolveImage(Request $request, string $field, ?string $current): ?string
    {
        if ($request->boolean('remove_'.$field) && ! $request->hasFile($field)) {
            $this->images->delete($current);

            return null;
        }

        if (! $request->hasFile($field)) {
            return $current;
        }

        try {
            $path = $this->images->store($request->file($field), 'gallery');
        } catch (InvalidArgumentException $e) {
            throw ValidationException::withMessages([$field => $e->getMessage()]);
        }

        $this->images->delete($current);

        return $path;
    }
}

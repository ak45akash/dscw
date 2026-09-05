<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\AuditLogService;
use App\Services\MediaLibraryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class MediaController extends Controller
{
    public function __construct(
        private MediaLibraryService $mediaLibrary,
        private AuditLogService $auditLog,
    ) {}

    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->hasPermission('content.manage'), 403);

        $media = Media::query()
            ->when($request->filled('folder'), fn ($q) => $q->where('folder', $request->string('folder')))
            ->latest()
            ->paginate(24)
            ->withQueryString();

        return view('admin.media.index', [
            'media' => $media,
            'folders' => Media::query()->distinct()->orderBy('folder')->pluck('folder'),
            'filters' => $request->only(['folder']),
            'disk' => $this->mediaLibrary->disk(),
        ]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()?->hasPermission('content.manage'), 403);

        return view('admin.media.form', [
            'item' => new Media(['folder' => 'media']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->hasPermission('content.manage'), 403);

        $data = $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'alt' => ['nullable', 'string', 'max:255'],
            'folder' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $media = $this->mediaLibrary->storeAsMedia(
                $request->file('file'),
                $data['folder'] ?: 'media',
                $data['alt'] ?? null,
                auth()->id(),
            );
        } catch (InvalidArgumentException $e) {
            throw ValidationException::withMessages(['file' => $e->getMessage()]);
        }

        $this->auditLog->log('media', 'uploaded', $media, null, ['path' => $media->path]);

        return redirect()->route('admin.media.index')->with('success', 'Media uploaded.');
    }

    public function edit(Media $medium): View
    {
        abort_unless(auth()->user()?->hasPermission('content.manage'), 403);

        return view('admin.media.form', ['item' => $medium]);
    }

    public function update(Request $request, Media $medium): RedirectResponse
    {
        abort_unless(auth()->user()?->hasPermission('content.manage'), 403);

        $data = $request->validate([
            'alt' => ['nullable', 'string', 'max:255'],
            'folder' => ['nullable', 'string', 'max:50'],
        ]);

        $medium->update([
            'alt' => $data['alt'] ?? '',
            'folder' => $data['folder'] ?: $medium->folder,
        ]);

        return redirect()->route('admin.media.index')->with('success', 'Media updated.');
    }

    public function destroy(Media $medium): RedirectResponse
    {
        abort_unless(auth()->user()?->hasPermission('content.manage'), 403);

        $old = ['path' => $medium->path];
        $this->mediaLibrary->deleteMedia($medium);
        $this->auditLog->log('media', 'deleted', null, $old, null);

        return redirect()->route('admin.media.index')->with('success', 'Media deleted.');
    }
}

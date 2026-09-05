<x-layouts.admin title="Media Library" breadcrumb="Content / Media">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <form method="GET" class="flex items-end gap-2">
            <div>
                <x-label for="folder">Folder</x-label>
                <select name="folder" id="folder" class="form-input">
                    <option value="">All</option>
                    @foreach($folders as $folder)
                        <option value="{{ $folder }}" @selected(($filters['folder'] ?? '') === $folder)>{{ $folder }}</option>
                    @endforeach
                </select>
            </div>
            <x-button type="submit" variant="secondary">Filter</x-button>
        </form>
        <div class="flex items-center gap-3">
            <span class="text-xs text-graphite-500">Disk: <code>{{ $disk }}</code></span>
            <x-button href="{{ route('admin.media.create') }}">Upload</x-button>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($media as $item)
            <x-card class="overflow-hidden p-0">
                <div class="aspect-video bg-graphite-100 dark:bg-graphite-800">
                    <img src="{{ $item->url() }}" alt="{{ $item->alt ?: $item->original_name }}" class="h-full w-full object-cover">
                </div>
                <div class="space-y-2 p-3 text-sm">
                    <div class="truncate font-medium" title="{{ $item->original_name }}">{{ $item->original_name ?: $item->filename }}</div>
                    <div class="text-xs text-graphite-500">{{ $item->folder }} · {{ $item->humanSize() }}</div>
                    <div class="flex justify-between gap-2">
                        <a href="{{ route('admin.media.edit', $item) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.media.destroy', $item) }}" onsubmit="return confirm('Delete this file?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                    <button type="button" class="w-full truncate rounded border border-graphite-200 px-2 py-1 text-left text-xs hover:bg-graphite-50 dark:border-graphite-700" onclick="navigator.clipboard.writeText(@js($item->url()))">Copy URL</button>
                </div>
            </x-card>
        @empty
            <x-card class="sm:col-span-2 lg:col-span-3 xl:col-span-4 text-center text-graphite-500">No media yet. Upload images to reuse across the site.</x-card>
        @endforelse
    </div>

    @if($media->hasPages())
        <div class="mt-6">{{ $media->links() }}</div>
    @endif
</x-layouts.admin>

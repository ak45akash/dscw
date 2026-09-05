<x-layouts.admin :title="$item->exists ? 'Edit Media' : 'Upload Media'" breadcrumb="Content / Media / Form">
    <form method="POST" action="{{ $item->exists ? route('admin.media.update', $item) : route('admin.media.store') }}" enctype="multipart/form-data" class="max-w-xl space-y-6">
        @csrf
        @if($item->exists) @method('PUT') @endif

        <x-card class="space-y-4">
            @if($item->exists)
                <img src="{{ $item->url() }}" alt="" class="max-h-48 rounded-lg object-contain">
                <p class="text-xs text-graphite-500 break-all">{{ $item->path }}</p>
            @else
                <div>
                    <x-label for="file" required>Image</x-label>
                    <input type="file" name="file" id="file" accept="image/jpeg,image/png,image/gif,image/webp" class="form-input" required>
                </div>
            @endif
            <div>
                <x-label for="alt">Alt text</x-label>
                <x-input name="alt" id="alt" :value="old('alt', $item->alt)" />
            </div>
            <div>
                <x-label for="folder">Folder</x-label>
                <x-input name="folder" id="folder" :value="old('folder', $item->folder ?? 'media')" />
            </div>
        </x-card>

        <div class="flex justify-between">
            <a href="{{ route('admin.media.index') }}" class="btn-secondary">Cancel</a>
            <x-button type="submit">{{ $item->exists ? 'Save' : 'Upload' }}</x-button>
        </div>
    </form>
</x-layouts.admin>

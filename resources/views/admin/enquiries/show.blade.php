<x-layouts.admin title="Enquiry" breadcrumb="Content / Enquiries / Detail">
    <div class="mb-6">
        <a href="{{ route('admin.enquiries.index') }}" class="btn-secondary">Back</a>
    </div>
    <x-card class="max-w-3xl space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">{{ $enquiry->name }}</h2>
                <p class="text-sm text-graphite-500">{{ $enquiry->email }} · {{ $enquiry->phone ?: 'No phone' }}</p>
            </div>
            <x-badge color="gray">{{ $enquiry->status }}</x-badge>
        </div>
        <div>
            <p class="text-sm font-medium text-graphite-500">Subject</p>
            <p>{{ $enquiry->subject ?: '—' }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-graphite-500">Message</p>
            <p class="whitespace-pre-wrap">{{ $enquiry->message }}</p>
        </div>
        <p class="text-xs text-graphite-500">Received {{ $enquiry->created_at?->format('M j, Y H:i') }}</p>
        <form method="POST" action="{{ route('admin.enquiries.status', $enquiry) }}" class="flex flex-wrap gap-2">
            @csrf
            @method('PATCH')
            <select name="status" class="form-input w-auto">
                @foreach(['new', 'read', 'archived'] as $status)
                    <option value="{{ $status }}" @selected($enquiry->status === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <x-button type="submit" variant="secondary">Update status</x-button>
        </form>
    </x-card>
</x-layouts.admin>

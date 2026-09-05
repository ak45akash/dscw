<x-layouts.admin title="Audit Log Detail" breadcrumb="System / Audit Logs / Detail">
    <div class="mb-4">
        <a href="{{ route('admin.audit-logs.index') }}" class="text-sm text-blue-600 hover:underline">← All logs</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card class="space-y-3 text-sm">
            <div><span class="text-graphite-500">When</span><div class="font-medium">{{ $log->created_at->toDayDateTimeString() }}</div></div>
            <div><span class="text-graphite-500">User</span><div class="font-medium">{{ $log->user?->name ?? 'System' }} ({{ $log->user?->email ?? '—' }})</div></div>
            <div><span class="text-graphite-500">Module / Action</span><div class="font-medium">{{ $log->module }} · {{ $log->action }}</div></div>
            <div><span class="text-graphite-500">IP</span><div class="font-medium">{{ $log->ip_address ?? '—' }}</div></div>
            <div><span class="text-graphite-500">User agent</span><div class="break-all font-medium">{{ $log->user_agent ?? '—' }}</div></div>
            @if($log->auditable_type)
                <div><span class="text-graphite-500">Auditable</span><div class="font-medium">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</div></div>
            @endif
        </x-card>

        <div class="space-y-4">
            <x-card>
                <h3 class="mb-3 font-semibold">Old values</h3>
                <pre class="overflow-x-auto rounded-lg bg-graphite-50 p-3 text-xs dark:bg-graphite-950">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '—' }}</pre>
            </x-card>
            <x-card>
                <h3 class="mb-3 font-semibold">New values</h3>
                <pre class="overflow-x-auto rounded-lg bg-graphite-50 p-3 text-xs dark:bg-graphite-950">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '—' }}</pre>
            </x-card>
        </div>
    </div>
</x-layouts.admin>

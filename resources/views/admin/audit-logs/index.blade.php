<x-layouts.admin title="Audit Logs" breadcrumb="System / Audit Logs">
    <form method="GET" class="mb-6 grid gap-3 rounded-xl border border-graphite-200 bg-white p-4 dark:border-graphite-800 dark:bg-graphite-900 sm:grid-cols-5">
        <div>
            <x-label for="module">Module</x-label>
            <select name="module" id="module" class="form-input">
                <option value="">All</option>
                @foreach($modules as $module)
                    <option value="{{ $module }}" @selected(($filters['module'] ?? '') === $module)>{{ $module }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-label for="user_id">User</x-label>
            <select name="user_id" id="user_id" class="form-input">
                <option value="">All</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(($filters['user_id'] ?? '') == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-label for="from">From</x-label>
            <x-input type="date" name="from" id="from" :value="$filters['from'] ?? ''" />
        </div>
        <div>
            <x-label for="to">To</x-label>
            <x-input type="date" name="to" id="to" :value="$filters['to'] ?? ''" />
        </div>
        <div class="flex items-end">
            <x-button type="submit" class="w-full">Filter</x-button>
        </div>
    </form>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-graphite-200 text-sm dark:divide-graphite-800">
                <thead class="bg-graphite-50 dark:bg-graphite-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">When</th>
                        <th class="px-4 py-3 text-left font-semibold">User</th>
                        <th class="px-4 py-3 text-left font-semibold">Module</th>
                        <th class="px-4 py-3 text-left font-semibold">Action</th>
                        <th class="px-4 py-3 text-right font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-graphite-100 dark:divide-graphite-800">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="px-4 py-3">{{ $log->module }}</td>
                            <td class="px-4 py-3">{{ $log->action }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.audit-logs.show', $log) }}" class="text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-graphite-500">No audit entries.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="border-t border-graphite-200 p-4 dark:border-graphite-800">{{ $logs->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>

<x-layouts.admin :title="$user->exists ? 'Edit User' : 'Add User'" breadcrumb="System / Users / Form">
    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="max-w-xl space-y-6">
        @csrf
        @if($user->exists) @method('PUT') @endif

        <x-card class="space-y-4">
            <div>
                <x-label for="name" required>Name</x-label>
                <x-input name="name" id="name" :value="old('name', $user->name)" required />
            </div>
            <div>
                <x-label for="email" required>Email</x-label>
                <x-input type="email" name="email" id="email" :value="old('email', $user->email)" required />
            </div>
            <div>
                <x-label for="password" :required="! $user->exists">Password</x-label>
                <x-input type="password" name="password" id="password" autocomplete="new-password" @if(! $user->exists) required @endif />
                @if($user->exists)
                    <x-form-hint>Leave blank to keep the current password.</x-form-hint>
                @endif
            </div>
            <div>
                <x-label for="password_confirmation">Confirm password</x-label>
                <x-input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" />
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $user->is_active ?? true))>
                <x-label for="is_active" class="mb-0">Active</x-label>
            </div>
            <div>
                <x-label required>Roles</x-label>
                <div class="mt-2 space-y-2">
                    @foreach($roles as $role)
                        <label class="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                name="role_ids[]"
                                value="{{ $role->id }}"
                                @checked(in_array($role->id, old('role_ids', $selectedRoleIds ?? []), false))
                            >
                            <span>{{ $role->label }} <span class="text-graphite-400">({{ $role->name }})</span></span>
                        </label>
                    @endforeach
                </div>
                @error('role_ids') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </x-card>

        <div class="flex justify-between">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
            <x-button type="submit">Save</x-button>
        </div>
    </form>
</x-layouts.admin>

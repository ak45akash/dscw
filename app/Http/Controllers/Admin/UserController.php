<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private AuditLogService $auditLog) {}

    public function index(): View
    {
        $this->authorizePermission('system.manage');

        $users = User::query()->with('roles')->orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorizePermission('system.manage');

        return view('admin.users.form', [
            'user' => new User(['is_active' => true]),
            'roles' => Role::query()->orderBy('label')->get(),
            'selectedRoleIds' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizePermission('system.manage');

        $data = $this->validated($request);
        $roleIds = $data['role_ids'];
        unset($data['role_ids']);

        $user = User::query()->create($data);
        $user->roles()->sync($roleIds);

        $this->auditLog->log(
            module: 'users',
            action: 'created',
            auditable: $user,
            newValues: ['email' => $user->email, 'roles' => $user->roles()->pluck('name')->all()],
        );

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        $this->authorizePermission('system.manage');

        return view('admin.users.form', [
            'user' => $user->load('roles'),
            'roles' => Role::query()->orderBy('label')->get(),
            'selectedRoleIds' => $user->roles->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizePermission('system.manage');

        $data = $this->validated($request, $user);
        $roleIds = $data['role_ids'];
        unset($data['role_ids']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($redirect = $this->guardLastSuperAdmin($user, $roleIds, (bool) ($data['is_active'] ?? false))) {
            return $redirect;
        }

        $old = ['email' => $user->email, 'is_active' => $user->is_active, 'roles' => $user->roles->pluck('name')->all()];

        $user->update($data);
        $user->roles()->sync($roleIds);

        $this->auditLog->log(
            module: 'users',
            action: 'updated',
            auditable: $user,
            oldValues: $old,
            newValues: ['email' => $user->email, 'is_active' => $user->is_active, 'roles' => $user->fresh()->roles->pluck('name')->all()],
        );

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorizePermission('system.manage');

        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if ($user->isSuperAdmin() && $this->superAdminCount() <= 1) {
            return back()->withErrors(['user' => 'Cannot delete the last super admin.']);
        }

        $this->auditLog->log(
            module: 'users',
            action: 'deleted',
            oldValues: ['email' => $user->email, 'roles' => $user->roles->pluck('name')->all()],
        );

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
            'is_active' => ['sometimes', 'boolean'],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ]);

        return [
            ...$data,
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function guardLastSuperAdmin(User $user, array $roleIds, bool $isActive): ?RedirectResponse
    {
        $superAdminRoleId = Role::query()->where('name', 'super_admin')->value('id');
        $willRemainSuper = $superAdminRoleId && in_array((int) $superAdminRoleId, array_map('intval', $roleIds), true);

        if ($user->isSuperAdmin() && (! $willRemainSuper || ! $isActive) && $this->superAdminCount() <= 1) {
            return back()->withInput()->withErrors(['role_ids' => 'Cannot remove or deactivate the last super admin.']);
        }

        if ($user->id === auth()->id() && ! $isActive) {
            return back()->withInput()->withErrors(['is_active' => 'You cannot deactivate your own account.']);
        }

        return null;
    }

    private function superAdminCount(): int
    {
        return User::query()->whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->count();
    }

    private function authorizePermission(string $permission): void
    {
        abort_unless(auth()->user()?->hasPermission($permission), 403);
    }
}

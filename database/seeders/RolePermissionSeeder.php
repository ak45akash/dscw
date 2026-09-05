<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = collect(config('dscw.permissions'))
            ->map(function (string $name) {
                $group = explode('.', $name)[0];

                return Permission::query()->updateOrCreate(
                    ['name' => $name],
                    [
                        'label' => str($name)->replace(['.', '_'], ' ')->title()->toString(),
                        'group' => $group,
                    ]
                );
            });

        $wildcard = Permission::query()->updateOrCreate(
            ['name' => '*'],
            ['label' => 'All Permissions', 'group' => 'system']
        );

        foreach (config('dscw.roles') as $roleName => $roleConfig) {
            $role = Role::query()->updateOrCreate(
                ['name' => $roleName],
                [
                    'label' => $roleConfig['label'],
                    'description' => "Default {$roleConfig['label']} role",
                ]
            );

            $permissionNames = $roleConfig['permissions'];

            if (in_array('*', $permissionNames, true)) {
                $role->permissions()->sync([$wildcard->id]);
            } else {
                $role->permissions()->sync(
                    $permissions->whereIn('name', $permissionNames)->pluck('id')
                );
            }
        }
    }
}

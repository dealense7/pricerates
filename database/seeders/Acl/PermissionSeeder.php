<?php

declare(strict_types=1);

namespace Database\Seeders\Acl;

use App\Models\Acl\Permission;
use App\Models\Acl\Role;
use App\Models\User\User;
use Illuminate\Cache\TaggableStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class PermissionSeeder extends Seeder
{
    protected array $groupedPermissions = [
        User::PERMISSIONS_SCOPE => [
            'display_name' => 'იუზერების მართვა',
            'permissions'  => [
                'read'          => ['display_name' => 'ნახვა'],
                'create'        => ['display_name' => 'შექმნა'],
                'update'        => ['display_name' => 'რედაქტირება'],
                'delete'        => ['display_name' => 'წაშლა'],
                'restore'       => ['display_name' => 'აღდგენა'],
                'deactivate'    => ['display_name' => 'დეაქტივაცია'],
                'activate'      => ['display_name' => 'აქტივაცია'],
                'read_everyone' => ['display_name' => 'ყველას ნახვა'],
            ],
        ],
        Role::PERMISSIONS_SCOPE => [
            'display_name' => 'როლების მართვა',
            'permissions'  => [
                'read'    => ['display_name' => 'ნახვა'],
                'create'  => ['display_name' => 'შექმნა'],
                'update'  => ['display_name' => 'რედაქტირება'],
                'delete'  => ['display_name' => 'წაშლა'],
                'restore' => ['display_name' => 'აღდგენა'],
            ],
        ],
    ];

    public function run(): void
    {
        foreach ($this->groupedPermissions as $key => $values) {
            $role = $this->createRole($key, $values['display_name']);

            $permissionIds = [];
            foreach ($values['permissions'] as $permissionKey => $permissionValues) {
                $permission      = $this->createPermission($key . '.' . $permissionKey, $permissionValues['display_name']);
                $permissionIds[] = $permission->getId();
            }

            $role->permissions()->sync($permissionIds);
        }

        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags([Role::class])->flush();
        }
    }

    protected function createPermission(string $key, string $name): Permission
    {
        return Permission::firstOrCreate(['name' => $key], [
            'name'         => $key,
            'display_name' => $name,
            'guard_name'   => 'api',
        ]);
    }

    private function createRole(string $key, string $name): Role
    {
        $role = (new Role())->where('name', 'manage_' . $key)->first();

        if ($role === null) {
            $role = (new Role())->create(
                [
                    'name'         => 'manage_' . $key,
                    'display_name' => $name,
                    'guard_name'   => 'api',
                ],
            );
        }

        return $role;
    }
}

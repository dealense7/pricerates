<?php

declare(strict_types=1);

namespace Database\Seeders\Acl;

use App\Enums\Acl\DefaultRoles;
use App\Models\Acl\Role;
use Illuminate\Cache\TaggableStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'         => DefaultRoles::MODERATOR->value,
                'display_name' => 'მოდერატორი',
                'is_custom'    => true,
            ],
            [
                'name'         => DefaultRoles::INITIATOR->value,
                'display_name' => 'ინიციატორი',
                'is_custom'    => true,
            ],
            [
                'name'         => DefaultRoles::REPRESENTATIVE->value,
                'display_name' => 'კომპანიის წარმომადგენელი',
                'is_custom'    => true,
            ],
        ];

        foreach ($roles as $role) {
            (new Role())->create(
                [
                    'name'         => $role['name'],
                    'display_name' => $role['display_name'],
                    'guard_name'   => 'api',
                ],
            );
        }

        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags([Role::class])->flush();
        }
    }
}

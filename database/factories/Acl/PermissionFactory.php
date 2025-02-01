<?php

declare(strict_types=1);

namespace Database\Factories\Acl;

use App\Models\Acl\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        return [
            'name'         => fake()->name,
            'display_name' => fake()->name,
            'guard_name'   => 'api',
        ];
    }
}

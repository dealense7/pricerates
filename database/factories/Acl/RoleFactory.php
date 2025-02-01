<?php

declare(strict_types=1);

namespace Database\Factories\Acl;

use App\Models\Acl\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name'         => fake()->name,
            'display_name' => fake()->word,
            'guard_name'   => 'api',
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Acl\Role;
use App\Models\User\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory([
            'email'     => 'admin@mail.com',
            'password'  => Hash::make('12345678'),
        ])->create();

        $roles = Role::all();
        $user->roles()->sync($roles->pluck('id'));
    }
}

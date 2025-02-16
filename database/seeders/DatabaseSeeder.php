<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Auth\Provider;
use App\Enums\User\ContactType;
use App\Models\Acl\Role;
use App\Models\User\ContactInformation;
use App\Models\User\User;
use Database\Seeders\Acl\PermissionSeeder;
use Database\Seeders\Acl\RoleSeeder;
use Database\Seeders\Currency\CurrencySeeder;
use Database\Seeders\General\CategorySeeder;
use Database\Seeders\General\ProviderSeeder;
use Database\Seeders\Store\StoreSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Client::factory()->create([
            'id'              => Provider::Internal,
            'provider'        => 'users',
            'password_client' => 1,
            'secret'          => null,
        ]);

        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,

            // Data
            ProviderSeeder::class,
            StoreSeeder::class,
            CategorySeeder::class,

            //Currency
            Currency\ProviderSeeder::class,
            CurrencySeeder::class,
        ]);

        $moderator = User::factory()->create([
            'username' => 'Moderator User',
            'email'    => 'example@email.com',
            'password' => Hash::make('12345678'),
        ]);

        ContactInformation::factory()->create([
            'user_id' => $moderator->id,
            'type'    => ContactType::EMAIL,
            'data'    => 'example@email.com',
        ]);

        $moderator->roles()->sync(Role::all()->pluck('id')->toArray());
    }
}

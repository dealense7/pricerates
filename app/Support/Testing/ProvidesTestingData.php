<?php

declare(strict_types=1);

namespace App\Support\Testing;

use App\Models\Acl\Permission;
use App\Models\Acl\Role;
use App\Models\Client\Company;
use App\Models\User\ContactInformation;
use App\Models\User\User;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client as OauthClient;

class ProvidesTestingData
{
    use InteractsWithAuthentication;

    /** @var array<\Faker\Generator> */
    private static array $faker = [];

    public static function createRandomOauthClient(array $params = []): OauthClient
    {
        return OauthClient::factory()->create($params);
    }

    public static function createRandomUserAndAuthorize(array $params = [], array $options = []): User
    {
        $user = self::createRandomUsers($params, $options)->first();

        (new self())->be($user, 'api');

        return $user;
    }

    public static function createRandomUsers(array $params = [], array $options = [], int $count = 1): Collection
    {
        $users = User::factory()->count($count)->create($params);
        foreach ($users as $user) {
            if (isset($options['permissions'])) {
                $permissions = [];

                foreach ((array) $options['permissions'] as $permission) {
                    $permissionObject = (new Permission())->firstOrCreate(
                        ['name' => $permission],
                        [
                            'display_name' => self::getFaker()->word,
                            'guard_name'   => 'api',
                        ],
                    );

                    $permissions[] = $permissionObject;
                }

                if ($permissions) {
                    $user->addUserPermissions($permissions);
                }
            }

            if (isset($options['roles'])) {
                $roles = [];

                foreach ((array) $options['roles'] as $role) {
                    $roleObject = (new Role())->firstOrCreate(
                        ['name' => $role],
                        [
                            'display_name' => self::getFaker()->word,
                            'guard_name'   => 'api',
                        ],
                    );

                    $roles[] = $roleObject;
                }

                if ($roles) {
                    $user->addUserRoles($roles);
                }
            }
        }

        return $users;
    }

    public static function createCompanyRandomItem(array $params = [], int $count = 1): Collection
    {
        return Company::factory()->count($count)->create($params);
    }

    public static function createContactInformationRandomItem(array $params = [], int $count = 1): Collection
    {
        return ContactInformation::factory()->count($count)->create($params);
    }

    public static function createRoleRandomItem(array $params = [], int $count = 1): Collection
    {
        return Role::factory()->count($count)->create($params);
    }

    public static function createPermissionRandomItem(array $params = [], int $count = 1): Collection
    {
        return Permission::factory()->count($count)->create($params);
    }

    public static function getFaker($locale = Factory::DEFAULT_LOCALE): Generator
    {
        if (! isset(self::$faker[$locale])) {
            self::$faker[$locale] = Factory::create($locale);
        }

        return self::$faker[$locale];
    }

    public function be(UserContract $user, $guard = null): static
    {
        if (isset($user->wasRecentlyCreated) && $user->wasRecentlyCreated) {
            $user->wasRecentlyCreated = false;
        }

        Auth::guard($guard)->setUser($user);

        Auth::shouldUse($guard);

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Providers;

use App\CacheRepositories\V1\Acl\PermissionCacheRepository;
use App\CacheRepositories\V1\Acl\RoleCacheRepository;
use App\Contracts\Repositories\Acl\PermissionRepositoryContract;
use App\Contracts\Repositories\Acl\RoleRepositoryContract;
use App\Contracts\Repositories\Currency\CurrencyRepositoryContract;
use App\Contracts\Repositories\Gas\GasRepositoryContract;
use App\Contracts\Repositories\Product\ProductRepositoryContract;
use App\Contracts\Repositories\User\ContactInformationRepositoryContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Contracts\Requests\Acl\RoleSaveRequestContract;
use App\Contracts\Requests\User\AttachPermissionsRequestContract;
use App\Contracts\Requests\User\AttachRolesRequestContract;
use App\Contracts\Requests\User\CreateSaveRequestContract;
use App\Contracts\Requests\User\PasswordChangeRequestContract;
use App\Contracts\Requests\User\UpdateSaveRequestContract;
use App\Contracts\Requests\User\UserDeactivateRequestContract;
use App\Contracts\Services\Acl\PermissionServiceContract;
use App\Contracts\Services\Acl\RoleServiceContract;
use App\Contracts\Services\Currency\CurrencyServiceContract;
use App\Contracts\Services\Gas\GasServiceContract;
use App\Contracts\Services\Product\ProductServiceContract;
use App\Contracts\Services\User\UserServiceContract;
use App\Http\Requests\Api\V1\Acl\RoleSaveRequest;
use App\Http\Requests\Api\V1\User\AttachPermissionsRequest;
use App\Http\Requests\Api\V1\User\AttachRolesRequest;
use App\Http\Requests\Api\V1\User\CreateSaveRequest;
use App\Http\Requests\Api\V1\User\PasswordChangeRequest;
use App\Http\Requests\Api\V1\User\UpdateSaveRequest;
use App\Http\Requests\Api\V1\User\UserDeactivateRequest;
use App\Repositories\V1\Acl\PermissionRepository;
use App\Repositories\V1\Acl\RoleRepository;
use App\Repositories\V1\Currency\CurrencyRepository;
use App\Repositories\V1\Gas\GasRepository;
use App\Repositories\V1\Product\ProductRepository;
use App\Repositories\V1\User\ContactInformationRepository;
use App\Repositories\V1\User\UserRepository;
use App\Services\V1\Acl\PermissionService;
use App\Services\V1\Acl\RoleService;
use App\Services\V1\Currency\CurrencyService;
use App\Services\V1\Gas\GasService;
use App\Services\V1\Products\ProductService;
use App\Services\V1\User\UserService;
use Illuminate\Support\ServiceProvider;

class BindingServiceProvider extends ServiceProvider
{
    private const REPOSITORIES = [
        UserRepositoryContract::class               => [
            'v1' => [
                UserRepository::class,
            ],
        ],
        RoleRepositoryContract::class               => [
            'v1' => [
                RoleRepository::class,
                RoleCacheRepository::class,
            ],
        ],
        PermissionRepositoryContract::class         => [
            'v1' => [
                PermissionRepository::class,
                PermissionCacheRepository::class,
            ],
        ],
        ContactInformationRepositoryContract::class => [
            'v1' => [
                ContactInformationRepository::class,
            ],
        ],
        CurrencyRepositoryContract::class           => [
            'v1' => [
                CurrencyRepository::class,
            ],
        ],
        GasRepositoryContract::class                => [
            'v1' => [
                GasRepository::class,
            ],
        ],
        ProductRepositoryContract::class            => [
            'v1' => [
                ProductRepository::class,
            ],
        ],
    ];

    private const SERVICES = [
        UserServiceContract::class       => [
            'v1' => [
                UserService::class,
            ],
        ],
        RoleServiceContract::class       => [
            'v1' => [
                RoleService::class,
            ],
        ],
        PermissionServiceContract::class => [
            'v1' => [
                PermissionService::class,
            ],
        ],
        CurrencyServiceContract::class   => [
            'v1' => [
                CurrencyService::class,
            ],
        ],
        GasServiceContract::class        => [
            'v1' => [
                GasService::class,
            ],
        ],
        ProductServiceContract::class    => [
            'v1' => [
                ProductService::class,
            ],
        ],
    ];

    private const REQUESTS = [
        CreateSaveRequestContract::class        => [
            'v1' => [
                CreateSaveRequest::class,
            ],
        ],
        UpdateSaveRequestContract::class        => [
            'v1' => [
                UpdateSaveRequest::class,
            ],
        ],
        PasswordChangeRequestContract::class    => [
            'v1' => [
                PasswordChangeRequest::class,
            ],
        ],
        RoleSaveRequestContract::class          => [
            'v1' => [
                RoleSaveRequest::class,
            ],
        ],
        AttachPermissionsRequestContract::class => [
            'v1' => [
                AttachPermissionsRequest::class,
            ],
        ],
        AttachRolesRequestContract::class       => [
            'v1' => [
                AttachRolesRequest::class,
            ],
        ],
        UserDeactivateRequestContract::class    => [
            'v1' => [
                UserDeactivateRequest::class,
            ],
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function register(): void
    {
        $version = mb_strtoupper($this->app['request']->header(
            'X-Api-Version',
            config('custom.constants.default_app_version'),
        ));
        $version = strtolower($version);

        $cacheServices = config('custom.constants.cache_services');

        $bindings = [
            ...self::REPOSITORIES,
            ...self::SERVICES,
            ...self::REQUESTS,
        ];

        foreach ($bindings as $abstract => $repositories) {
            $repositories = $repositories[$version];

            $concrete = $cacheServices ? ($repositories[1] ?? $repositories[0]) : $repositories[0];
            $this->app->bind($abstract, $concrete);
        }
    }
}

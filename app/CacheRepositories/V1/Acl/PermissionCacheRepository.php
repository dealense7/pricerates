<?php

declare(strict_types=1);

namespace App\CacheRepositories\V1\Acl;

use App\CacheRepositories\CacheRepository;
use App\Contracts\Repositories\Acl\PermissionRepositoryContract;
use App\Models\Acl\Role;
use App\Repositories\V1\Acl\PermissionRepository;
use App\Support\Collection;
use Illuminate\Contracts\Cache\Repository as CacheContract;

class PermissionCacheRepository extends CacheRepository implements PermissionRepositoryContract
{
    protected string $cacheKey = Role::class;

    private PermissionRepository $repository;

    public function __construct(CacheContract $cache, PermissionRepository $repository)
    {
        $this->cache      = $cache;
        $this->repository = $repository;
    }

    public function getAllItems(array $filters = []): Collection
    {
        $key = $this->createKeyFromArgs([
            func_get_args(),
        ], 'permission_items');

        return $this->setTag()->remember($key, function () use ($filters) {
            return $this->repository->getAllItems($filters);
        });
    }
}

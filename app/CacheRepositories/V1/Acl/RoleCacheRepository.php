<?php

declare(strict_types=1);

namespace App\CacheRepositories\V1\Acl;

use App\CacheRepositories\CacheRepository;
use App\Contracts\Repositories\Acl\RoleRepositoryContract;
use App\Models\Acl\Role;
use App\Repositories\V1\Acl\RoleRepository;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleCacheRepository extends CacheRepository implements RoleRepositoryContract
{
    protected string $cacheKey = Role::class;

    private RoleRepository $repository;

    public function __construct(CacheContract $cache, RoleRepository $repository)
    {
        $this->cache      = $cache;
        $this->repository = $repository;
    }

    public function findItems(
        array $filters = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator {
        $key = $this->createKeyFromArgs([
            func_get_args(),
        ], 'items');

        return $this->setTag()->remember($key, function () use ($filters, $page, $perPage, $sort) {
            return $this->repository->findItems($filters, $page, $perPage, $sort);
        });
    }

    public function findById(int $id): ?Role
    {
        $key = $this->createKeyFromArgs([
            func_get_args(),
        ], 'item');

        return $this->setTag()->remember($key, function () use ($id) {
            return $this->repository->findById($id);
        });
    }

    public function create(array $data): Role
    {
        $this->clearByTag();

        return $this->repository->create($data);
    }

    public function update(array $data, Role $item): Role
    {
        $this->clearByTag();

        return $this->repository->update($data, $item);
    }

    public function attachPermissions(array $permissions, Role $item): Role
    {
        $this->clearByTag();

        return $this->repository->attachPermissions($permissions, $item);
    }

    public function delete(Role $item): void
    {
        $this->clearByTag();

        $this->repository->delete($item);
    }
}

<?php

declare(strict_types=1);

namespace App\Services\V1\Acl;

use App\Contracts\Repositories\Acl\RoleRepositoryContract;
use App\Contracts\Services\Acl\RoleServiceContract;
use App\Exceptions\ItemNotFoundException;
use App\Models\Acl\Role;
use App\Services\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RoleService extends Service implements RoleServiceContract
{
    public function __construct(protected RoleRepositoryContract $repository,)
    {
    }

    public function findItems(
        array $filters = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator {
        $this->authorize('read', new Role());

        return $this->repository->findItems($filters, $page, $perPage, $sort);
    }

    public function findById(int $id): ?Role
    {
        $item = $this->repository->findById($id);

        if (empty($item)) {
            return null;
        }

        $this->authorize('read', $item);

        return $item;
    }

    public function findByIdOrFail(int $id): Role
    {
        $item = $this->findById($id);

        if (! $item) {
            throw new ItemNotFoundException();
        }

        return $item;
    }

    public function create(array $data): Role
    {
        $this->authorize('create', new Role());

        $permissions = $data['permissions'];
        $role        = $this->repository->create([
            'display_name' => $data['name'],
            'name'         => Str::snake($data['name']),
            'guard'        => 'api',
            'is_custom'    => true,
        ]);

        return $this->repository->attachPermissions($permissions, $role);
    }

    public function update(array $data, Role $item): Role
    {
        $this->authorize('update', new Role());

        $permissions = Arr::get($data, 'permissions', []);
        $role        = $this->repository->update(
            [
                'display_name' => $data['name'],
                'name'         => Str::snake($data['name']),
                'guard'        => 'api',
            ],
            $item,
        );

        return $this->repository->attachPermissions($permissions, $role);
    }

    public function delete(Role $item): bool
    {
        $this->authorize('delete', $item);

        $this->repository->delete($item);

        return true;
    }
}

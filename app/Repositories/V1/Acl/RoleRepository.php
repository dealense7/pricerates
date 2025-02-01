<?php

declare(strict_types=1);

namespace App\Repositories\V1\Acl;

use App\Contracts\Repositories\Acl\RoleRepositoryContract;
use App\Filters\FilterByName;
use App\Models\Acl\Role;
use App\Repositories\Repository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleRepository extends Repository implements RoleRepositoryContract
{
    public function findItems(
        array $filters = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator {
        $model = $this->getModel();

        $items = $this->getData($filters)
            ->with([
                'permissions',
            ]);

        foreach ($model->parseSort($sort) as $column => $direction) {
            $items = $items->orderBy($column, $direction);
        }

        $items = $items->orderBy('id', 'desc');

        return $items->paginate($model->getValidPerPage($perPage), ['*'], 'page', $page);
    }

    public function update(array $data, Role $item): Role
    {
        $item->fill($data);

        $item->saveOrFail();

        $item->load([
            'permissions',
        ]);

        return $item;
    }

    public function attachPermissions(array $permissions, Role $item): Role
    {
        $item->permissions()->sync($permissions);

        $item->load([
            'permissions',
        ]);

        return $item;
    }

    public function findById(int $id): ?Role
    {
        /** @var \App\Models\Acl\Role|null $item */
        $item = $this->getModel()
            ->with([
                'permissions',
            ])
            ->find($id);

        return $item;
    }

    public function create(array $data): Role
    {
        return $this->update($data, $this->getModel());
    }

    public function delete(Role $item): void
    {
        $item->delete();
    }

    public function getModel(): Role
    {
        return new Role();
    }

    public function getFilters(): array
    {
        return [
            FilterByName::class,
        ];
    }
}

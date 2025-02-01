<?php

declare(strict_types=1);

namespace App\Repositories\V1\User;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Filters\FilterByName;
use App\Filters\General\FilterByCompanyId;
use App\Models\User\User;
use App\Repositories\Repository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository extends Repository implements UserRepositoryContract
{
    public function findItems(
        array $filters = [],
        array $relations = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator {
        $model = $this->getModel();

        $items = $this->getData($filters);
        $items->with($relations);

        foreach ($model->parseSort($sort) as $column => $direction) {
            $items = $items->orderBy($column, $direction);
        }

        $items = $items->orderBy('id', 'desc');

        return $items->paginate($model->getValidPerPage($perPage), ['*'], 'page', $page);
    }

    public function findById(int $id, array $relations = [], array $filters = []): ?User
    {
        /** @var \App\Models\User\User|null $item */
        $item = $this->getData($filters)->with($relations)->find($id);

        return $item;
    }

    public function findRemovedItemByIdOrFail(int $id): ?User
    {
        /** @var \App\Models\User\User|null $item */
        $item = $this->getModel()->onlyTrashed()->find($id);

        return $item;
    }

    public function update(User $item, array $data, array $relations = []): User
    {
        $item->fill($data);

        $item->saveOrFail();

        $item->load($relations);

        return $item;
    }

    public function forceFill(User $item, array $data): bool
    {
        $item->forceFill($data)->saveOrFail();

        return true;
    }

    public function store(array $data, array $relations = []): User
    {
        return $this->update($this->getModel(), $data, $relations);
    }

    public function delete(User $item): void
    {
        $item->delete();
    }

    public function restore(User $item): User
    {
        $item->restore();

        return $item;
    }

    public function attachPermissions(array $permissions, User $item): User
    {
        $item->permissions()->sync($permissions);

        $item->load(
            'roles.permissions',
            'permissions',
        );

        return $item;
    }

    public function attachRoles(array $roles, User $item): User
    {
        $item->roles()->sync($roles);

        $item->load(
            'roles.permissions',
        );

        return $item;
    }

    public function getModel(): User
    {
        return new User();
    }

    public function getFilters(): array
    {
        return [
            FilterByName::class,
            FilterByCompanyId::class,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\User;

use App\Contracts\Requests\User\AttachPermissionsRequestContract;
use App\Contracts\Requests\User\AttachRolesRequestContract;
use App\Contracts\Requests\User\CreateSaveRequestContract;
use App\Contracts\Requests\User\PasswordChangeRequestContract;
use App\Contracts\Requests\User\UserDeactivateRequestContract;
use App\Contracts\Services\User\UserServiceContract;
use App\Http\Controllers\Api\ApiController;
use App\Resources\Acl\PermissionResource;
use App\Resources\Acl\RoleResource;
use App\Resources\User\UserResource;
use App\Support\Resources\Responses\ArrayResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class UserController extends ApiController
{
    public function items(UserServiceContract $service): JsonResponse
    {
        $filters   = $this->getInputFilters();
        $page      = $this->getInputPage();
        $perPage   = $this->getInputPerPage();
        $sort      = $this->getInputSort();
        $relations = [];

        $items = $service->findItems($filters, $relations, $page, $perPage, $sort);

        return $this->resource(
            $items,
            UserResource::class,
            $relations,
        );
    }

    public function show(int $id, UserServiceContract $service): JsonResponse
    {
        $relations = [
            'contactInformation',
        ];

        $item = $service->findById($id, $relations);

        if (! $item) {
            return $this->resourceNotFound(__('User not found'));
        }

        return $this->resource($item, UserResource::class, $relations);
    }

    public function store(
        CreateSaveRequestContract $request,
        UserServiceContract $service,
    ): JsonResponse {
        $data = $request->validated();

        $user = $service->store($data, ['contactInformation']);

        return $this->resource(
            $user,
            UserResource::class,
            [
                'contactInformation',
            ],
        )->setStatusCode(Response::HTTP_CREATED);
    }

    public function deactivate(
        int $id,
        UserDeactivateRequestContract $request,
        UserServiceContract $service,
    ): JsonResponse {
        $data   = $request->validated();
        $reason = Arr::get($data, 'reason');
        $user   = $service->findById($id);

        if (! $user) {
            return $this->resourceNotFound(__('User not found'));
        }

        $user = $service->deactivate($user, $reason);

        return $this->resource($user, UserResource::class);
    }

    public function activate(
        int $id,
        UserServiceContract $service,
    ): JsonResponse {
        $user = $service->findById($id);

        if (! $user) {
            return $this->resourceNotFound(__('User not found'));
        }

        $user = $service->activate($user);

        return $this->resource($user, UserResource::class);
    }

    public function delete(int $id, UserServiceContract $service): JsonResponse
    {
        $user = $service->findById($id);

        if (! $user) {
            return $this->resourceNotFound(__('User not found'));
        }

        $service->delete($user);

        return $this->success('ok');
    }

    public function restore(
        int $id,
        UserServiceContract $service,
    ): JsonResponse {
        $user = $service->findRemovedItemByIdOrFail($id);

        $user = $service->restore($user);

        return $this->resource(
            $user,
            UserResource::class,
        );
    }

    public function passwordUpdate(
        PasswordChangeRequestContract $request,
        UserServiceContract $service,
    ): JsonResponse {
        $data = $request->validated();

        $service->passwordUpdate($data);

        return $this->success('Password successfully updated');
    }

    public function getAcl(int $id, UserServiceContract $service): JsonResponse
    {
        $user = $service->findById($id);

        if (! $user) {
            return $this->resourceNotFound(__('User not found'));
        }

        $data = $service->getAcl($user);

        return $this->resource(
            $data,
            ArrayResource::class,
        );
    }

    public function attachPermissions(
        int $id,
        AttachPermissionsRequestContract $request,
        UserServiceContract $service,
    ): JsonResponse {

        $data = $request->validated();

        $user = $service->findById($id);

        if (! $user) {
            return $this->resourceNotFound(__('User not found'));
        }

        $items = $service->attachPermissions(Arr::get($data, 'permissions', []), $user);

        return $this->resource(
            $items,
            PermissionResource::class,
        );
    }

    public function attachRoles(
        int $id,
        AttachRolesRequestContract $request,
        UserServiceContract $service,
    ): JsonResponse {

        $roles = $request->validated()['roles'];

        $user = $service->findById($id);

        if (! $user) {
            return $this->resourceNotFound(__('User not found'));
        }

        $items = $service->attachRoles($roles, $user);

        return $this->resource(
            $items,
            RoleResource::class,
            [
                'permissions',
            ],
        );
    }
}

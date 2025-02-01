<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Acl;

use App\Contracts\Requests\Acl\RoleSaveRequestContract;
use App\Contracts\Services\Acl\RoleServiceContract;
use App\Http\Controllers\Api\ApiController;
use App\Resources\Acl\RoleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoleController extends ApiController
{
    public function items(RoleServiceContract $service,): JsonResponse
    {
        $filters = $this->getInputFilters();
        $page    = $this->getInputPage();
        $perPage = $this->getInputPerPage();
        $sort    = $this->getInputSort();

        $items = $service->findItems($filters, $page, $perPage, $sort);

        return $this->resource(
            $items,
            RoleResource::class,
            [
                'permissions',
            ],
        );
    }

    public function create(
        RoleSaveRequestContract $request,
        RoleServiceContract $service,
    ): JsonResponse {
        $data = $request->validated();

        $user = $service->create($data);

        return $this->resource(
            $user,
            RoleResource::class,
            [
                'permissions',
            ],
        )->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(
        int $id,
        RoleSaveRequestContract $request,
        RoleServiceContract $service,
    ): JsonResponse {
        $data = $request->validated();

        $item = $service->findByIdOrFail($id);

        $item = $service->update($data, $item);

        return $this->resource(
            $item,
            RoleResource::class,
            [
                'permissions',
            ],
        );
    }

    public function show(int $id, RoleServiceContract $service,): JsonResponse
    {
        $item = $service->findByIdOrFail($id);

        return $this->resource(
            $item,
            RoleResource::class,
            [
                'permissions',
            ],
        );
    }

    public function delete(int $id, RoleServiceContract $service,): JsonResponse
    {
        $item = $service->findByIdOrFail($id);

        $service->delete($item);

        return $this->success('ok');
    }
}

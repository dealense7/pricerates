<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Acl;

use App\Contracts\Services\Acl\PermissionServiceContract;
use App\Http\Controllers\Api\ApiController;
use App\Resources\Acl\PermissionResource;
use Illuminate\Http\JsonResponse;

class PermissionController extends ApiController
{
    public function items(
        PermissionServiceContract $service,
    ): JsonResponse {
        $filters = $this->getInputFilters();

        $items = $service->getAllItems($filters);

        return $this->resource(
            $items,
            PermissionResource::class,
        );
    }
}

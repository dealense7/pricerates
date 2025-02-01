<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Resources\Responses\ApiResponse;
use App\Support\Resources\Responses\ArrayResource;
use App\Support\Resources\Responses\ErrorResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ApiController extends Controller
{
    protected function error(string $error, int $code = 500, array $headers = [], string $message = 'error'): JsonResponse
    {
        return ApiResponse::resource($message, [$error], ErrorResource::class)
            ->response()
            ->setStatusCode($code)
            ->withHeaders($headers);
    }

    protected function throwCustomValidationError(string $field, string $error): void
    {
        throw ValidationException::withMessages([$field => $error]);
    }

    protected function resourceNotFound(string $message): JsonResponse
    {
        return $this->error($message, ResponseAlias::HTTP_NOT_FOUND);
    }

    protected function success(string $message, int $code = 200, array $headers = []): JsonResponse
    {
        return ApiResponse::resource($message, [], ArrayResource::class)
            ->response()
            ->setStatusCode($code)
            ->withHeaders($headers);
    }

    protected function resource($data, string $resourceType, array $includes = []): JsonResponse
    {
        return ApiResponse::resource('ok', $data, $resourceType, $includes)->response();
    }

    protected function getInputFilters(): array
    {
        return (array) $this->getRequest()->input('filters');
    }

    protected function getInputPage(): int
    {
        return (int) $this->getRequest()->input('page', 1);
    }

    protected function getInputPerPage(): ?int
    {
        $perPage = $this->getRequest()->input('perPage');
        if ($perPage) {
            $perPage = (int) $perPage;
        }

        return $perPage;
    }

    protected function getInputSort(): string
    {
        return (string) $this->getRequest()->input('sort');
    }
}

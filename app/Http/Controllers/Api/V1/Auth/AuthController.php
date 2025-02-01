<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Contracts\Services\User\UserServiceContract;
use App\Http\Controllers\Api\ApiController;
use App\Resources\User\UserResource;
use App\Support\Auth\Passport\Contracts\AuthServiceContract;
use App\Support\Resources\Responses\ArrayResource;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use League\OAuth2\Server\AuthorizationServer;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends ApiController
{
    use HandlesOAuthErrors;

    private AuthorizationServer $server;

    public function __construct(AuthorizationServer $server)
    {
        $this->server = $server;
    }

    /**
     * @throws \Laravel\Passport\Exceptions\OAuthServerException
     */
    public function token(ServerRequestInterface $request)
    {
        return $this->withErrorHandling(function () use ($request) {
            return $this->convertResponse(
                $this->server->respondToAccessTokenRequest($request, new Psr7Response()),
            );
        });
    }

    public function currentUser(AuthServiceContract $authService): JsonResponse
    {
        $user = $authService->getUser();

        return $this->resource($user, UserResource::class);
    }

    public function permissions(AuthServiceContract $authService, UserServiceContract $service): JsonResponse
    {
        $user = $authService->getUser();

        $data = $service->getAcl($user);

        return $this->resource(
            $data,
            ArrayResource::class,
        );
    }

    public function revokeToken(AuthServiceContract $authService): JsonResponse
    {
        $authService->revokeToken();

        return $this->success('ok');
    }
}

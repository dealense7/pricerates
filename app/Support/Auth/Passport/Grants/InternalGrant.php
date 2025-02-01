<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Grants;

use App\Support\Auth\Passport\Contracts\AuthServiceContract;
use App\Support\Auth\Passport\Contracts\RefreshTokenBridgeRepositoryContract;
use App\Support\Auth\Passport\Contracts\TokenRepositoryContract;
use DateInterval;
use Laravel\Passport\Bridge\AccessToken;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function is_null;

class InternalGrant extends AbstractGrant
{
    protected readonly AuthServiceContract $authService;

    public function __construct(
        AuthServiceContract $authService,
        RefreshTokenBridgeRepositoryContract $refreshTokenRepository,
    ) {
        $this->authService = $authService;
        $this->setRefreshTokenRepository($refreshTokenRepository);
        $this->refreshTokenTTL = new DateInterval('P1M');
    }

    /**
     * @throws \League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        DateInterval $accessTokenTTL,
    ): ResponseTypeInterface {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request));
        /** @var \App\Support\Auth\Passport\Contracts\UserContract $user */
        $user   = $this->validateUser($request);


        // Finalize the requested scopes
        $scopes = $this->scopeRepository->finalizeScopes(
            $scopes,
            $this->getIdentifier(),
            $client,
            $user->getIdentifier(),
        );

        // Issue and persist access token
        $accessToken  = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $scopes);
        $refreshToken = $this->issueRefreshToken($accessToken);

        if ($user->uses_otp_check) {
            $tokenRepository = app(TokenRepositoryContract::class);
            $tokenRepository->updateOtpVerified($accessToken->getIdentifier(), false);
        }

        // Inject access token into response type
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        // Fire login event
        $this->authService->fireLoginEvent('api', $user);

        return $responseType;
    }

    /**
     * @throws \League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function getRefreshToken(AccessToken $token): void
    {
        $this->issueRefreshToken($token);
    }

    public function getIdentifier(): string
    {
        return 'internal';
    }

    protected function validateClient(ServerRequestInterface $request): ClientEntityInterface
    {
        [$basicAuthUser,] = $this->getBasicAuthCredentials($request);

        $clientId = $this->getRequestParameter('client_id', $request, $basicAuthUser);
        if (is_null($clientId)) {
            throw OAuthServerException::invalidRequest('client_id');
        }

        // Get client without validating secret
        $client = $this->clientRepository->getClientEntity($clientId);

        if ($client instanceof ClientEntityInterface === false) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::CLIENT_AUTHENTICATION_FAILED, $request));
            throw OAuthServerException::invalidClient($request);
        }

        return $client;
    }

    protected function validateUser(ServerRequestInterface $request): UserEntityInterface
    {
        $login = $this->getRequestParameter('login', $request);
        if (is_null($login)) {
            throw OAuthServerException::invalidRequest('login');
        }

        $password = $this->getRequestParameter('password', $request);
        if (is_null($password)) {
            throw OAuthServerException::invalidRequest('password');
        }

        try {
            $user = $this->authService->retrieveByCredentials(['login' => $login]);
        } catch (Throwable $e) {
            report($e);

            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        if (! $user instanceof UserEntityInterface) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        if (! $this->authService->validateCredentials($user, $password)) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        return $user;
    }
}

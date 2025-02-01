<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Support\Resources\Responses\ApiResponse;
use App\Support\Resources\Responses\ErrorResource;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Arr;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected $levels = [
        //
    ];
    protected $dontReport = [
        OAuthServerException::class,
    ];

    public function report(Throwable $e): void
    {
        parent::report($e);

        // Do not call sentry, if custom report() method is defined
//        if (! method_exists($e, 'report')) {
//            $this->reportToSentry($e);
//        }
    }

    public function render($request, Throwable $e): FoundationResponse
    {
        if ($e instanceof TokenMismatchException) {
            if ($request->expectsJson()) {
                return $this->prepareJsonResponse($request, $e);
            }
        }

        return parent::render($request, $e);
    }

    protected function convertExceptionToArray(Throwable $e): array
    {
        return config('app.debug') ? [
            'message'   => ! empty($e->getMessage()) ? $e->getMessage() : 'Server Error',
            'errors'    => [
                'general' => [
                    ! empty($e->getMessage()) ? $e->getMessage() : 'Server Error',
                ],
            ],
            'exception' => $e::class,
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'trace'     => collect($e->getTrace())->map(static function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
        ] : [
            'message' => $this->isHttpException($e) && ! empty($e->getMessage()) ? $e->getMessage() : 'Server Error',
            'errors'  => [
                'general' => [
                    $this->isHttpException($e) && ! empty($e->getMessage()) ? $e->getMessage() : 'Server Error',
                ],
            ],
        ];
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     */
    protected function unauthenticated($request, AuthenticationException $exception): FoundationResponse
    {
        if ($request->expectsJson()) {
            return ApiResponse::resource('error', [$exception->getMessage()], ErrorResource::class)
                ->response()
                ->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        return redirect()->guest('/');
    }
}

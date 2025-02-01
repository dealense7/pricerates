<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Support\Resources\Responses\ApiResponse;
use App\Support\Resources\Responses\ErrorResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use League\OAuth2\Server\Exception\OAuthServerException as LeagueException;

use function app;
use function str_replace;

/**
 * @method \League\OAuth2\Server\Exception\OAuthServerException getPrevious()
 */
class OAuthException extends Exception
{
    public function __construct(string $message, int $code, LeagueException $e)
    {
        parent::__construct($message, $code, $e);
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            $resource = ApiResponse::resource('error', [$this->getTranslatedMessage()], ErrorResource::class);
            if (app()->isDebug()) {
                $resource->appendAdditional([
                    'meta' => [
                        'debug' => [
                            'passport' => [
                                'httpStatusCode' => $this->getPrevious()->getHttpStatusCode(),
                                'errorType'      => $this->getPrevious()->getErrorType(),
                                'hint'           => $this->getPrevious()->getHint(),
                                'message'        => $this->getPrevious()->getMessage(),
                            ],
                        ],
                    ],
                ]);
            }

            return $resource
                ->response()
                ->setStatusCode($this->getPrevious()->getHttpStatusCode());
        }

        return redirect()->back();
    }

    private function getTranslatedMessage(): string
    {
        $msg = str_replace([' ', '.', ','], ['_', '', ''], $this->getMessage());
        $msg = Str::lower($msg);

        return __('oauth.' . $msg);
    }
}

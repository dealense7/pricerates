<?php

declare(strict_types=1);

namespace App\Support\Helpers\Auth;

use App\Helpers\SignatureHelper;
use App\Support\Helpers\Helper;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;

class ConnectionHelper
{
    /**
     * @param string $url
     * @param array $fields
     * @param string $method
     * @param string $requestOptions
     * @param array|null $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function send(
        string $url,
        array $fields,
        string $method,
        string $requestOptions = 'JSON',
        ?array $headers = null,
    ): ResponseInterface {
        static $client = null;
        if (is_null($client)) {
            $client = new Client([
                'timeout' => config('custom.constants.vendors_guzzle_timeout'),
            ]);
        }
        $result = $client->$method($url, array_merge([
            constant(sprintf('%s::%s', RequestOptions::class, $requestOptions)) => $fields,
        ], ! is_null($headers) ? [
            RequestOptions::HEADERS => $headers,
        ] : []));

        return $result;
    }

    /**
     * @param string $url
     * @param array $fields
     * @param string $method
     * @param string $requestOptions
     * @param array|null $headers
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \App\Exceptions\Signature\InvalidPrivateKeyException
     * @throws \Illuminate\Validation\ValidationException
     * @throws \App\Exceptions\Signature\InvalidPublicKeyException
     */
    public static function sendWithSignature(
        string $url,
        array $fields,
        string $method,
        string $requestOptions = 'JSON',
        ?array $headers = null,
    ): ResponseInterface {
        Arr::set($headers, 'signature', SignatureHelper::getSignature($fields));
        Arr::set($headers, 'X-Requested-With', 'XMLHttpRequest');
        static $client = null;
        if (is_null($client)) {
            $client = new Client([
                'timeout' => config('custom.constants.vendors_guzzle_timeout'),
            ]);
        }
        $result = $client->$method($url, array_merge([
            constant(sprintf('%s::%s', RequestOptions::class, $requestOptions)) => $fields,
        ], ! is_null($headers) ? [
            RequestOptions::HEADERS => $headers,
        ] : []));

        $data = json_decode($result->getBody(), true);
        if ($result->getHeader('signature')) {
            SignatureHelper::verifySignature($data, $result->getHeader('signature')[0]);
        }

        return $result;
    }

    /**
     * @param string $errorMessage
     * @param int $statusCode
     * @param string|null $fieldName
     * @return void
     *
     * @throws \Exception|\Illuminate\Http\Exceptions\HttpResponseException
     */
    public static function exception(string $errorMessage, int $statusCode, ?string $fieldName = null): void
    {
        $decoded = json_decode($errorMessage, true);
        if ($statusCode === Response::HTTP_UNPROCESSABLE_ENTITY) {
            $errors = self::generateErrors(Arr::get($decoded, 'errors', []), $fieldName);
            throw new HttpResponseException(Helper::response(false, null, $statusCode, $errors));
        }
        throw new Exception(Arr::get($decoded, 'message', $errorMessage), $statusCode);
    }

    /**
     * @param array $errors
     * @param string|null $fieldName
     * @return array
     */
    protected static function generateErrors(array $errors, ?string $fieldName = null): array
    {
        $result = [];
        foreach ($errors as $key => $error) {
            $type = config('custom.errors.types.validation');
            $result[] = Helper::generateErrorMessage($error, $type, $key);
        }

        return $result;
    }
}

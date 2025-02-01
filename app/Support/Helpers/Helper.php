<?php

declare(strict_types=1);

namespace App\Support\Helpers;

use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class Helper
{
    public const UUID_VALID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    /**
     * Generic response wrapper
     *
     * @param  bool  $result
     * @param  array  $data
     * @param  int  $code
     * @param  array  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public static function response(
        bool $result = true,
        ?array $data = null,
        int $code = 200,
        array $errors = [],
    ): JsonResponse {
        return response()->json(array_merge(
            count($errors) === 0 ? ['data' => is_null($data) ? ['result' => $result] : $data] : [],
            count($errors) > 0 ? ['errors' => $errors] : [],
        ), $code, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * @param  array  $errors
     * @param  string|null  $type
     * @param  string|null  $field
     * @return array
     */
    public static function generateErrorMessage(array $errors, ?string $type = null, ?string $field = null): array
    {
        $type = $type ?: config('custom.errors.types.default');

        return [
            'type'     => $type,
            'field'    => $field,
            'messages' => $errors,
        ];
    }

    /**
     * This function returns authenticated user object (for ide, to detect model)
     *
     * @return \App\Models\User\User|null
     */
    public static function user(): ?User
    {
        /** @var \App\Models\User\User|null $user */
        $user = auth()->user();

        return $user;
    }

    public static function getLanguageId(): int
    {
        $languageId   = Language::Georgian->value;
        $languageSlug = strtolower(request()->header('X-Api-Language', ''));

        if (
            in_array(
                $languageSlug,
                [
                    strtolower(Language::Georgian->data()['slug']),
                    strtolower(Language::English->data()['slug']),
                ],
            )
        ) {
            $languageId = Language::getFromSlug($languageSlug)->value;
        }

        return $languageId;
    }

    /**
     * @param  string|null  $number
     * @param  int  $decimals
     * @param  string  $decPoint
     * @param  string  $separator
     * @return string|null
     */
    public static function numberFormat(
        ?string $number = null,
        int $decimals = 2,
        string $decPoint = '.',
        string $separator = '',
    ): ?string {
        return ! is_null($number) ? number_format($number, $decimals, $decPoint, $separator) : null;
    }

    /**
     * This function dumps database queries in non-production environment.
     *
     * @param  bool  $dump
     * @param  bool  $log
     */
    public static function dumpQueries(bool $dump = false, bool $log = false)
    {
        if (! app()->environment('production')) {
            $queries = [];
            try {
                foreach (app()->make('debugbar')->getCollectors()['queries']->collect()['statements'] as $key => $q) {
                    $queries[$key] = Arr::get($q, 'sql');
                }
            } catch (Throwable $e) {
                dd('Wholaaaa, Can\'t detect queries :)) => ' . $e->getMessage());
            }
            if ($dump) {
                dump($queries);
            } elseif ($log) {
                $i = 0;
                foreach ($queries as $query) {
                    ++$i;
                    Log::info(sprintf('%s_%s', $i, $query));
                }
            } else {
                dd($queries);
            }
        }
    }

    /**
     * @param  string  $data
     * @return string|null
     */
    public static function stripTags(?string $data = null): ?string
    {
        return $data ? trim(html_entity_decode(strip_tags($data)), "\t\n\r\0\x0B\xC2\xA0") : '';
    }

    public static function getTmpPath(): string
    {
        $path = sprintf('%s/%s', config('custom.constants.local_path'), Str::uuid()->toString());

        app()->terminating(static function () use ($path) {
            self::deleteFile($path);
        });

        // https://www.php.net/manual/en/function.register-shutdown-function.php
        // Registers a callback to be executed after script execution finishes or exit() is called.
        register_shutdown_function(static function () use ($path) {
            self::deleteFile($path);
        });

        return $path;
    }

    private static function deleteFile(string $path): void
    {
        try {
            if (file_exists($path)) {
                unlink($path);
            }
        } catch (Throwable $th) {
            // Optionally log the error here
        }
    }
}

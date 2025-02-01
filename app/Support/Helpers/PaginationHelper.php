<?php

declare(strict_types=1);

namespace App\Support\Helpers;

class PaginationHelper
{
    /**
     * This function returns per page from request or returns default value
     *
     * @param int $default
     * @param string $requestKey
     * @param int $min
     * @param int $max
     * @return int
     */
    public static function getPerPage(
        int $default = 15,
        string $requestKey = 'per_page',
        int $min = 1,
        int $max = 100,
    ): int {
        $perPage = intval(request()->get($requestKey));

        return $min <= $perPage && $perPage <= $max ? $perPage : $default;
    }

    /**
     * This function removes unneeded keys from paginated array
     *
     * @param array $data
     */
    public static function cleanResult(array &$data)
    {
        unset(
            $data['first_page_url'],
            $data['last_page_url'],
            $data['next_page_url'],
            $data['path'],
            $data['prev_page_url'],
        );
    }
}

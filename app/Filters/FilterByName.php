<?php

declare(strict_types=1);

namespace App\Filters;

use Closure;

class FilterByName
{
    public function handle(array $request, Closure $next): array
    {
        $filter = $request['filter'];
        $query  = $request['query'];
        if (! empty($filter['username']) && is_string($filter['username'])) {
            $query->where('username', 'like', '%' . $filter['username'] . '%');
        }

        return $next($request);
    }
}

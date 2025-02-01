<?php

declare(strict_types=1);

namespace App\Filters\General;

use Closure;

class FilterByCompanyId
{
    public function handle(array $request, Closure $next): array
    {
        $filter = $request['filter'];
        $query  = $request['query'];
        if (! empty($filter['company_id']) && is_int($filter['company_id'])) {
            $query->where('company_id', $filter['company_id']);
        }

        return $next($request);
    }
}

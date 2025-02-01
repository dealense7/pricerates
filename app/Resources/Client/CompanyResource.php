<?php

declare(strict_types=1);

namespace App\Resources\Client;

use App\Models\Client\Company;
use App\Support\Resources\JsonResource;

class CompanyResource extends JsonResource
{
    protected static array $transformMapping = [
        'name'         => 'name',
        'display_name' => 'displayName',
        'created_at'   => 'createdAt',
        'updated_at'   => 'updatedAt',
    ];

    public function __construct(?Company $resource)
    {
        $this->resource = $resource;
    }
}

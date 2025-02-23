<?php

declare(strict_types=1);

namespace App\Resources\Currency;

use App\Models\Currency\Provider;
use App\Resources\User\ContactInformationResource;
use App\Support\Resources\JsonResource;
use App\Support\Resources\JsonResourceCollection;

class ProviderResource extends JsonResource
{
    protected static array $transformMapping = [
        'title'    => 'title',
        'name'     => 'name',
        'status'   => 'status',
        'logo_url' => 'logoUrl',
    ];

    public function __construct(?Provider $resource)
    {
        $this->resource = $resource;
    }

    public function includeContactInformation(): JsonResourceCollection
    {
        return new JsonResourceCollection($this->whenLoaded('contactInformation'), ContactInformationResource::class);
    }
}

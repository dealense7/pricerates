<?php

declare(strict_types=1);

namespace App\Resources\User;

use App\Models\User\ContactInformation;
use App\Support\Resources\JsonResource;

class ContactInformationResource extends JsonResource
{
    protected static array $transformMapping = [
        'type'       => 'type',
        'typeToText' => 'typeToText',
        'data'       => 'data',
        'is_default' => 'isDefault',
    ];

    public function __construct(?ContactInformation $resource)
    {
        $this->resource = $resource;
    }
}

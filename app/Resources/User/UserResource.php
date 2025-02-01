<?php

declare(strict_types=1);

namespace App\Resources\User;

use App\Models\User\User;
use App\Support\Resources\JsonResource;
use App\Support\Resources\JsonResourceCollection;

class UserResource extends JsonResource
{
    protected static array $transformMapping = [
        'username'            => 'username',
        'first_name'          => 'firstName',
        'last_name'           => 'lastName',
        'email'               => 'email',
        'email_verified_at'   => 'emailVerifiedAt',
        'deactivated_at'      => 'deactivatedAt',
        'deactivation_reason' => 'deactivationReason',
        'created_at'          => 'createdAt',
        'updated_at'          => 'updatedAt',
    ];

    protected static array $hideInOutput = [
        'email_verified_at',
        'status_id',
        'client_id',
    ];

    public function __construct(?User $resource)
    {
        $this->resource = $resource;
    }

    public function includeContactInformation(): JsonResourceCollection
    {
        return new JsonResourceCollection($this->whenLoaded('contactInformation'), ContactInformationResource::class);
    }
}

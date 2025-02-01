<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class Policy
{
    use HandlesAuthorization;

    protected function denyWithMessage(string $permissionName): Response
    {
        return Response::deny('You have no permission for this action | ' . $permissionName, 403);
    }

    protected function denyWithCustomMessage(string $message): Response
    {
        return Response::deny($message, 403);
    }
}

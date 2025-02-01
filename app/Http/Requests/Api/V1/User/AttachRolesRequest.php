<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\User;

use App\Contracts\Requests\User\AttachRolesRequestContract;
use App\Http\Requests\Api\V1\Request;

class AttachRolesRequest extends Request implements AttachRolesRequestContract
{
    public function rules(): array
    {
        return [
            'roles'   => ['required', 'array'],
            'roles.*' => ['required', 'integer', 'exists:roles,id'],
        ];
    }
}

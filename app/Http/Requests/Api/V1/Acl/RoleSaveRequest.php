<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Acl;

use App\Contracts\Requests\Acl\RoleSaveRequestContract;
use App\Http\Requests\Api\V1\Request;

class RoleSaveRequest extends Request implements RoleSaveRequestContract
{
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255', 'unique:roles'],
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['required', 'integer', 'exists:permissions,id'],
        ];
    }
}

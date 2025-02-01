<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\User;

use App\Contracts\Requests\User\AttachPermissionsRequestContract;
use App\Http\Requests\Api\V1\Request;

class AttachPermissionsRequest extends Request implements AttachPermissionsRequestContract
{
    public function rules(): array
    {
        return [
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['nullable', 'integer', 'exists:permissions,id'],
        ];
    }
}

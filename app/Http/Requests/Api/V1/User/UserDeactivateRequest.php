<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\User;

use App\Contracts\Requests\User\UserDeactivateRequestContract;
use App\Http\Requests\Api\V1\Request;

class UserDeactivateRequest extends Request implements UserDeactivateRequestContract
{
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string'],
        ];
    }
}

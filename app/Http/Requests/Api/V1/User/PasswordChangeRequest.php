<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\User;

use App\Contracts\Requests\User\PasswordChangeRequestContract;
use App\Http\Requests\Api\V1\Request;

class PasswordChangeRequest extends Request implements PasswordChangeRequestContract
{
    public function rules(): array
    {
        return [
            'currentPassword' => ['required', 'string'],
            'newPassword'     => ['required', 'string', 'min:8', 'different:currentPassword'],
            'confirmPassword' => ['required', 'string', 'same:newPassword'],
        ];
    }
}

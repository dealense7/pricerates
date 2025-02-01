<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\User;

use App\Contracts\Requests\User\CreateSaveRequestContract;
use App\Http\Requests\Api\V1\Request;

class CreateSaveRequest extends Request implements CreateSaveRequestContract
{
    public function rules(): array
    {
        return [
            'username'    => ['required', 'string', 'max:255'],
            'firstName'   => ['required', 'string', 'max:255'],
            'lastName'    => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255'],
            'phoneNumber' => ['required', 'string', 'max:50'],
        ];
    }
}

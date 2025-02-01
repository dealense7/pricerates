<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\User;

use App\Contracts\Requests\User\UpdateSaveRequestContract;
use App\Http\Requests\Api\V1\Request;

class UpdateSaveRequest extends Request implements UpdateSaveRequestContract
{
    public function rules(): array
    {
        return [
            'username'  => ['required', 'string', 'max:255', 'unique:users,username,' . $this->route('id')],
            'firstName' => ['required', 'string', 'max:255'],
            'lastName'  => ['required', 'string', 'max:255'],
        ];
    }
}

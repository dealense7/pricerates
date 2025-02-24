<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemsPageIndex extends FormRequest
{
    public function rules(): array
    {
        return [
            'filters.categoryId' => ['nullable', 'integer'],
        ];
    }
}

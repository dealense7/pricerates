<?php

declare(strict_types=1);

namespace Database\Factories\Client;

use App\Models\Client\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = $this->faker->company;

        return [
            'name'         => $name,
            'display_name' => 'შპს ' . $name,
        ];
    }
}

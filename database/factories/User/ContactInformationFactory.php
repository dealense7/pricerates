<?php

declare(strict_types=1);

namespace Database\Factories\User;

use App\Enums\User\ContactType;
use App\Models\User\ContactInformation;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactInformationFactory extends Factory
{
    protected $model = ContactInformation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->first() ?? User::factory(),
            'type'    => $this->faker->randomElement(ContactType::toArray()),
            'data'    => $this->faker->phoneNumber(),
        ];
    }
}

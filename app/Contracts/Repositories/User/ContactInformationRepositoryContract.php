<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\User;

use App\Models\User\ContactInformation;

interface ContactInformationRepositoryContract
{
    public function store(array $data): ContactInformation;

    public function update(ContactInformation $item, array $data): ContactInformation;

    public function delete(ContactInformation $item): void;
}

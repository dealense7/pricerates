<?php

declare(strict_types=1);

namespace App\Repositories\V1\User;

use App\Contracts\Repositories\User\ContactInformationRepositoryContract;
use App\Models\User\ContactInformation;
use App\Repositories\Repository;

class ContactInformationRepository extends Repository implements ContactInformationRepositoryContract
{
    public function update(ContactInformation $item, array $data): ContactInformation
    {
        $item->fill($data);

        $item->saveOrFail();

        return $item;
    }

    public function store(array $data): ContactInformation
    {
        return $this->update($this->getModel(), $data);
    }

    public function delete(ContactInformation $item): void
    {
        $item->delete();
    }

    public function getModel(): ContactInformation
    {
        return new ContactInformation();
    }
}

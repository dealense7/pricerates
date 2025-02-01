<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User\User;
use Illuminate\Auth\Access\Response;

class UserPolicy extends Policy
{
    public function read(User $user, User $item): Response
    {
        if ($user->can($item->getPermission('read'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('read'));
    }

    public function create(User $user, User $item): Response
    {
        if ($user->can($item->getPermission('create'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('create'));
    }

    public function update(User $user, User $item): Response
    {
        if ($user->can($item->getPermission('update'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('update'));
    }

    public function deactivate(User $user, User $item): Response
    {
        if ($user->getId() === $item->getId()) {
            return $this->denyWithCustomMessage('Do me a favour please, gerara here.');
        }

        if ($item->getDeactivatedAt() !== null) {
            return $this->denyWithCustomMessage('Account is already deactivated');
        }

        if ($user->can($item->getPermission('deactivate'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('deactivate'));
    }

    public function activate(User $user, User $item): Response
    {
        if ($user->getId() === $item->getId()) {
            return $this->denyWithCustomMessage('What are you doing?');
        }

        if ($item->getDeactivatedAt() === null) {
            return $this->denyWithCustomMessage('Account is already activated');
        }

        if ($user->can($item->getPermission('activate'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('activate'));
    }

    public function delete(User $user, User $item): Response
    {
        if ($user->getId() === $item->getId()) {
            return $this->denyWithCustomMessage('only chuck norris can delete himself');
        }

        if ($user->can($item->getPermission('delete'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('delete'));
    }

    public function restore(User $user, User $item): Response
    {
        if ($user->can($item->getPermission('restore'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('restore'));
    }
}

<?php

declare(strict_types=1);

namespace App\Services\V1\User;

use App\Contracts\Repositories\User\ContactInformationRepositoryContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Contracts\Services\User\UserServiceContract;
use App\Enums\User\ContactType;
use App\Exceptions\ItemNotFoundException;
use App\Models\User\User;
use App\Resources\User\UserResource;
use App\Services\Service;
use App\Support\Collection;
use App\Support\Helpers\Helper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService extends Service implements UserServiceContract
{
    public function __construct(
        protected UserRepositoryContract $repository,
        protected ContactInformationRepositoryContract $contactInformationRepository,
    ) {
        //
    }

    public function findItems(
        array $filters = [],
        array $relations = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator {
        $this->authorize('read', new User());

        return $this->repository->findItems($filters, $relations, $page, $perPage, $sort);
    }

    public function findById(int $id, array $relations = []): ?User
    {
        $filters = [];

        $item = $this->repository->findById($id, $relations, $filters);

        if (empty($item)) {
            return null;
        }

        $this->authorize('read', $item);

        return $item;
    }

    public function findRemovedItemByIdOrFail(int $id): User
    {
        $item = $this->repository->findRemovedItemByIdOrFail($id);

        if (! $item) {
            throw new ItemNotFoundException();
        }

        $this->authorize('read', $item);

        return $item;
    }

    public function store(array $data, array $relations = []): User
    {
        $this->authorize('create', (new User()));

        return DB::transaction(function () use ($data, $relations) {
            $passwordPlainText = Str::password();

            $phoneNumber = $data['phoneNumber'];

            $data             = UserResource::transformToInternal($data);

            $data['password'] = Hash::make($passwordPlainText);

            $user = $this->repository->store($data, $relations);

            // Phone Data
            $contactData             = [
                'user_id'    => $user->getId(),
                'type'       => ContactType::PHONE,
                'data'       => $phoneNumber,
                'is_default' => true,
            ];
            $phoneContactInformation = $this->contactInformationRepository->store($contactData);

            // EmailData
            $contactData['type'] = ContactType::EMAIL;
            $contactData['data'] = $data['email'];

            $emailContactInformation = $this->contactInformationRepository->store($contactData);
            $user->setRelation('contactInformation', [$phoneContactInformation, $emailContactInformation]);

            return $user;
        });
    }

    public function deactivate(User $user, string $reason): User
    {
        $this->authorize('deactivate', $user);

        return $this->repository->update($user, [
            'deactivated_at'      => now(),
            'deactivation_reason' => $reason,
        ]);
    }

    public function activate(User $user): User
    {
        $this->authorize('activate', $user);

        return $this->repository->update($user, [
            'deactivated_at'      => null,
            'deactivation_reason' => null,
        ]);
    }

    public function delete(User $user): bool
    {
        $this->authorize('delete', $user);

        $this->repository->delete($user);

        return true;
    }

    public function restore(User $user): User
    {
        $this->authorize('restore', $user);

        return $this->repository->restore($user);
    }

    public function getAcl(User $user): array
    {
        $permissions = $user->getAllPermissions();
        $roles       = $user->roles;

        return [
            'permissions' => $permissions,
            'roles'       => $roles,
        ];
    }

    public function attachPermissions(array $permissions, User $user): SupportCollection
    {
        $this->authorize('update', $user);

        $user = $this->repository->attachPermissions($permissions, $user);

        return $user->getAllPermissions();
    }

    public function attachRoles(array $roles, User $user): Collection
    {
        $this->authorize('update', $user);

        $user = $this->repository->attachRoles($roles, $user);

        return $user->roles;
    }

    public function passwordUpdate(array $data): bool
    {
        $user = Helper::user();

        if (! Hash::check($data['currentPassword'], $user->password)) {
            throw new HttpClientException('Current Password is wrong', 403);
        }

        $newPassword = $data['newPassword'];

        $this->repository->forceFill($user, ['password' => Hash::make($newPassword),]);

        return true;
    }
}

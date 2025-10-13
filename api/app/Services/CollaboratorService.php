<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\CollaboratorRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class CollaboratorService extends BaseService
{
    /**
     * UserService constructor.
     */
    public function __construct(
        private CollaboratorRepository $collaboratorRepository,
        private UserService $userService,
    ) {}

    /**
     * Register new collaborator
     */
    public function store(array $data): ?User
    {
        $data['cpf'] = $this->onlyNumbers(data_get($data, 'cpf'));

        $user = match ($data['type'] ?? null) {
            'manager' => $this->createCollaboratorManagerUser($data),
            default => $this->createCollaboratorStaffUser($data),
        };

        $user->createAccessToken();

        return $user;
    }

    /**
     * Get all collaborators
     */
    public function index(User $user, array $filters): ?Collection
    {
        $filters = ! empty($filters) ? $this->prepareFilters($filters) : [];

        return $this->collaboratorRepository->getStaffByManagerWithFilters($user->manager?->id, $filters);
    }

    /**
     * Update collaborator
     */
    public function update(array $data, User $collaboratorUser, User $loggedUser): ?User
    {
        // TODO; check
        if (isset($data['cpf'])) {
            $data['cpf'] = $this->onlyNumbers(data_get($data, 'cpf'));
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $isUpdatingOwnRegister = $loggedUser->id === $collaboratorUser->id;

        if ($isUpdatingOwnRegister) {
            $this->collaboratorRepository->update($loggedUser->id, $data);
        }

        if (! $isUpdatingOwnRegister) {
            $this->collaboratorRepository->updateStaffByManagerId($loggedUser->manager?->id, $data, $collaboratorUser->id);
        }

        return $collaboratorUser->refresh();
    }

    /**
     * Delete a collaborator
     */
    public function destroy(User $user)
    {
        $user->delete();
    }

    /**
     * Prepare filters
     */
    private function prepareFilters(array $filters): array
    {
        $where = [];

        foreach ($filters as $key => $value) {
            if ($key == 'cpf') {
                $value = $this->onlyNumbers($value);
            }
            $where[] = [$key, 'REGEXP', $value];
        }

        return $where;
    }

    /**
     * Create User and Staff
     */
    private function createCollaboratorStaffUser(array $data): User
    {
        $user = $this->userService->createUser($data);

        $user->staff()->create([
            'manager_id' => $data['manager_id'],
        ]);

        return $user;
    }

    /**
     * Create User and Manager
     */
    private function createCollaboratorManagerUser(array $data): User
    {
        $user = $this->userService->createUser($data);

        $user->manager([
            'user_id' => $user->id,
        ])->create();

        return $user;
    }
}

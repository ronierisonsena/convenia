<?php

namespace App\Services;

use App\Models\Manager;
use App\Models\Staff;
use App\Models\User;
use App\Repositories\CollaboratorRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserTypeRepository;
use Illuminate\Support\Collection;

class CollaboratorService extends BaseService
{
    /**
     * UserService constructor.
     */
    public function __construct(
        private UserRepository $userRepository,
        private CollaboratorRepository $collaboratorRepository,
        private UserTypeRepository $userTypeRepository,
        private UserService $userService,
    ) {}

    /**
     * Register new Staff
     */
    public function store(array $data): ?User
    {
        $data['cpf'] = $this->onlyNumbers($data['cpf']);

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
     * Prepare filters
     * 
     * @return array
     */
    public function prepareFilters(array $filters): array
    {
        $where = [];

        foreach ($filters as $key => $value) {
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

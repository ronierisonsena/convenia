<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

class CollaboratorRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected User $model,
    ) {}

    public function getStaffByManagerWithFilters(int $managerId, array $filters = []): Collection
    {
        return $this->model
            ->where($filters)
            ->join('staff', 'staff.user_id', '=', 'users.id')
            ->where('staff.manager_id', $managerId)
            ->get(['users.*', 'staff.manager_id', 'staff.user_id']);
    }

    public function updateStaffByManagerId(int $managerId, array $data, int $collaboratorUserId): void
    {
        $this->model
            ->join('staff', 'staff.user_id', '=', 'users.id')
            ->where('staff.manager_id', $managerId)
            ->where('users.id', $collaboratorUserId)
            ->update($data);
    }
}

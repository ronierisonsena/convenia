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

    /**
     * @return mixed
     */
    public function getStaffByManagerWithFilters(int $managerId, array $filters = []): Collection
    {
        return $this->model
            ->where($filters)
            ->join('staff', 'staff.user_id', '=', 'users.id')
            ->where('staff.manager_id', $managerId)
            ->get();
    }
}

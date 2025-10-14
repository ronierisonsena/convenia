<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    /**
     * UserRepository constructor.
     */
    public function __construct(
        protected User $model,
    ) {}

    public function getCollaboratorsIdsByEmail(array $emails)
    {
        return $this->model->whereIn('email', $emails)->pluck('id')->toArray();
    }
}

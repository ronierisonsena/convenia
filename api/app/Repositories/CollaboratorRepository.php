<?php

namespace App\Repositories;

use App\Models\User;

class CollaboratorRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private User $model,
    ) {}
}

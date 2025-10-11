<?php

namespace App\Repositories;

use App\Models\UserType;

class UserTypeRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public UserType $model,
    ) {}
}

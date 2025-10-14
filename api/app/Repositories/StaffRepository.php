<?php

namespace App\Repositories;

use App\Models\Staff;

class StaffRepository extends BaseRepository
{
    /**
     * UserRepository constructor.
     */
    public function __construct(
        protected Staff $model,
    ) {}
}

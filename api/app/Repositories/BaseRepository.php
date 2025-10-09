<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }
}

<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function getByAttribute(string $attribute, $value, $operator = '='): mixed
    {
        return $this->model->where($attribute, $operator, $value)->get();
    }

    public function findBy(string $attribute, $value): Model
    {
        return $this->model->where($attribute, $value)->firstOrFail();
    }
}

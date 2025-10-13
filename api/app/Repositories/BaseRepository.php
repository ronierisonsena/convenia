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

    public function update(string $id, array $data): Model
    {
        $model = $this->getById($id);
        $model->update($data);

        return $model->refresh();
    }

    public function getById(string $id): ?Model
    {
        return $this->model->where('id', $id)->first();
    }
}

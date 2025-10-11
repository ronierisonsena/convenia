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

    public function getByAttributes(array $attributesValues, $operator = '='): mixed
    {
        $where = [];

        foreach ($attributesValues as $key => $arrayAttributeValue) {
            $operator = count($arrayAttributeValue) == 3 ? $arrayAttributeValue[1] : $operator;
            $where[] = [$arrayAttributeValue[0], $operator, $arrayAttributeValue[2]];
        }

        return $this->model->where($where)->get();
    }
}

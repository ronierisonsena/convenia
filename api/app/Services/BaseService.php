<?php

namespace App\Services;

abstract class BaseService
{
    /**
     * Return only numbers in string
     */
    public function onlyNumbers(?string $string): ?string
    {
        return preg_replace('/\D/', '', $string);
    }
}

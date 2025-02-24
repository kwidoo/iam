<?php

namespace App\Contracts\Models;

interface UserWriteModel
{

    /**
     * @param array $attributes
     *
     * @return bool
     */
    public static function create($attributes = []);

    /**
     * @param string $uuid
     *
     * @return mixed
     */
    public static function find(string $uuid);
}

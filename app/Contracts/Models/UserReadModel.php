<?php

namespace App\Contracts\Models;

interface UserReadModel
{

    /**
     * @param array $attributes
     *
     * @return self
     */
    public function fill(array $attributes);

    /**
     * @return bool
     */
    public function save(array $options = []);
}

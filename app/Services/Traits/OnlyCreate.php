<?php

namespace App\Services\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

trait OnlyCreate
{

    public function list(Data $query)
    {
        throw new Exception('Not implemented');
    }

    public function getById(Data $query): Model
    {
        throw new Exception('Not implemented');
    }

    public function update(string $id, array $data): mixed
    {
        throw new Exception('Not implemented');
    }

    public function delete(string $id): bool
    {
        throw new Exception('Not implemented');
    }

    public function restore(string $id): bool
    {
        throw new Exception('Not implemented');
    }
}

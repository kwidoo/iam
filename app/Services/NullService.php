<?php

namespace App\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\Mere\Data\ListQueryData;
use Kwidoo\Mere\Data\ShowQueryData;
use Kwidoo\Mere\Services\BaseService;

class NullService extends BaseService
{
    /**
     * @return string
     */
    public function eventKey(): string
    {
        return 'no_service';
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function create(array $data): string
    {
        throw new Exception('Not implemented');
    }

    /** DISABLED */
    public function list(ListQueryData $query)
    {
        throw new Exception('Not implemented');
    }

    public function getById(ShowQueryData $query): Model
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
}

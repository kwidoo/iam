<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;


class ByContact implements CriteriaInterface
{
    public function __construct(
        protected string $value,
        protected string $type,
    ) {}
    /**
     * Apply criteria in query repository
     *
     * @param App\Models\Organization $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model
            ->where('value', $this->value)
            ->where('type', $this->type);
    }
}

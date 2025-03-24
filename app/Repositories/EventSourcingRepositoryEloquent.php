<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\Repositories\EventSourcingRepository;
use App\Validators\EventSourcingValidator;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

/**
 * Class EventSourcingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class EventSourcingRepositoryEloquent extends BaseRepository implements EventSourcingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return EloquentStoredEvent::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}

<?php

namespace App\Services;

use App\Contracts\Services\EventSourcingService as EventSourcingServiceContract;
use App\Contracts\Repositories\EventSourcingRepository;
use Prettus\Repository\Contracts\RepositoryInterface;

class EventSourcingService implements EventSourcingServiceContract
{
    protected RepositoryInterface $repository;

    public function __construct(EventSourcingRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function eventKey(): string
    {
        return 'event';
    }

    public function recordEvent(array $eventData) {}
    public function retrieveEvents(array $criteria = []) {}
    public function replayEvents() {}
}

<?php

namespace App\Contracts\Services;

interface EventSourcingService
{
    public function recordEvent(array $eventData);
    public function retrieveEvents(array $criteria = []);
    public function replayEvents();
}

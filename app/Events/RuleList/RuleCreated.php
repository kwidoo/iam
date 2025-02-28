<?php

namespace App\Events\RuleList;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class RuleCreated extends ShouldBeStored
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param array<string,mixed> $data
     */
    public function __construct(public array $data)
    {
        //
    }
}

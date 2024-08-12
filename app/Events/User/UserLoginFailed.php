<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserLoginFailed extends ShouldBeStored
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param User|null $user
     * @param array<string,mixed> $data
     */
    public function __construct(
        public ?User $user,
        public array $data = [],
    ) {
        //
    }
}
